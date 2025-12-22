<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    /**
     * Handle the incoming request.
     * Fortify expects this to return a User or throw ValidationException.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Request $request): User
    {
        $username = trim($request->input(Fortify::username()));
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            throw ValidationException::withMessages([
                Fortify::username() => [__('The provided credentials are incorrect.')],
            ]);
        }

        // Determine if the input is an email or WhatsApp number
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        
        // Find user by email or WhatsApp number
        $user = null;
        if ($isEmail) {
            // Try to find by email
            $user = User::where('email', $username)->first();
        } else {
            // Try to find by WhatsApp number
            // Normalize the WhatsApp number (remove spaces, dashes, etc., but keep +)
            $normalizedWhatsApp = preg_replace('/[^\d+]/', '', $username);
            
            // Try exact match first
            $user = User::where('whatsapp_number', $normalizedWhatsApp)->first();
            
            // If not found, try with the original input
            if (!$user) {
                $user = User::where('whatsapp_number', $username)->first();
            }
            
            // If still not found, try without + prefix
            if (!$user && str_starts_with($normalizedWhatsApp, '+')) {
                $withoutPlus = substr($normalizedWhatsApp, 1);
                $user = User::where('whatsapp_number', $withoutPlus)
                    ->orWhere('whatsapp_number', '+' . $withoutPlus)
                    ->first();
            }
        }

        // If user not found or password doesn't match
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                Fortify::username() => [__('The provided credentials are incorrect.')],
            ]);
        }

        // Update the request with the user's email so Fortify can continue
        // This ensures session management works correctly
        $request->merge([Fortify::username() => $user->email]);

        return $user;
    }
}

