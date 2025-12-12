<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Rules\ValidFin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Normalize FIN to uppercase early so unique/checksum behave consistently
        if (isset($input['fin'])) {
            $input['fin'] = strtoupper(trim((string) $input['fin']));
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'unique:users,whatsapp_number'],
            'fin' => ['required', 'string', 'size:9', 'unique:users,fin', new ValidFin()],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $userData = [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'user_type' => $input['user_type'] ?? 'member', // Default to member
                'whatsapp_number' => $input['whatsapp_number'] ?? null,
                'age' => $input['age'] ?? null,
                'gender' => $input['gender'] ?? null,
            ];

            // Generate FIN and QR code for members
            if (($userData['user_type'] ?? 'member') === 'member') {
                $userData['fin'] = $input['fin'];
                
                // Set QR code to FIN value
                $userData['qr_code'] = $userData['fin'];
            }

            return tap(User::create($userData), function (User $user) {
                if (Jetstream::userHasTeamFeatures($user)) {
                    $this->createTeam($user);
                }
            });
        });
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
