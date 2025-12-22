<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventQrCodeModal extends Component
{
    public $eventCode;
    public $open = false;
    public $event = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;

    protected $listeners = [
        'openEventQrModal' => 'open',
        'closeEventQrModal' => 'close',
    ];

    public function mount($eventCode = null)
    {
        $this->eventCode = $eventCode;
        if ($this->eventCode) {
            $this->loadEvent();
        }
    }

    public function open($eventCode = null)
    {
        if ($eventCode) {
            $this->eventCode = $eventCode;
        }
        if ($this->eventCode) {
            $this->loadEvent();
        }
        $this->open = true;
        $this->error = null;
        $this->success = false;
        $this->memberFin = auth()->user()?->fin;
    }

    public function close()
    {
        $this->open = false;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
    }

    public function loadEvent()
    {
        if ($this->eventCode) {
            // Event code already includes EVT- prefix in database
            $this->event = Event::where('event_code', $this->eventCode)->first();
        }
    }

    public function processScan()
    {
        // TODO: Implement event QR code processing logic
        $this->error = 'Event QR code processing is not yet implemented.';
    }

    public function render()
    {
        return view('livewire.event-qr-code-modal');
    }
}
