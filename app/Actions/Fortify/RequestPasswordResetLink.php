<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\TwilioWhatsAppService;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RequestPasswordResetLink
{
    protected $twilioService;
    protected $whatsappCloudService;

    public function __construct()
    {
        $this->twilioService = new TwilioWhatsAppService();
        $this->whatsappCloudService = new WhatsAppCloudApiService();
    }

    /**
     * Handle the password reset request
     */
    public function __invoke(array $input): string
    {
        $resetMethod = $input['reset_method'] ?? 'whatsapp'; // 'email', 'whatsapp', or 'sms'
        $identifier = $input['identifier'] ?? ''; // email or phone number

        // Find user by email or phone number
        $user = null;
        if ($resetMethod === 'email') {
            $user = User::where('email', $identifier)->first();
        } else {
            // For both WhatsApp and SMS, search by whatsapp_number field
            $normalizedNumber = $this->normalizeWhatsAppNumber($identifier);
            $user = User::where('whatsapp_number', $identifier)
                ->orWhere('whatsapp_number', $normalizedNumber)
                ->first();
        }

        if (!$user) {
            // Return same message regardless to prevent user enumeration
            return __('If your account exists, we have sent password reset instructions.');
        }

        // Generate a new temporary password
        $temporaryPassword = Str::random(12);

        // Update user's password
        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
        ])->save();

        // Send via chosen method
        if ($resetMethod === 'whatsapp' || $resetMethod === 'sms') {
            // Both WhatsApp and SMS use the WhatsApp service
            $this->sendPasswordViaWhatsApp($user, $temporaryPassword, $resetMethod, $identifier);
        } else {
            // Use default Laravel password reset (sends link via email)
            Password::sendResetLink(['email' => $user->email]);
        }

        return __('If your account exists, we have sent password reset instructions.');
    }

    /**
     * Send temporary password via WhatsApp (used for both WhatsApp and SMS)
     */
    protected function sendPasswordViaWhatsApp(User $user, string $password, string $method = 'whatsapp', string $identifier = ''): void
    {
        // Use the user's stored whatsapp_number, or the provided identifier if different
        $phoneNumber = $user->whatsapp_number;
        
        // If we have an identifier and it's different from user's stored number, use the identifier
        // This handles cases where user provides a different number for SMS
        if ($identifier) {
            $normalizedIdentifier = $this->normalizeWhatsAppNumber($identifier);
            $normalizedStored = $this->normalizeWhatsAppNumber($phoneNumber ?? '');
            
            // Use identifier if it's provided and different, or if user has no stored number
            if (!$phoneNumber || ($normalizedIdentifier !== $normalizedStored && $normalizedIdentifier)) {
                $phoneNumber = $normalizedIdentifier;
            }
        }
        
        if (!$phoneNumber) {
            Log::warning('User has no phone number for password reset', [
                'user_id' => $user->id,
                'method' => $method
            ]);
            return;
        }

        $appName = config('app.name', 'Hope Village');
        $methodLabel = $method === 'sms' ? 'SMS' : 'WhatsApp';
        $message = "Your {$appName} temporary password is: {$password}\n\nPlease login and change your password immediately for security.";

        // Try Twilio first, then WhatsApp Cloud API
        if ($this->twilioService->enabled()) {
            $result = $this->twilioService->sendMessage($phoneNumber, $message);
            if ($result['ok']) {
                Log::info("Password reset sent via Twilio {$methodLabel}", [
                    'user_id' => $user->id,
                    'method' => $method
                ]);
                return;
            }
        }

        if ($this->whatsappCloudService->enabled()) {
            $this->whatsappCloudService->sendMessage($phoneNumber, $message);
            Log::info("Password reset sent via WhatsApp Cloud API ({$methodLabel})", [
                'user_id' => $user->id,
                'method' => $method
            ]);
        }
    }

    /**
     * Normalize WhatsApp number format
     */
    protected function normalizeWhatsAppNumber(string $number): string
    {
        // Remove all non-digit characters except +
        $normalized = preg_replace('/[^\d+]/', '', $number);
        
        // If doesn't start with +, add default country code (Singapore)
        if (!str_starts_with($normalized, '+')) {
            $normalized = '+65' . preg_replace('/\D/', '', $normalized);
        }
        
        return $normalized;
    }
}

