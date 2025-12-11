<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $locationId;
    public $name = '';
    public $description = '';
    public $address = '';
    public $city = '';
    public $province = '';
    public $postal_code = '';
    public $phone = '';
    public $email = '';
    public $is_active = true;
    public $thumbnail;
    public $existingThumbnail = null;
    public $latitude = null;
    public $longitude = null;
    public $country = 'SG'; // Default to Singapore (ISO 3166-1 alpha-2 country code)
    public $showMessage = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'is_active' => 'boolean',
        'thumbnail' => 'nullable|image|max:2048',
    ];

    public function mount($location_code = null)
    {
        $this->showMessage = session()->has('message');
        
        if ($location_code) {
            $this->locationId = $location_code;
            $location = Location::where('location_code', $location_code)->firstOrFail();
            $this->name = $location->name;
            $this->description = $location->description;
            $this->address = $location->address;
            $this->city = $location->city;
            $this->province = $location->province;
            $this->postal_code = $location->postal_code;
            $this->phone = $location->phone;
            $this->email = $location->email;
            $this->is_active = $location->is_active;
            
            $media = $location->getFirstMedia('thumbnail');
            if ($media) {
                $this->existingThumbnail = $media->getUrl();
            }
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        if ($this->locationId) {
            $location = Location::where('location_code', $this->locationId)->firstOrFail();
            $location->update([
                'name' => $this->name,
                'description' => $this->description,
                'address' => $this->address,
                'city' => $this->city,
                'province' => $this->province,
                'postal_code' => $this->postal_code,
                'phone' => $this->phone,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ]);
            $message = 'Location updated successfully.';
        } else {
            $location = Location::create([
                'name' => $this->name,
                'description' => $this->description,
                'address' => $this->address,
                'city' => $this->city,
                'province' => $this->province,
                'postal_code' => $this->postal_code,
                'phone' => $this->phone,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ]);
            $message = 'Location created successfully.';
        }

        // Handle thumbnail upload
        if ($this->thumbnail) {
            // Clear existing thumbnail
            $location->clearMediaCollection('thumbnail');
            
            // Add new thumbnail
            $location->addMedia($this->thumbnail->getRealPath())
                ->usingName($location->name . ' - Thumbnail')
                ->toMediaCollection('thumbnail');
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.locations.index');
    }

    public function removeThumbnail()
    {
        if ($this->locationId) {
            $location = Location::where('location_code', $this->locationId)->firstOrFail();
            $location->clearMediaCollection('thumbnail');
            $this->existingThumbnail = null;
            $this->dispatch('thumbnail-removed');
        }
    }

    public function updateAddressFromMap($addressData)
    {
        $this->address = $addressData['address'] ?? '';
        $this->city = $addressData['city'] ?? '';
        $this->province = $addressData['province'] ?? '';
        $this->postal_code = $addressData['postal_code'] ?? '';
        $this->latitude = $addressData['latitude'] ?? null;
        $this->longitude = $addressData['longitude'] ?? null;
    }

    public function render()
    {
        return view('livewire.locations.form')->layout('components.layouts.app');
    }
}

