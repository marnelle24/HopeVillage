<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class AllEvents extends Component
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
        $event = Event::findOrFail($id);
        $event->delete(); // Soft delete
        
        session()->flash('message', 'Event deleted successfully.');
        $this->showMessage = true;
        $this->dispatch('event-deleted');
    }

    public function render()
    {
        $query = Event::with(['location', 'creator', 'registrations', 'media']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('event_code', 'like', '%' . $this->search . '%')
                  ->orWhere('venue', 'like', '%' . $this->search . '%')
                  ->orWhereHas('location', function ($locationQuery) {
                      $locationQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $events = $query->orderBy('start_date', 'desc')
            ->paginate(12);

        return view('livewire.events.all-events', [
            'events' => $events,
        ])->layout('components.layouts.app');
    }
}
