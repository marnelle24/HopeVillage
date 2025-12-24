<?php

namespace App\Livewire\Member\Events;

use Livewire\Attributes\On;
use Livewire\Component;

class MyEvents extends Component
{
    #[On('event-joined')]
    public function refreshOnJoinedEvent(): void
    {
        // Intentionally empty: calling this method triggers a re-render.
    }

    public function render()
    {
        $allRegistrations = auth()->user()
            ->eventRegistrations()
            ->whereNotNull('event_id')
            ->with('event.location', 'event.media')
            ->get();

        // Separate attended events from registered/joined events
        $attendedEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status === 'attended' && $registration->event !== null;
            })
            ->sortByDesc('attended_at');

        $registeredEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status !== 'attended' && $registration->event !== null;
            })
            ->sortByDesc('registered_at');

        return view('livewire.member.events.my-events', [
            'attendedEvents' => $attendedEvents,
            'registeredEvents' => $registeredEvents,
        ]);
    }
}


