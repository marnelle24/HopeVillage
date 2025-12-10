<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $locationCode;
    public $location;
    public $search = '';
    public $statusFilter = 'all';
    public $showMessage = false;

    protected $paginationTheme = 'tailwind';

    public function mount($location_code)
    {
        $this->locationCode = $location_code;
        $this->location = Location::where('location_code', $location_code)->firstOrFail();
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
        $event = Event::findOrFail($id);
        $event->delete(); // Soft delete
        
        session()->flash('message', 'Event deleted successfully.');
        $this->showMessage = true;
        $this->dispatch('event-deleted');
    }

    public function render()
    {
        $query = Event::where('location_id', $this->location->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('event_code', 'like', '%' . $this->search . '%')
                  ->orWhere('venue', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $events = $query->with(['creator', 'registrations'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('livewire.events.index', [
            'events' => $events,
            'location' => $this->location,
        ])->layout('components.layouts.app');
    }
}
