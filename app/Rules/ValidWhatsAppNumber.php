<?php

namespace App\Rules;

use App\Services\TwilioWhatsAppService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidWhatsAppNumber implements ValidationRule
{
    public function __construct(
        private readonly TwilioWhatsAppService $whatsAppService
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let 'required' rule handle empty values
        }

        // If WhatsApp service is not enabled, skip validation
        if (!$this->whatsAppService->enabled()) {
            return;
        }

        // Normalize the phone number (intl-tel-input sends E.164 format)
        $phoneNumber = trim((string) $value);
        
        // Validate phone number format using Twilio Lookup API
        if (!$this->whatsAppService->isValidWhatsAppNumber($phoneNumber)) {
            $validationResult = $this->whatsAppService->validatePhoneNumber($phoneNumber);
            $errorMessage = $validationResult['error'] ?? 'Please ensure the number is valid and can receive messages.';
            // $fail('The Mobile Number is invalid. ' . $errorMessage);
            $fail('The Mobile Number is invalid.');
        }
    }
}

