<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            Password::min(8)
                // ->mixedCase() // Requires at least one uppercase and one lowercase letter
                // ->symbols() // Requires at least one special character
                ->numbers() // Requires at least one number
                ->uncompromised(), // Checks if password has been compromised in data leaks
            'confirmed'
        ];
    }
}
