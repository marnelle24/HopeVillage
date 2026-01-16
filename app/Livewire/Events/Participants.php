<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use Livewire\Component;
use Livewire\WithPagination;

class Participants extends Component
{
    use WithPagination;

    public $eventCode;
    public $event;
    public $search = '';
    public $statusFilter = 'all';
    public $registeredDateSort = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount($event_code)
    {
        $this->eventCode = $event_code;
        $this->loadEvent();
    }

    public function loadEvent()
    {
        $this->event = Event::where('event_code', $this->eventCode)->firstOrFail();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortByRegisteredDate()
    {
        $this->registeredDateSort = $this->registeredDateSort === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $query = EventRegistration::where('event_id', $this->event->id)
            ->with('user');

        // Apply search filter
        if ($this->search !== '') {
            $searchTerm = '%' . $this->search . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('fin', 'like', $searchTerm);
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply registered date sorting
        $orderByColumn = $this->registeredDateSort === 'asc' ? 'asc' : 'desc';
        $query->orderByRaw('COALESCE(registered_at, created_at) ' . $orderByColumn);

        $registrations = $query->paginate(10);

        return view('livewire.events.participants', [
            'registrations' => $registrations,
            'event' => $this->event,
        ]);
    }
}
