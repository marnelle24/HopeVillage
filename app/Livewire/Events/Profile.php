<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;

class Profile extends Component
{
    public $eventCode;
    public $event;

    public function mount($event_code)
    {
        $this->eventCode = $event_code;
        $this->loadEvent();
    }

    public function loadEvent()
    {
        $this->event = Event::with(['location', 'creator', 'registrations.user'])
            ->where('event_code', $this->eventCode)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.events.profile', [
            'event' => $this->event,
        ])->layout('components.layouts.app');
    }
}
