<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppCloudApiService
{
    public function enabled(): bool
    {
        return (bool) (config('services.whatsapp.cloud_api_token')
            && config('services.whatsapp.phone_number_id')
            && config('services.whatsapp.api_version'));
    }

    /**
     * Check if a phone number is registered on WhatsApp (Cloud API /contacts).
     */
    public function isWhatsAppUser(string $phoneNumber): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $to = $this->toWhatsAppId($phoneNumber);
        if (!$to) {
            return false;
        }

        $version = config('services.whatsapp.api_version');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        $response = Http::withToken(config('services.whatsapp.cloud_api_token'))
            ->acceptJson()
            ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/contacts", [
                'blocking' => 'wait',
                'contacts' => [
                    [
                        'input' => $to,
                        'type' => 'PHONE',
                    ],
                ],
            ]);

        if (!$response->successful()) {
            return false;
        }

        $contacts = $response->json('contacts');
        $status = $contacts[0]['status'] ?? null;

        return $status === 'valid';
    }

    /**
     * Send a plain text WhatsApp message with the verification code.
     */
    public function sendVerificationCode(string $phoneNumber, string $code): void
    {
        if (!$this->enabled()) {
            return;
        }

        $to = $this->toWhatsAppId($phoneNumber);
        if (!$to) {
            return;
        }

        $version = config('services.whatsapp.api_version');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $appName = config('app.name', 'HopeVillage');

        Http::withToken(config('services.whatsapp.cloud_api_token'))
            ->acceptJson()
            ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => "Your {$appName} verification code is {$code}. It expires in 10 minutes.",
                ],
            ]);
    }

    /**
     * Convert user input into a WhatsApp Cloud API 'to' identifier (digits only, E.164 without '+').
     */
    private function toWhatsAppId(string $phoneNumber): ?string
    {
        $raw = trim($phoneNumber);
        if ($raw === '') {
            return null;
        }

        // Keep digits only.
        $digits = preg_replace('/\D+/', '', $raw);

        return $digits ?: null;
    }
}
