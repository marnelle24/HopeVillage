<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isMember') && $user->isMember() && !$user->is_verified) {
            return redirect()->route('verification.code.show');
        }

        return redirect()->route('dashboard');
    }
}
