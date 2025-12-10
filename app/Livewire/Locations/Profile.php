<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;

class Profile extends Component
{
    public $locationCode;
    public $location;

    public function mount($location_code)
    {
        $this->locationCode = $location_code;
        $this->loadLocation();
    }

    public function loadLocation()
    {
        $this->location = Location::with(['events' => function ($query) {
            $query->latest()->take(5);
        }])->where('location_code', $this->locationCode)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.locations.profile', [
            'location' => $this->location,
        ])->layout('components.layouts.app');
    }
}
