<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;

class MailCheckService
{
    /**
     * Verify mail credentials and settings from .env, and log the result.
     * For SMTP: connects and authenticates; logs success or auth/connection errors.
     *
     * @return array{success: bool, message: string}
     */
    public function checkAndLog(): array
    {
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $encryption = config('mail.mailers.smtp.encryption');
        $username = config('mail.mailers.smtp.username');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $context = [
            'mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username ? '(set)' : '(empty)',
            'from' => $fromAddress,
        ];

        if (! in_array($mailer, ['smtp', 'failover'], true)) {
            Log::info('Mail check: Not using SMTP. No credentials to verify.', $context);
            return [
                'success' => true,
                'message' => 'Mail driver is "' . $mailer . '". No SMTP credentials to verify.',
            ];
        }

        try {
            $transport = Mail::mailer()->getSymfonyTransport();

            if (! $transport instanceof SmtpTransport) {
                Log::info('Mail check: Default mailer transport is not SMTP.', $context);
                return [
                    'success' => true,
                    'message' => 'Mail transport is not SMTP; no credential check performed.',
                ];
            }

            $transport->start();
            $transport->stop();

            Log::info(
                'Mail credentials and settings are correct. No auth error. SMTP connection and authentication succeeded.',
                $context
            );

            return [
                'success' => true,
                'message' => 'Mail credentials and settings are correct. No auth error.',
            ];
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $context['error'] = $message;

            Log::warning(
                'Mail check failed. Credentials or settings may be wrong or there was an auth/connection error.',
                $context
            );

            return [
                'success' => false,
                'message' => $message,
            ];
        }
    }
}
