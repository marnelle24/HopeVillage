<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;

class Delete extends Component
{
    public $locationId;
    public $locationName;
    public $showModal = false;

    protected $listeners = ['confirmDelete'];

    public function confirmDelete($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $id;
        $this->locationName = $location->name;
        $this->showModal = true;
    }

    public function delete()
    {
        $location = Location::findOrFail($this->locationId);
        $location->delete();
        
        $this->showModal = false;
        session()->flash('message', 'Location deleted successfully.');
        
        $this->dispatch('location-deleted');
        return redirect()->route('admin.locations.index');
    }

    public function close()
    {
        $this->showModal = false;
        $this->locationId = null;
        $this->locationName = null;
    }

    public function render()
    {
        return view('livewire.locations.delete');
    }
}

