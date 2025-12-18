<?php

namespace App\Providers;

use App\Listeners\SendRegistrationVerificationCodes;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
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
