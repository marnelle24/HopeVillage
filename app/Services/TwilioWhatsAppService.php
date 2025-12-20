<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    public function enabled(): bool
    {
        $hasAccount = (bool) config('services.twilio.account_sid');
        $hasFrom = (bool) config('services.twilio.whatsapp_from');

        $hasAuthToken = (bool) config('services.twilio.auth_token');
        $hasApiKey = (bool) (config('services.twilio.api_key_sid') && config('services.twilio.api_key_secret'));

        return $hasAccount && $hasFrom && ($hasAuthToken || $hasApiKey);
    }

    /**
     * Send a WhatsApp OTP message using Twilio.
     *
     * @return array{ok: bool, to?: string, sid?: string|null, status?: string|null, error?: string}
     */
    public function sendVerificationCode(string $phoneNumber, string $code): array
    {
        if (!$this->enabled()) {
            Log::warning('Twilio WhatsApp not enabled (missing config).', [
                'has_account_sid' => (bool) config('services.twilio.account_sid'),
                'has_auth_token' => (bool) config('services.twilio.auth_token'),
                'has_api_key_sid' => (bool) config('services.twilio.api_key_sid'),
                'has_api_key_secret' => (bool) config('services.twilio.api_key_secret'),
                'has_whatsapp_from' => (bool) config('services.twilio.whatsapp_from'),
            ]);
            return [
                'ok' => false,
                'error' => 'Twilio is not configured. Set TWILIO_ACCOUNT_SID + TWILIO_WHATSAPP_FROM, and either (TWILIO_AUTH_TOKEN) OR (TWILIO_API_KEY_SID + TWILIO_API_KEY_SECRET). If you use config caching, run: php artisan config:clear',
            ];
        }

        $to = $this->normalizeWhatsAppAddress($phoneNumber);
        if (!$to) {
            Log::warning('Twilio WhatsApp invalid destination number format.', [
                'input' => $phoneNumber,
                'hint' => 'Use +E.164 format like +6591234567 (or set TWILIO_DEFAULT_COUNTRY_CODE).',
            ]);
            return [
                'ok' => false,
                'error' => 'Invalid WhatsApp number format. Use +E.164 like +6591234567 (or set TWILIO_DEFAULT_COUNTRY_CODE=65 for Singapore).',
            ];
        }

        $sid = (string) config('services.twilio.account_sid');
        $from = (string) config('services.twilio.whatsapp_from');

        // Auth: prefer API Key if present, otherwise Account SID + Auth Token
        $username = null;
        $password = null;
        $authMode = null;

        $apiKeySid = (string) config('services.twilio.api_key_sid');
        $apiKeySecret = (string) config('services.twilio.api_key_secret');
        if ($apiKeySid !== '' && $apiKeySecret !== '') {
            $username = $apiKeySid;
            $password = $apiKeySecret;
            $authMode = 'api_key';
        } else {
            $token = (string) config('services.twilio.auth_token');
            $username = $sid;
            $password = $token;
            $authMode = 'auth_token';
        }

        $appName = config('app.name', 'HopeVillage');
        $body = "Your {$appName} verification code is {$code}. It expires in 10 minutes.";

        $response = Http::withBasicAuth($username, $password)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $to,
                'Body' => $body,
            ]);

        if (!$response->successful()) {
            $payload = $response->json();
            $twilioMessage = is_array($payload) ? ($payload['message'] ?? null) : null;
            $twilioCode = is_array($payload) ? ($payload['code'] ?? null) : null;

            Log::error('Twilio WhatsApp send failed.', [
                'status' => $response->status(),
                'to' => $to,
                'from' => $from,
                'auth_mode' => $authMode,
                'body' => $payload ?: $response->body(),
            ]);

            $details = $twilioMessage ?: 'Twilio rejected the request.';
            if ($twilioCode) {
                $details .= " (Twilio code: {$twilioCode})";
            }

            return [
                'ok' => false,
                'to' => $to,
                'error' => $details,
            ];
        }

        Log::info('Twilio WhatsApp send accepted.', [
            'to' => $to,
            'from' => $from,
            'auth_mode' => $authMode,
            'sid' => $response->json('sid'),
            'status' => $response->json('status'),
        ]);

        return [
            'ok' => true,
            'to' => $to,
            'sid' => $response->json('sid'),
            'status' => $response->json('status'),
        ];
    }

    /**
     * Validate if a phone number is valid and can potentially receive WhatsApp messages.
     * Uses Twilio Lookup API to validate phone number format.
     * 
     * Note: Twilio doesn't have a direct API to check if a number is registered on WhatsApp
     * without sending a message. This method validates the phone number format using
     * Twilio Lookup API. Actual WhatsApp validation happens when sending messages.
     *
     * @return array{valid: bool, error?: string, formatted?: string}
     */
    public function validatePhoneNumber(string $phoneNumber): array
    {
        if (!$this->enabled()) {
            return [
                'valid' => false,
                'error' => 'Twilio service is not enabled'
            ];
        }

        // Normalize the phone number
        $normalized = $this->normalizeWhatsAppAddress($phoneNumber);
        if (!$normalized) {
            return [
                'valid' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        // Extract E.164 format (remove whatsapp: prefix)
        $e164Number = str_replace('whatsapp:', '', $normalized);

        $sid = (string) config('services.twilio.account_sid');

        // Auth: prefer API Key if present, otherwise Account SID + Auth Token
        $username = null;
        $password = null;

        $apiKeySid = (string) config('services.twilio.api_key_sid');
        $apiKeySecret = (string) config('services.twilio.api_key_secret');
        if ($apiKeySid !== '' && $apiKeySecret !== '') {
            $username = $apiKeySid;
            $password = $apiKeySecret;
        } else {
            $token = (string) config('services.twilio.auth_token');
            $username = $sid;
            $password = $token;
        }

        // Ensure we have credentials before making the request
        if (empty($username) || empty($password)) {
            Log::warning('Twilio Lookup skipped - missing credentials.', [
                'phone' => $phoneNumber,
                'normalized' => $e164Number,
            ]);

            return [
                'valid' => false,
                'error' => 'Twilio credentials are not properly configured'
            ];
        }

        // Use Twilio Lookup API to validate phone number
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($username, $password)
                ->get("https://lookups.twilio.com/v1/PhoneNumbers/{$e164Number}", [
                    'Type' => 'carrier',
                ]);

            if (!$response->successful()) {
                $payload = $response->json();
                $errorMessage = is_array($payload) ? ($payload['message'] ?? 'Invalid phone number') : 'Invalid phone number';
                
                Log::warning('Twilio Lookup failed for phone number validation.', [
                    'phone' => $phoneNumber,
                    'normalized' => $e164Number,
                    'status' => $response->status(),
                    'error' => $errorMessage,
                ]);

                return [
                    'valid' => false,
                    'error' => $errorMessage
                ];
            }

            $data = $response->json();
            
            Log::info('Twilio Lookup successful for phone number.', [
                'phone' => $phoneNumber,
                'normalized' => $e164Number,
                'carrier' => $data['carrier']['name'] ?? 'Unknown',
                'type' => $data['carrier']['type'] ?? 'Unknown',
            ]);

            return [
                'valid' => true,
                'formatted' => $e164Number,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio Lookup exception during phone number validation.', [
                'phone' => $phoneNumber,
                'normalized' => $e164Number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'valid' => false,
                'error' => 'Unable to validate phone number at this time. Please try again later.'
            ];
        }
    }

    /**
     * Check if a phone number is potentially a valid WhatsApp number.
     * This validates the format but cannot confirm WhatsApp registration without sending a message.
     * 
     * @return bool True if the phone number format is valid
     */
    public function isValidWhatsAppNumber(string $phoneNumber): bool
    {
        $result = $this->validatePhoneNumber($phoneNumber);
        return $result['valid'] ?? false;
    }

    /**
     * Normalize into Twilio WhatsApp address: whatsapp:+E164
     */
    private function normalizeWhatsAppAddress(string $phoneNumber): ?string
    {
        $raw = trim($phoneNumber);
        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, 'whatsapp:')) {
            return $raw;
        }

        // Keep + and digits only.
        $normalized = preg_replace('/[^\d\+]/', '', $raw);
        if (!$normalized) {
            return null;
        }

        // If no +, optionally prepend default country code (e.g. 65 for SG).
        if (!str_starts_with($normalized, '+')) {
            $digitsOnly = preg_replace('/\D+/', '', $normalized);
            $cc = (string) config('services.twilio.default_country_code');
            if ($cc !== '' && $digitsOnly !== '') {
                $normalized = '+' . preg_replace('/\D+/', '', $cc) . $digitsOnly;
            } else {
                return null;
            }
        }

        return 'whatsapp:' . $normalized;
    }
}
