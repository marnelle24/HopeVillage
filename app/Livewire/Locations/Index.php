<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
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

    public function delete($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();
        
        session()->flash('message', 'Location deleted successfully.');
        $this->showMessage = true;
        $this->dispatch('location-deleted');
    }

    public function render()
    {
        $query = Location::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $locations = $query->with('media')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.locations.index', [
            'locations' => $locations,
        ])->layout('components.layouts.app');
    }
}

