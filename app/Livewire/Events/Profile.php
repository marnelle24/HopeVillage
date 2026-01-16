<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Services\QrCodeService;
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
        $qrCodeService = app(QrCodeService::class);
        $qrCodeImage = $qrCodeService->generateQrCodeImage($this->event->event_code, 400);

        return view('livewire.events.profile', [
            'event' => $this->event,
            'qrCodeImage' => $qrCodeImage,
        ])->layout('components.layouts.app');
    }
}
