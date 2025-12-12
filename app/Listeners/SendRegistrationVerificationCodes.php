<?php

namespace App\Listeners;

use App\Mail\VerificationCodeMail;
use App\Models\VerificationCode;
use App\Services\TwilioWhatsAppService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendRegistrationVerificationCodes
{
    public function __construct(
        private readonly TwilioWhatsAppService $whatsApp,
    ) {
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (!method_exists($user, 'isMember') || !$user->isMember()) {
            return;
        }

        if ($user->is_verified) {
            return;
        }

        // Single code used for both email + WhatsApp.
        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        // Email record + send
        VerificationCode::create([
            'contact' => $user->email,
            'type' => 'email',
            'code' => $code,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($code, 10));

        // WhatsApp send via Twilio (sandbox-compatible). We only record WhatsApp if Twilio accepts the message.
        if (!empty($user->whatsapp_number)) {
            $result = $this->whatsApp->sendVerificationCode($user->whatsapp_number, $code);
            if (($result['ok'] ?? false) === true) {
                VerificationCode::create([
                    'contact' => $user->whatsapp_number,
                    'type' => 'whatsapp',
                    'code' => $code,
                    'expires_at' => $expiresAt,
                    'is_used' => false,
                ]);
            }
        }
    }
}
