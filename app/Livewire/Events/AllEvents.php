<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

class AllEvents extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $locationFilter = '';
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
        $query = Event::with(['location', 'creator', 'registrations', 'media'])
            // Only show events from locations that are not soft deleted
            ->whereHas('location', function ($locationQuery) {
                $locationQuery->whereNull('deleted_at');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('event_code', 'like', '%' . $this->search . '%')
                  ->orWhere('venue', 'like', '%' . $this->search . '%')
                  ->orWhereHas('location', function ($locationQuery) {
                      $locationQuery->whereNull('deleted_at')
                          ->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->locationFilter) {
            $query->where('location_id', $this->locationFilter);
        }

        $events = $query->orderBy('start_date', 'desc')
            ->paginate(12);

        // Only show non-deleted locations in the filter dropdown
        $locations = Location::whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('livewire.events.all-events', [
            'events' => $events,
            'locations' => $locations,
        ])->layout('components.layouts.app');
    }
}
