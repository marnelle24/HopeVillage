<?php

namespace App\Livewire\Amenities;

use App\Models\Amenity;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $amenityId;
    public $location_id = '';
    public $name = '';
    public $description = '';
    public $type = 'Others';
    public $typeOtherValue = '';
    public $capacity = null;
    public $hourly_rate = null;
    public $operating_hours = [];
    public $is_bookable = true;
    public $is_active = true;
    public $images = [];
    public $existingImages = [];
    public $showMessage = false;

    protected $rules = [
        'location_id' => 'required|exists:locations,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:Basketball,Pickleball,Badminton,Function Hall,Swimming Pool,Others',
        'typeOtherValue' => 'required_if:type,Others|string|max:255',
        'capacity' => 'nullable|integer|min:1',
        'hourly_rate' => 'nullable|numeric|min:0',
        'operating_hours' => 'nullable|array',
        'is_bookable' => 'boolean',
        'is_active' => 'boolean',
        'images.*' => 'nullable|image|max:2048',
    ];

    public function mount($id = null)
    {
        $this->showMessage = session()->has('message');
        
        if ($id) {
            $this->amenityId = $id;
            $amenity = Amenity::findOrFail($id);
            $this->location_id = $amenity->location_id;
            $this->name = $amenity->name;
            $this->description = $amenity->description;
            
            // Handle type: if it starts with "others-", explode it
            if (str_starts_with($amenity->type, 'others-')) {
                $parts = explode('-', $amenity->type, 2);
                $this->type = 'Others';
                $this->typeOtherValue = $parts[1] ?? '';
            } else {
                $this->type = $amenity->type;
                $this->typeOtherValue = '';
            }
            
            $this->capacity = $amenity->capacity;
            $this->hourly_rate = $amenity->hourly_rate;
            $this->operating_hours = $amenity->operating_hours ?? [];
            $this->is_bookable = $amenity->is_bookable;
            $this->is_active = $amenity->is_active;
            
            $media = $amenity->getMedia('images');
            foreach ($media as $m) {
                $this->existingImages[] = $m->getUrl();
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

        // Prepare type value: if Others is selected, concatenate with typeOtherValue
        $typeValue = $this->type;
        if ($this->type === 'Others' && !empty($this->typeOtherValue)) {
            $typeValue = 'others-' . $this->typeOtherValue;
        }

        if ($this->amenityId) {
            $amenity = Amenity::findOrFail($this->amenityId);
            $amenity->update([
                'location_id' => $this->location_id,
                'name' => $this->name,
                'description' => $this->description,
                'type' => $typeValue,
                'capacity' => $this->capacity,
                'hourly_rate' => $this->hourly_rate,
                'operating_hours' => $this->operating_hours,
                'is_bookable' => $this->is_bookable,
                'is_active' => $this->is_active,
            ]);
            $message = 'Amenity updated successfully.';
        } else {
            $amenity = Amenity::create([
                'location_id' => $this->location_id,
                'name' => $this->name,
                'description' => $this->description,
                'type' => $typeValue,
                'capacity' => $this->capacity,
                'hourly_rate' => $this->hourly_rate,
                'operating_hours' => $this->operating_hours,
                'is_bookable' => $this->is_bookable,
                'is_active' => $this->is_active,
            ]);
            $message = 'Amenity created successfully.';
        }

        // Handle image uploads
        if ($this->images) {
            foreach ($this->images as $image) {
                $amenity->addMedia($image->getRealPath())
                    ->usingName($amenity->name . ' - Image')
                    ->toMediaCollection('images');
            }
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.amenities.index');
    }

    public function removeImage($index)
    {
        if (isset($this->existingImages[$index])) {
            $amenity = Amenity::findOrFail($this->amenityId);
            $media = $amenity->getMedia('images');
            if (isset($media[$index])) {
                $media[$index]->delete();
            }
            // Refresh existing images
            $this->existingImages = [];
            foreach ($amenity->getMedia('images') as $m) {
                $this->existingImages[] = $m->getUrl();
            }
        }
    }

    public function removeNewImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function render()
    {
        $locations = Location::orderBy('name')->get();
        $types = ['Basketball', 'Pickleball', 'Badminton', 'Function Hall', 'Swimming Pool', 'Others'];

        return view('livewire.amenities.form', [
            'locations' => $locations,
            'types' => $types,
        ])->layout('components.layouts.app');
    }
}
