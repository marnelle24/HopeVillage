<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;

class SingpassController extends Controller
{
    /**
     * Base64url encode (RFC 4648) without padding.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Public client JWKS endpoint. Singpass calls this URL (PX-E0101) to fetch our public keys.
     * Register this URL in the Singpass Developer Portal as "Client JWKS URL".
     * Set SINGPASS_CLIENT_JWKS_JSON in .env (run: php artisan singpass:generate-jwks).
     */
    public function jwks()
    {
        $json = config('services.singpass.client_jwks_json');
        if (empty($json)) {
            return response()->json([
                'error' => 'SINGPASS_CLIENT_JWKS_JSON not configured. Run: php artisan singpass:generate-jwks',
            ], 404)->header('Content-Type', 'application/json');
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded) || !isset($decoded['keys']) || !is_array($decoded['keys'])) {
            return response()->json(['error' => 'Invalid client JWKS format.'], 500)->header('Content-Type', 'application/json');
        }
        return response()->json($decoded)->header('Content-Type', 'application/json');
    }

    /**
     * Redirect user to Singpass authorization endpoint
     */
    public function redirect()
    {
        $state = Str::random(40);
        $nonce = Str::random(40);

        // PKCE (S256): generate code_verifier and derived code_challenge.
        // code_verifier must be 43-128 chars; base64url(random_bytes(64)) yields ~86 chars.
        $codeVerifier = $this->base64UrlEncode(random_bytes(64));
        $codeChallenge = $this->base64UrlEncode(hash('sha256', $codeVerifier, true));
        
        session([
            'singpass_state' => $state,
            'singpass_nonce' => $nonce,
            'singpass_pkce_verifier' => $codeVerifier,
        ]);

        $params = [
            'client_id' => config('services.singpass.client_id'),
            'redirect_uri' => config('services.singpass.redirect'),
            'response_type' => 'code',
            'scope' => 'openid',
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        $authUrl = config('services.singpass.authorization_endpoint') . '?' . http_build_query($params);

        // Temporarily add this before the redirect:
        \Log::info('Singpass Auth URL', [
            'url' => $authUrl,
            'client_id' => config('services.singpass.client_id'),
            'redirect_uri' => config('services.singpass.redirect'),
        ]);

        return redirect($authUrl);
    }

    /**
     * Handle Singpass callback
     */
    public function callback(Request $request)
    {
        // Verify state parameter
        $state = $request->query('state');
        if ($state !== session('singpass_state')) {
            return redirect()->route('login')->withErrors(['singpass' => 'Invalid state parameter.']);
        }

        // Check for error
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors(['singpass' => $request->query('error_description', 'Authentication failed.')]);
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('login')->withErrors(['singpass' => 'Authorization code not received.']);
        }

        // Exchange authorization code for tokens
        try {
            $tokenResponse = $this->exchangeCodeForTokens($code);
            
            if (!isset($tokenResponse['id_token'])) {
                return redirect()->route('login')->withErrors(['singpass' => 'Failed to retrieve ID token.']);
            }

            // Verify and decode ID token
            $idToken = $this->verifyAndDecodeIdToken($tokenResponse['id_token']);
            
            if (!$idToken) {
                return redirect()->route('login')->withErrors(['singpass' => 'Invalid ID token.']);
            }

            // Verify nonce
            if (isset($idToken['nonce']) && $idToken['nonce'] !== session('singpass_nonce')) {
                return redirect()->route('login')->withErrors(['singpass' => 'Invalid nonce.']);
            }

            // Get or create user
            $user = $this->getOrCreateUser($idToken);

            // Login user
            Auth::login($user, true);

            // Clear session data
            session()->forget(['singpass_state', 'singpass_nonce', 'singpass_pkce_verifier']);

            // Redirect based on user type
            if ($user->isMember()) {
                return redirect()->route('member.events');
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Singpass callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->withErrors(['singpass' => 'Authentication failed. Please try again.']);
        }
    }

    /**
     * Exchange authorization code for tokens
     */
    private function exchangeCodeForTokens(string $code): array
    {
        $codeVerifier = session('singpass_pkce_verifier');
        if (!$codeVerifier) {
            throw new \Exception('Missing PKCE code_verifier in session.');
        }

        $response = Http::asForm()->post(config('services.singpass.token_endpoint'), [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.singpass.redirect'),
            'client_id' => config('services.singpass.client_id'),
            'client_secret' => config('services.singpass.client_secret'),
            'code_verifier' => $codeVerifier,
        ]);

        if (!$response->successful()) {
            Log::error('Singpass token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Token exchange failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get jwks_uri from OpenID discovery (.well-known/openid-configuration).
     */
    private function getJwksUriFromDiscovery(): ?string
    {
        $parsed = parse_url(config('services.singpass.authorization_endpoint'));
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $baseUrl = rtrim("{$scheme}://{$host}", '/');
        $discoveryUrl = $baseUrl . '/.well-known/openid-configuration';

        $response = Http::get($discoveryUrl);
        if (!$response->successful()) {
            Log::warning('Singpass OpenID discovery failed', [
                'url' => $discoveryUrl,
                'status' => $response->status(),
            ]);
            return null;
        }

        $json = json_decode($response->body(), true);
        if (!is_array($json) || empty($json['jwks_uri'])) {
            Log::warning('Singpass discovery response missing jwks_uri', [
                'url' => $discoveryUrl,
            ]);
            return null;
        }

        return $json['jwks_uri'];
    }

    /**
     * Fetch and validate JWKS JSON from Singpass. Tries config jwks_uri first, then discovery.
     * Returns the raw JWKS JSON string for JWKSet::createFromJson(), or null if invalid.
     */
    private function fetchAndValidateJwks(): ?string
    {
        $triedUris = [];
        $jwksUri = config('services.singpass.jwks_uri');

        for ($attempt = 0; $attempt < 2; $attempt++) {
            if (empty($jwksUri)) {
                $jwksUri = $this->getJwksUriFromDiscovery();
            }
            if (empty($jwksUri)) {
                break;
            }

            $triedUris[] = $jwksUri;
            $response = Http::get($jwksUri);

            if (!$response->successful()) {
                Log::error('Failed to fetch JWKS from Singpass', [
                    'jwks_uri' => $jwksUri,
                    'status' => $response->status(),
                    'body_preview' => substr($response->body(), 0, 500),
                ]);
                $jwksUri = null;
                continue;
            }

            $body = $response->body();
            $contentType = $response->header('Content-Type');
            if ($contentType && stripos($contentType, 'application/json') === false) {
                Log::error('JWKS endpoint did not return JSON', [
                    'jwks_uri' => $jwksUri,
                    'content_type' => $contentType,
                    'body_preview' => substr($body, 0, 500),
                ]);
                $jwksUri = null;
                continue;
            }

            $decoded = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JWKS response is not valid JSON', [
                    'jwks_uri' => $jwksUri,
                    'json_error' => json_last_error_msg(),
                    'body_preview' => substr($body, 0, 500),
                ]);
                $jwksUri = null;
                continue;
            }

            if (!is_array($decoded) || !isset($decoded['keys']) || !is_array($decoded['keys'])) {
                Log::error('Invalid JWKS object: missing or invalid "keys" array', [
                    'jwks_uri' => $jwksUri,
                    'body_preview' => substr($body, 0, 500),
                ]);
                $jwksUri = null;
                continue;
            }

            return $body;
        }

        Log::error('Could not obtain valid JWKS after trying URIs', ['tried_uris' => $triedUris]);
        return null;
    }

    /**
     * Verify and decode ID token
     */
    private function verifyAndDecodeIdToken(string $idToken): ?array
    {
        try {
            $jwksJson = $this->fetchAndValidateJwks();
            if ($jwksJson === null) {
                return null;
            }

            $jwks = JWKSet::createFromJson($jwksJson);

            // Parse JWT
            $serializer = new CompactSerializer();
            $jws = $serializer->unserialize($idToken);

            // Verify signature
            $algorithmManager = new AlgorithmManager([new ES256()]);
            $jwsVerifier = new JWSVerifier($algorithmManager);
            
            $isValid = false;
            foreach ($jwks as $jwk) {
                try {
                    if ($jwsVerifier->verifyWithKey($jws, $jwk, 0)) {
                        $isValid = true;
                        break;
                    }
                } catch (\Exception $e) {
                    // Continue to next key if verification fails
                    continue;
                }
            }

            if (!$isValid) {
                Log::error('ID token signature verification failed');
                return null;
            }

            // Decode payload
            $payload = json_decode($jws->getPayload(), true);

            if (!$payload) {
                Log::error('Failed to decode ID token payload');
                return null;
            }

            // Verify claims
            $now = time();
            if (isset($payload['exp']) && $payload['exp'] < $now) {
                Log::error('ID token expired', ['exp' => $payload['exp'], 'now' => $now]);
                return null;
            }

            if (isset($payload['iss']) && !str_contains($payload['iss'], 'singpass.gov.sg')) {
                Log::error('Invalid token issuer', ['iss' => $payload['iss']]);
                return null;
            }

            return $payload;

        } catch (\Exception $e) {
            Log::error('ID token verification error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Get or create user from Singpass data
     */
    private function getOrCreateUser(array $idToken): User
    {
        $sub = $idToken['sub'] ?? null;
        $uuid = $idToken['uuid'] ?? null;

        if (!$sub) {
            throw new \Exception('Sub claim not found in ID token');
        }

        // Try to find existing user by Singpass sub
        $user = User::where('singpass_sub', $sub)->first();

        if ($user) {
            // Update last verified time
            $user->update([
                'singpass_verified_at' => now(),
                'singpass_uuid' => $uuid,
            ]);
            return $user;
        }

        // Try to find by UUID if provided
        if ($uuid) {
            $user = User::where('singpass_uuid', $uuid)->first();
            if ($user) {
                $user->update([
                    'singpass_sub' => $sub,
                    'singpass_verified_at' => now(),
                ]);
                return $user;
            }
        }

        // For new users, create a basic user account
        // Note: You may need to adjust this based on your requirements
        $user = User::create([
            'name' => $idToken['name'] ?? ($idToken['given_name'] ?? 'Singpass User'),
            'email' => $idToken['email'] ?? ($sub . '@singpass.temp'),
            'password' => bcrypt(Str::random(32)), // Random password since Singpass handles auth
            'user_type' => 'member', // Default to member for Singpass login
            'singpass_sub' => $sub,
            'singpass_uuid' => $uuid,
            'singpass_verified_at' => now(),
            'email_verified_at' => now(), // Singpass verified users are considered email verified
        ]);

        return $user;
    }
}
