<?php

namespace App\Livewire;

use App\Models\Voucher;
use Livewire\Component;

class VoucherQrCodeModal extends Component
{
    public $voucherCode;
    public $open = false;
    public $voucher = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;

    protected $listeners = [
        'openVoucherQrModal' => 'open',
        'closeVoucherQrModal' => 'close',
    ];

    public function mount($voucherCode = null)
    {
        $this->voucherCode = $voucherCode;
        if ($this->voucherCode) {
            $this->loadVoucher();
        }
    }

    public function open($voucherCode = null)
    {
        if ($voucherCode) {
            $this->voucherCode = $voucherCode;
        }
        if ($this->voucherCode) {
            $this->loadVoucher();
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

    public function loadVoucher()
    {
        if ($this->voucherCode) {
            // Voucher code already includes VOU- prefix in database
            $this->voucher = Voucher::with('merchant')->where('voucher_code', $this->voucherCode)->first();
        }
    }

    public function processScan()
    {
        // TODO: Implement voucher QR code processing logic
        $this->error = 'Voucher QR code processing is not yet implemented.';
    }

    public function render()
    {
        return view('livewire.voucher-qr-code-modal');
    }
}
