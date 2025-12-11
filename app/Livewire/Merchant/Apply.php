<?php

namespace App\Livewire\Merchant;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
    public $showSuccess = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'contact_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'website' => 'nullable|url|max:255',
        'logo' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'contact_name.required' => 'Contact person name is required.',
        'phone.required' => 'Phone number is required.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please provide a valid email address.',
        'website.url' => 'Please provide a valid website URL.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

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

        // Create or get merchant user
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            // Create new user with random password
            // $randomPassword = Str::random(12);
            $randomPassword = '123123123';
            $user = User::create([
                'name' => $this->contact_name,
                'email' => $this->email,
                'password' => Hash::make($randomPassword),
                'whatsapp_number' => $this->phone,
                'user_type' => 'merchant_user',
                'current_merchant_id' => $merchant->id,
            ]);
        } else {
            // Update existing user if needed
            if (!$user->whatsapp_number && $this->phone) {
                $user->update(['whatsapp_number' => $this->phone]);
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
        ]);
        $this->resetErrorBag();

        $this->showSuccess = true;
    }

    public function render()
    {
        return view('livewire.merchant.apply')->layout('layouts.guest');
    }
}
