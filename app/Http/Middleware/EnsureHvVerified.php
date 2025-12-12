<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHvVerified
{
    /**
     * Ensure the authenticated member has completed OTP verification.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce for members.
        if ($user && method_exists($user, 'isMember') && $user->isMember()) {
            if (!$user->is_verified) {
                return redirect()->route('verification.code.show');
            }
        }

        return $next($request);
    }
}
