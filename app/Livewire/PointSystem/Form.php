<?php

namespace App\Livewire\PointSystem;

use App\Models\Amenity;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Location;
use App\Models\ActivityType;
use App\Models\PointSystemConfig;

class Form extends Component
{
    public $configId;
    public $activity_type_id = '';
    public $location_id = '';
    public $amenity_id = '';
    public $points = '';
    public $description = '';
    public $is_active = true;
    public $showMessage = false;

    // Activity Type Modal
    public $showActivityTypeModal = false;
    public $newActivityTypeName = '';
    public $newActivityTypeDescription = '';
    public $newActivityTypeIsActive = true;

    protected $rules = [
        'activity_type_id' => 'required|exists:activity_types,id',
        'location_id' => 'nullable|exists:locations,id',
        'amenity_id' => 'nullable|exists:amenities,id',
        'points' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->pointSystemEnabled = (bool) Setting::get('point_system_enabled', true);

        if (!$this->pointSystemEnabled) {
            return redirect()->route('admin.point-system.index')->with('message', 'Point system is disabled.');
        }
        
        $this->showMessage = session()->has('message');
        
        if ($id) {
            $this->configId = $id;
            $config = PointSystemConfig::findOrFail($id);
            $this->activity_type_id = $config->activity_type_id;
            $this->location_id = $config->location_id;
            $this->amenity_id = $config->amenity_id;
            $this->points = $config->points;
            $this->description = $config->description;
            $this->is_active = $config->is_active;
        }
    }

    public function updatedShowActivityTypeModal($value)
    {
        // Clear the activity type created message when modal is closed
        if (!$value && session()->has('activity_type_created')) {
            session()->forget('activity_type_created');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        // Clear amenity_id when location_id changes
        if ($propertyName === 'location_id') {
            $this->amenity_id = '';
        }
    }

    public function openActivityTypeModal()
    {
        $this->showActivityTypeModal = true;
        $this->newActivityTypeName = '';
        $this->newActivityTypeDescription = '';
        $this->newActivityTypeIsActive = true;
        $this->resetErrorBag();
    }

    public function closeActivityTypeModal()
    {
        $this->showActivityTypeModal = false;
        $this->newActivityTypeName = '';
        $this->newActivityTypeDescription = '';
        $this->resetErrorBag();
    }

    public function createActivityType()
    {
        $this->validate([
            'newActivityTypeName' => 'required|string|max:255|unique:activity_types,name',
            'newActivityTypeDescription' => 'nullable|string',
            'newActivityTypeIsActive' => 'boolean',
        ], [
            'newActivityTypeName.required' => 'Activity type name is required.',
            'newActivityTypeName.unique' => 'This activity type name already exists.',
        ]);

        $activityType = ActivityType::create([
            'name' => $this->newActivityTypeName,
            'description' => $this->newActivityTypeDescription ?: $this->newActivityTypeName,
            'is_active' => $this->newActivityTypeIsActive,
        ]);

        // Set the newly created activity type as selected
        $this->activity_type_id = $activityType->id;

        // Close the modal
        $this->closeActivityTypeModal();

        // Show success message
        session()->flash('activity_type_created', 'Activity type created successfully and selected.');
    }

    public function save()
    {
        $this->validate();

        // Check for duplicate combination (activity_type_id, location_id, amenity_id)
        $existingConfig = PointSystemConfig::where('activity_type_id', $this->activity_type_id)
            ->where('location_id', $this->location_id ?? null)
            ->where('amenity_id', $this->amenity_id ?? null)
            ->when($this->configId, function ($query) {
                $query->where('id', '!=', $this->configId);
            })
            ->first();

        if ($existingConfig) {
            $this->addError('activity_type_id', 'A configuration already exists for this activity type, location, and amenity combination.');
            return;
        }

        $data = [
            'activity_type_id' => $this->activity_type_id,
            'location_id' => $this->location_id ?: null,
            'amenity_id' => $this->amenity_id ?: null,
            'points' => $this->points,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->configId) {
            $config = PointSystemConfig::findOrFail($this->configId);
            $config->update($data);
            $message = 'Point System configuration updated successfully.';
        } else {
            $config = PointSystemConfig::create($data);
            $message = 'Point System configuration created successfully.';
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.point-system.index');
    }

    public function render()
    {
        $activityTypes = ActivityType::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $amenities = Amenity::when($this->location_id, function ($query) {
            $query->where('location_id', $this->location_id);
        })->orderBy('name')->get();

        return view('livewire.point-system.form', [
            'activityTypes' => $activityTypes,
            'locations' => $locations,
            'amenities' => $amenities,
        ])->layout('components.layouts.app');
    }
}

