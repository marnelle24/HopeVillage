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
