<?php

namespace App\Livewire;

use App\Models\Location;
use Livewire\Component;

class LocationQrCodeModal extends Component
{
    public $locationCode;
    public $open = false;
    public $location = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;

    protected $listeners = [
        'openLocationQrModal' => 'open',
        'closeLocationQrModal' => 'close',
    ];

    public function mount($locationCode = null)
    {
        $this->locationCode = $locationCode;
        if ($this->locationCode) {
            $this->loadLocation();
        }
    }

    public function open($locationCode = null)
    {
        if ($locationCode) {
            $this->locationCode = $locationCode;
        }
        if ($this->locationCode) {
            $this->loadLocation();
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

    public function loadLocation()
    {
        if ($this->locationCode) {
            $this->location = Location::where('location_code', $this->locationCode)->first();
        }
    }

    public function processScan()
    {
        $user = auth()->user();
        
        if (!$user || !$user->fin) {
            $this->error = 'Please login to scan QR codes.';
            return;
        }

        if (!$this->location) {
            $this->error = 'Location not found.';
            return;
        }

        $this->processing = true;
        $this->error = null;
        $this->success = false;

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->post(route('api.member-activity.scan'), [
                'member_fin' => $user->fin,
                'location_code' => $this->locationCode,
                'type_of_activity' => 'ENTRY'
            ]);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                $this->success = true;
                $this->dispatch('location-qr-processed', [
                    'code' => $this->locationCode,
                    'data' => $data
                ]);
                session()->flash('qr-scan-success', 'Location scan processed successfully!');
            } else {
                $this->error = $data['message'] ?? $data['error'] ?? 'Failed to process location scan.';
            }
        } catch (\Exception $e) {
            $this->error = 'Network error: Failed to process scan.';
        } finally {
            $this->processing = false;
        }
    }

    public function render()
    {
        return view('livewire.location-qr-code-modal');
    }
}
