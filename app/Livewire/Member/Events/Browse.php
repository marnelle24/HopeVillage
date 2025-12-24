<?php

namespace App\Livewire\Member\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
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
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
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
            $registration = EventRegistration::firstOrCreate(
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

            if ($registration->wasRecentlyCreated) {
                app(PointsService::class)->awardEventJoin($user, $event);
            }
        } catch (QueryException) {
            // In case of a race condition on the unique(user_id,event_id) constraint.
        }

        // Set success flash message
        session()->flash('message', 'You have successfully liked the event.');
        session()->flash('message_type', 'success');

        sleep(1); // Wait for 2 seconds to allow the flash message to be displayed

        // Redirect to member events page with type=my-events parameter
        $this->redirect(route('member.events', ['type' => 'my-events']));
    }

    public function render()
    {
        $userId = auth()->id();

        $events = Event::query()
            ->where('status', 'published')
            ->where('end_date', '>=', now())
            // Only show events from locations that are not soft deleted
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('venue', 'like', '%' . $this->search . '%')
                        ->orWhereHas('location', function ($lq) {
                            $lq->whereNull('deleted_at')
                                ->where('name', 'like', '%' . $this->search . '%');
                        });
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


