<?php

namespace App\Livewire\Member\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Illuminate\Database\QueryException;
use Livewire\Component;

class Profile extends Component
{
    public string $eventCode;

    public function mount(string $event_code): void
    {
        $this->eventCode = $event_code;
    }

    public function join(): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('event_code', $this->eventCode)
            ->whereIn('status', ['published', 'completed'])
            ->withCount('registrations')
            ->firstOrFail();

        if ($event->max_participants && $event->max_participants > 0) {
            if ($event->registrations_count >= $event->max_participants) {
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

        session()->flash('message', 'You have successfully joined the event.');
        session()->flash('message_type', 'success');

        $this->dispatch('scroll-to-top');
        $this->dispatch('event-joined', eventId: $event->id);
    }

    public function render()
    {
        $userId = auth()->id();

        $event = Event::query()
            ->where('event_code', $this->eventCode)
            ->whereIn('status', ['published', 'completed'])
            ->with(['location', 'media'])
            ->withCount('registrations')
            ->withExists([
                'registrations as is_registered' => fn ($rq) => $rq->where('user_id', $userId),
            ])
            ->firstOrFail();

        return view('livewire.member.events.profile', [
            'event' => $event,
        ])->layout('layouts.app');
    }
}


