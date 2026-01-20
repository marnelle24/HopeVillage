<?php

namespace App\Livewire\Merchant;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Jetstream\Jetstream;
use Livewire\Component;
use Livewire\WithFileUploads;

class Apply extends Component
{
    use WithFileUploads;

    public $name = '';
    public $description = '';
    public $contact_name = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $city = '';
    public $province = '';
    public $postal_code = '';
    public $website = '';
    public $logo;
    public $password = '';
    public $password_confirmation = '';
    public $terms = false;
    public $showSuccess = false;
    public $isSubmitting = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:9',
                'unique:users,whatsapp_number',
            ],
            'email' => 'nullable|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase() // Requires at least one uppercase and one lowercase letter
                    ->numbers() // Requires at least one number
                    ->symbols() // Requires at least one special character
                    ->uncompromised(), // Checks if password has been compromised in data leaks
                'confirmed',
            ],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];
    }

    protected $messages = [
        'name.required' => 'Merchant name is required.',
        'contact_name.required' => 'Contact person name is required.',
        'phone.required' => 'Phone number is required.',
        'phone.unique' => 'This mobile number is already registered. Please use a different number.',
        'phone.max' => 'The mobile number must be 9 digits long.',
        'email.email' => 'Please provide a valid email address.',
        'website.url' => 'Please provide a valid website URL.',
        'password.required' => 'Password is required.',
        'password.min' => 'The password must be at least 8 characters.',
        'password.mixed' => 'The password must contain at least one uppercase and one lowercase letter.',
        'password.numbers' => 'The password must contain at least one number.',
        'password.symbols' => 'The password must contain at least one special character.',
        'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
        'password.confirmed' => 'Password confirmation does not match.',
        'terms.accepted' => 'You must accept the terms and conditions.',
        'terms.required' => 'You must accept the terms and conditions.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

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

    public function submit()
    {
        $this->isSubmitting = true;
        $this->validate();
        
        // Minimum 2 second delay
        sleep(2);

        $merchant = Merchant::create([
            'name' => $this->name,
            'description' => $this->description,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'website' => $this->website,
            'is_active' => false, // Subject for approval
        ]);

        // Handle logo upload
        if ($this->logo) {
            $merchant->addMedia($this->logo->getPathname())
                ->usingName($merchant->name . ' - Logo')
                ->usingFileName($this->logo->getClientOriginalName())
                ->toMediaCollection('logo');
        }

        // Generate email if not provided
        $userEmail = !empty(trim($this->email ?? '')) 
            ? trim($this->email) 
            : $this->generateUserRandomEmail();

        // Create or get merchant user by phone number
        $user = User::where('whatsapp_number', $this->phone)->first();
        
        if (!$user) {
            // Create new user with provided password
            $user = User::create([
                'name' => $this->contact_name,
                'email' => $userEmail,
                'password' => Hash::make($this->password),
                'whatsapp_number' => $this->phone,
                'user_type' => 'merchant_user',
                'current_merchant_id' => $merchant->id,
            ]);
        } else {
            // Update existing user if needed
            // Update email if it was auto-generated and user doesn't have one
            if (empty($user->email) || str_ends_with($user->email, '@hopevillage.sg')) {
                $user->update(['email' => $userEmail]);
            }
            
            // Update user type if not already merchant_user
            if ($user->user_type !== 'merchant_user') {
                $user->update(['user_type' => 'merchant_user']);
            }
        }

        // Attach user to merchant if not already attached
        if (!$merchant->users()->where('user_id', $user->id)->exists()) {
            // Check if this is the user's first merchant
            $isFirstMerchant = $user->merchants()->count() === 0;
            
            $merchant->users()->attach($user->id, [
                'is_default' => $isFirstMerchant,
            ]);

            // Set as current merchant if it's their first merchant
            if ($isFirstMerchant) {
                $user->update(['current_merchant_id' => $merchant->id]);
            }
        }

        // Reset form
        $this->reset([
            'name',
            'description',
            'contact_name',
            'phone',
            'email',
            'address',
            'city',
            'province',
            'postal_code',
            'website',
            'logo',
            'password',
            'password_confirmation',
            'terms',
        ]);
        $this->resetErrorBag();

        $this->showSuccess = true;
        $this->isSubmitting = false;
    }

    public function render()
    {
        return view('livewire.merchant.apply')->layout('layouts.guest');
    }
}
