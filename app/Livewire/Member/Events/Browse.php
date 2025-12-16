<?php

namespace App\Livewire\Member\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function join(int $eventId): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->where('end_date', '>=', now())
            ->findOrFail($eventId);

        if ($event->max_participants && $event->max_participants > 0) {
            $current = $event->registrations()->count();
            if ($current >= $event->max_participants) {
                session()->flash('message', 'This event is already full.');
                session()->flash('message_type', 'error');
                $this->dispatch('scroll-to-top');
                return;
            }
        }

        try {
            EventRegistration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'type' => 'app',
                    'status' => 'registered',
                    'registered_at' => now(),
                ]
            );
        } catch (QueryException) {
            // In case of a race condition on the unique(user_id,event_id) constraint.
        }

        session()->flash('message', 'You have successfully joined the event.');
        session()->flash('message_type', 'success');

        // UX: bring the user back to the alert, and notify other components (e.g. My Events) to refresh.
        $this->dispatch('scroll-to-top');
        $this->dispatch('event-joined', eventId: $event->id);

        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();

        $events = Event::query()
            ->where('status', 'published')
            ->where('end_date', '>=', now())
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('venue', 'like', '%' . $this->search . '%')
                        ->orWhereHas('location', fn ($lq) => $lq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->with(['location', 'media'])
            ->withCount('registrations')
            ->withExists([
                'registrations as is_registered' => fn ($rq) => $rq->where('user_id', $userId),
            ])
            ->orderBy('start_date')
            ->paginate(6);

        return view('livewire.member.events.browse', [
            'events' => $events,
        ]);
    }
}


