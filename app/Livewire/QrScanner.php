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
        $isMerchant = $user && $user->isMerchantUser();
        $isMember = $user && $user->isMember();

        $qrUpper = strtoupper(trim($result)); // uppercase the result
        $qrType = null;

        if($isMerchant && str_starts_with($qrUpper, 'VOU-')) 
        {
            $scannedVoucher = explode('_', $qrUpper);
            $voucherCode = $scannedVoucher[0]; // voucher qr code
            $redeemerQrCode = $scannedVoucher[1]; // member qr code who redeem the voucher

        // Process the QR code to determine type and prepare data for display
            $displayData = [
                'raw_value' => $scannedVoucher,
                'type' => 'voucher',
                'code' => $redeemerQrCode,
                'member_fin' => $redeemerQrCode,
            ];

            $qrType = 'VOU';
            $displayData['type'] = 'voucher';
            $displayData['code'] = $voucherCode;
            $displayData['description'] = 'Voucher QR Code';

        }
        else
        {
            // Process the QR code to determine type and prepare data for display
            $displayData = [
                'raw_value' => $result,
                'type' => null,
                'code' => null,
                'member_fin' => $memberFin,
            ];
            
            if (str_starts_with($qrUpper, 'LOC-')) 
            {
                $qrType = 'LOC';
                $displayData['type'] = 'Location';
                $displayData['code'] = $result;
                $displayData['description'] = 'Location QR Code';
            } 
            elseif (str_starts_with($qrUpper, 'EVT-'))
            {
                $qrType = 'EVT';
                $displayData['type'] = 'Event';
                $displayData['code'] = $result;
                $displayData['description'] = 'Event QR Code';
            } 
            else 
            {
                $displayData['type'] = 'Unknown';
                $displayData['code'] = $result;
                $displayData['description'] = 'Unknown QR Code Type';
            }

        }

        $this->resultData = $displayData;
        $this->showResultModal = true;
        
        // Set the selected QR type and code for component rendering
        if ($isMember && $qrType === 'LOC') {
            $this->selectedQrType = 'location';
            $this->selectedQrCode = $result;
            $this->dispatch('openLocationQrModal', $result);
        } elseif ($isMember && $qrType === 'EVT') {
            $this->selectedQrType = 'event';
            $this->selectedQrCode = $result;
            $this->dispatch('openEventQrModal', $result);
        }
        elseif ($isMerchant && $qrType === 'VOU') {
            $this->selectedQrType = 'voucher';
            $this->selectedQrCode = $voucherCode;
            // Pass both voucher code and redeemer QR code
            $this->dispatch('openVoucherQrModal', $voucherCode, $redeemerQrCode);
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
