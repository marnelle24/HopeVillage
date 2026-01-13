<?php

namespace App\Livewire\Merchants;

use App\Models\Voucher;
use App\Models\AdminVoucher;
use App\Services\QrCodeService;
use Livewire\Component;

class VoucherCard extends Component
{
    public $voucherCode;
    public $type; // 'merchant' or 'admin'
    public $voucher;
    public $claimedCount = 0;
    public $redeemedCount = 0;
    public $qrCodeImage;

    public function mount($voucherCode, $type)
    {
        $this->voucherCode = $voucherCode;
        $this->type = $type;
        $this->loadVoucher();
    }

    public function loadVoucher()
    {
        if ($this->type === 'admin') {
            $this->voucher = AdminVoucher::where('voucher_code', $this->voucherCode)->first();
            if ($this->voucher) {
                $this->claimedCount = $this->voucher->users()
                    ->wherePivot('status', 'claimed')
                    ->count();
                $this->redeemedCount = $this->voucher->users()
                    ->wherePivot('status', 'redeemed')
                    ->count();
            }
        } else {
            $this->voucher = Voucher::where('voucher_code', $this->voucherCode)->first();
            if ($this->voucher) {
                $this->claimedCount = $this->voucher->users()
                    ->wherePivot('status', 'claimed')
                    ->count();
                $this->redeemedCount = $this->voucher->users()
                    ->wherePivot('status', 'redeemed')
                    ->count();
            }
        }
        
        // Generate QR code
        if ($this->voucher) {
            $qrCodeService = app(QrCodeService::class);
            $this->qrCodeImage = $qrCodeService->generateQrCodeImage($this->voucher->voucher_code, 120);
        }
    }

    public function render()
    {
        return view('livewire.merchants.voucher-card');
    }
}
