<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Rules\ValidRecaptcha;
use App\Services\PointsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Generate a random email for the user
     */
    protected function generateUserRandomEmail(): string
    {
        // Generate email: user-uuid@hopevillage.sg
        do {
            $uuid = explode('-', Str::uuid()->toString())[0];
            $email = 'user-' . $uuid . '@hopevillage.sg';
        } while (User::where('email', $email)->exists());
        
        return $email;
    }

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Make email optional - if not provided, generate from WhatsApp
        $email = !empty(trim($input['email'] ?? '')) 
            ? trim($input['email']) 
            : null;

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',  // Changed from 'required'
                'string', 
                'email', 
                'max:255', 
                'unique:users'  // Only validate if email is provided
            ],
            'whatsapp_number' => [
                'required',
                'string',
                'max:12',
                'unique:users,whatsapp_number',
            ],
            'fin' => ['required', 'string', 'size:4', 'regex:/^\d{3}[A-Z]$/i'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'string', 'max:20'],
            'type_of_work' => ['nullable', 'string', 'max:255', 'in:Migrant worker,Migrant domestic worker,Others'],
            'type_of_work_custom' => ['nullable', 'required_if:type_of_work,Others', 'string', 'max:255'],
            'g-recaptcha-response' => config('services.recaptcha.secret_key') ? ['required', new ValidRecaptcha()] : ['nullable'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'whatsapp_number.unique' => 'This mobile number is already registered.',
            'whatsapp_number.required' => 'This mobile number is required.',
            'whatsapp_number.max' => 'The mobile number must be 8 digits long.',
            'fin.required' => 'The FIN/NIRC is required.',
            'fin.regex' => 'The FIN/NIRC is invalid. Example: 123W',
            'fin.size' => 'It must be the last 4 characters only. (e.g. 124X)',
        ])->validate();

        return DB::transaction(function () use ($input, $email) {
            // Generate email from WhatsApp if not provided
            if (empty($email)) {
                $email = $this->generateUserRandomEmail();
            }

            // Determine type_of_work value
            $typeOfWork = $input['type_of_work'] ?? 'Migrant worker';
            if ($typeOfWork === 'Others' && !empty($input['type_of_work_custom'] ?? '')) {
                $typeOfWork = trim($input['type_of_work_custom']);
            }

            $userData = [
                'name' => $input['name'],
                'email' => $email,
                'password' => Hash::make($input['password']),
                'user_type' => $input['user_type'] ?? 'member', // Default to member
                'whatsapp_number' => $input['whatsapp_number'] ?? null,
                'age' => $input['age'] ?? null,
                'gender' => $input['gender'] ?? null,
                'type_of_work' => $typeOfWork,
            ];

            // Get FIN from request instead of auto-generating
            // Use FIN from request
            if (!empty($input['fin'])) {
                $userData['fin'] = strtoupper(trim($input['fin']));
            }

            // Generate unique 9-character QR code for members
            if (($userData['user_type'] ?? 'member') === 'member') {
                // Generate a unique 9-character QR code
                do {
                    $qrCode = strtoupper(Str::random(9));
                } while (User::where('qr_code', $qrCode)->exists());
                
                $userData['qr_code'] = $qrCode;
            }

            // OLD CODE - Auto-generation of FIN (commented out)
            // if (($userData['user_type'] ?? 'member') === 'member') {
            //     // Generate a unique 9-character UID
            //     do {
            //         $fin = strtoupper(Str::random(9));
            //     } while (User::where('fin', $fin)->exists());
            //     
            //     $userData['fin'] = $fin;
            //     
            //     // Set QR code to FIN value
            //     $userData['qr_code'] = $userData['fin'];
            // }

            return tap(User::create($userData), function (User $user) {
                if (Jetstream::userHasTeamFeatures($user)) {
                    $this->createTeam($user);
                }
                
                // Award 10 points for registration (only for members)
                if ($user->isMember()) {
                    app(PointsService::class)->awardRegistration($user);
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
