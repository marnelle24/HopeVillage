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
        $registeredEvents = auth()->user()
            ->eventRegistrations()
            ->whereNotNull('event_id')
            ->with('event.location', 'event.media')
            ->latest('registered_at')
            ->get();

        return view('livewire.member.events.my-events', [
            'registeredEvents' => $registeredEvents,
        ]);
    }
}


