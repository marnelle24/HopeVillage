<?php

namespace App\Providers;

use App\Listeners\SendRegistrationVerificationCodes;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Registered::class, SendRegistrationVerificationCodes::class);

        View::addNamespace('mail', resource_path('views/vendor/mail/html'));

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            $appName = config('app.name');
            $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage())
                ->subject(__('Reset Password') . ' - ' . $appName)
                ->markdown('emails.reset-password', [
                    'url' => $url,
                    'userName' => $notifiable->name ?? null,
                    'expireMinutes' => $expireMinutes,
                    'appName' => $appName,
                ]);
        });
        
        // Force HTTPS when behind ngrok
        if (request()->header('x-forwarded-proto') === 'https' || 
            str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
            
            // Configure session cookies for HTTPS/ngrok
            config([
                'session.secure' => true,
                'session.same_site' => 'lax',
            ]);
            
            // Add ngrok domain to Sanctum stateful domains
            $host = request()->getHost();
            if (str_contains($host, 'ngrok')) {
                $stateful = config('sanctum.stateful', []);
                if (!in_array($host, $stateful)) {
                    config(['sanctum.stateful' => array_merge($stateful, [$host])]);
                }
            }
        }
    }
}
