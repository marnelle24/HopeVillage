<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\VerificationCode;
use App\Services\PointsService;
use App\Services\TwilioWhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VerificationCodeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-code', [
            'email' => $user->email,
            'whatsapp' => $user->whatsapp_number,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $code = (string) $request->input('code');

        $valid = VerificationCode::query()
            ->where('code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('type', 'email')->where('contact', $user->email);
                });

                if (!empty($user->whatsapp_number)) {
                    $q->orWhere(function ($q3) use ($user) {
                        $q3->where('type', 'whatsapp')->where('contact', $user->whatsapp_number);
                    });
                }
            })
            ->latest()
            ->first();

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.'])->withInput();
        }

        // Mark all matching codes for this user as used (both email + whatsapp).
        VerificationCode::query()
            ->where('code', $code)
            ->where('is_used', false)
            ->where(function ($q) use ($user) {
                $q->where('contact', $user->email);
                if (!empty($user->whatsapp_number)) {
                    $q->orWhere('contact', $user->whatsapp_number);
                }
            })
            ->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

        // Check if user was already verified before this update
        $wasAlreadyVerified = $user->is_verified;
        
        $user->forceFill(['is_verified' => true])->save();

        // Award 10 points for creating and verifying account (only on first verification)
        if (!$wasAlreadyVerified) {
            // Check if user already has points from account verification (safety check)
            $hasVerificationPoints = $user->pointLogs()
                ->whereHas('activityType', function ($query) {
                    $query->where('name', PointsService::ACTIVITY_ACCOUNT_VERIFICATION);
                })
                ->exists();

            if (!$hasVerificationPoints) {
                app(PointsService::class)->awardAccountVerification($user);
            }
        }

        return redirect()->route('dashboard');
    }

    public function resend(Request $request, TwilioWhatsAppService $whatsApp): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('dashboard');
        }

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        VerificationCode::create([
            'contact' => $user->email,
            'type' => 'email',
            'code' => $code,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($code, 10));

        $whatsAppSent = false;
        $whatsAppError = null;
        if (!empty($user->whatsapp_number)) {
            $result = $whatsApp->sendVerificationCode($user->whatsapp_number, $code);
            $whatsAppSent = (bool) ($result['ok'] ?? false);
            $whatsAppError = $result['error'] ?? null;

            if ($whatsAppSent) {
                VerificationCode::create([
                    'contact' => $user->whatsapp_number,
                    'type' => 'whatsapp',
                    'code' => $code,
                    'expires_at' => $expiresAt,
                    'is_used' => false,
                ]);
            }
        }

        $message = 'A new verification code has been sent to your email.';
        if (!empty($user->whatsapp_number)) {
            if ($whatsAppSent) {
                $message .= ' WhatsApp message sent.';
            } else {
                $message .= ' WhatsApp not sent.';
                if ($whatsAppError) {
                    $message .= ' Reason: ' . $whatsAppError;
                } else {
                    $message .= ' Reason: unknown (check logs).';
                }
            }
        }

        return back()->with('status', $message);
    }
}
