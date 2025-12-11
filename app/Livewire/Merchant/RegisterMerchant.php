<?php

namespace App\Livewire\Merchant;

use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegisterMerchant extends Component
{
    use WithFileUploads;

    public $showModal = false;
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


    public function mount()
    {
        $currentMerchant = auth()->user()->currentMerchant();
        
        // Only allow if current merchant is active
        if (!$currentMerchant || !$currentMerchant->is_active) {
            $this->showModal = false;
        }
    }

    public function openModal()
    {
        $currentMerchant = auth()->user()->currentMerchant();
        
        // Only allow if current merchant is active
        if (!$currentMerchant || !$currentMerchant->is_active) {
            return;
        }
        
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function updatedShowModal($value)
    {
        if (!$value) {
            // Modal was closed, reset form
            $this->resetForm();
        }
    }

    public function resetForm()
    {
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
            'showSuccess',
        ]);
        $this->resetErrorBag();
    }

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

        // Attach the current user to the new merchant (skip merchant user creation for modal registrations)
        $currentUser = auth()->user();
        if (!$merchant->users()->where('user_id', $currentUser->id)->exists()) {
            $merchant->users()->attach($currentUser->id, [
                'is_default' => false,
            ]);
        }

        $this->resetForm();
        $this->showSuccess = true;
        
        // Close modal after 3 seconds
        $this->dispatch('merchant-registered');
    }

    public function render()
    {
        return view('livewire.merchant.register-merchant');
    }
}
