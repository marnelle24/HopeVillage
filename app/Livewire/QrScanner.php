<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Reusable QR Code Scanner Component
 * 
 * Usage:
 * 1. Include the component in your view:
 *    <livewire:qr-scanner />
 * 
 * 2. Open the scanner from anywhere:
 *    - Dispatch event: $this->dispatch('openQrScanner');
 *    - Or use Alpine.js: $dispatch('openQrScanner');
 * 
 * 3. Listen for scan results:
 *    - Listen to 'qr-code-scanned' event with the scanned value
 *    - Example: protected $listeners = ['qr-code-scanned' => 'handleQrScan'];
 * 
 * 4. Customize title/description:
 *    <livewire:qr-scanner title="Scan Member QR Code" description="Point camera at QR code" />
 */
class QrScanner extends Component
{
    public $open = false;
    public $scanError = null;
    public $scanResult = null;
    public $showResultModal = false;
    public $resultData = null;
    public $selectedQrType = null; // 'location', 'event', 'voucher', or null
    public $selectedQrCode = null;
    public $autoClose = true; // Auto close modal after successful scan
    public $title = 'Scan QR Code';
    public $description = 'Position the QR code within the frame';

    protected $listeners = [
        'openQrScanner' => 'open',
        'closeQrScanner' => 'close',
    ];

    public function open()
    {
        $this->open = true;
        $this->scanError = null;
        $this->scanResult = null;
        $this->dispatch('qr-scanner-opened');
    }

    public function close()
    {
        $this->open = false;
        $this->scanError = null;
        $this->scanResult = null;
        $this->dispatch('qr-scanner-closed');
    }

    public function handleScanResult($result)
    {
        $this->scanResult = $result;
        
        // Get the authenticated user's FIN
        $user = auth()->user();
        $memberFin = $user?->fin ?? null;
        
        // Process the QR code to determine type and prepare data for display
        $qrUpper = strtoupper(trim($result));
        $qrType = null;
        $displayData = [
            'raw_value' => $result,
            'type' => null,
            'code' => null,
            'member_fin' => $memberFin,
        ];
        
        if (str_starts_with($qrUpper, 'LOC-')) {
            $qrType = 'LOC';
            $displayData['type'] = 'Location';
            $displayData['code'] = $result;
            $displayData['description'] = 'Location QR Code';
        } elseif (str_starts_with($qrUpper, 'EVT-')) {
            $qrType = 'EVT';
            $displayData['type'] = 'Event';
            $displayData['code'] = $result;
            $displayData['description'] = 'Event QR Code';
        } elseif (str_starts_with($qrUpper, 'VOU-')) {
            $qrType = 'VOU';
            $displayData['type'] = 'Voucher';
            $displayData['code'] = $result;
            $displayData['description'] = 'Voucher QR Code';
        } else {
            $displayData['type'] = 'Unknown';
            $displayData['code'] = $result;
            $displayData['description'] = 'Unknown QR Code Type';
        }

        $this->resultData = $displayData;
        $this->showResultModal = true;
        
        // Set the selected QR type and code for component rendering
        if ($qrType === 'LOC') {
            $this->selectedQrType = 'location';
            $this->selectedQrCode = $result;
            $this->dispatch('openLocationQrModal', $result);
        } elseif ($qrType === 'EVT') {
            $this->selectedQrType = 'event';
            $this->selectedQrCode = $result;
            $this->dispatch('openEventQrModal', $result);
        } elseif ($qrType === 'VOU') {
            $this->selectedQrType = 'voucher';
            $this->selectedQrCode = $result;
            $this->dispatch('openVoucherQrModal', $result);
        }
        
        $this->dispatch('qr-code-scanned', $result);
    }
    
    public function closeResultModal()
    {
        $this->showResultModal = false;
        $this->resultData = null;
        $this->selectedQrType = null;
        $this->selectedQrCode = null;
    }
    
    public function processLocationScan($locationCode)
    {
        $user = auth()->user();
        
        if (!$user || !$user->fin) {
            $this->scanError = 'Please login to scan QR codes.';
            $this->showResultModal = false;
            return;
        }
        
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->post(route('api.member-activity.scan'), [
                'member_fin' => $user->fin,
                'location_code' => $locationCode,
                'type_of_activity' => 'ENTRY'
            ]);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                $this->dispatch('qr-code-processed', [
                    'type' => 'location',
                    'code' => $locationCode,
                    'data' => $data
                ]);
                $this->closeResultModal();
                session()->flash('qr-scan-success', 'Location scan processed successfully!');
            } else {
                $this->scanError = $data['message'] ?? $data['error'] ?? 'Failed to process location scan.';
                $this->showResultModal = false;
            }
        } catch (\Exception $e) {
            $this->scanError = 'Network error: Failed to process scan.';
            $this->showResultModal = false;
        }
    }

    public function render()
    {
        return view('livewire.qr-scanner');
    }
}
