<?php

namespace App\Livewire\Amenities;

use App\Models\Amenity;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $locationFilter = '';
    public $typeFilter = '';
    public $showMessage = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->showMessage = session()->has('message');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        return redirect()->route('admin.amenities.edit', $id);
    }

    public function delete($id)
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->delete();
        
        session()->flash('message', 'Amenity deleted successfully.');
        $this->showMessage = true;
        $this->dispatch('amenity-deleted');
    }

    public function render()
    {
        $query = Amenity::with(['location', 'media']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        if ($this->locationFilter) {
            $query->where('location_id', $this->locationFilter);
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $amenities = $query->orderBy('created_at', 'desc')->paginate(10);
        $locations = Location::orderBy('name')->get();

        return view('livewire.amenities.index', [
            'amenities' => $amenities,
            'locations' => $locations,
        ])->layout('components.layouts.app');
    }
}
