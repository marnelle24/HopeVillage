<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchantUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isMerchantUser()) {
            abort(403, 'Unauthorized access. Merchant user privileges required.');
        }

        // Ensure user has at least one merchant
        if ($user->merchants()->count() === 0) {
            abort(403, 'No merchant assigned to your account. Please contact an administrator.');
        }

        // Ensure user has a current merchant set
        if (!$user->currentMerchant()) {
            // Set first merchant as current if none is set
            $firstMerchant = $user->merchants()->first();
            if ($firstMerchant) {
                $user->setCurrentMerchant($firstMerchant->id);
            }
        }

        return $next($request);
    }
}
