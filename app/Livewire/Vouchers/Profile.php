<?php

namespace App\Livewire\Vouchers;

use App\Models\Voucher;
use Livewire\Component;

class Profile extends Component
{
    public $voucherCode;
    public $voucher;

    public function mount($voucher_code)
    {
        $this->voucherCode = $voucher_code;
        $this->loadVoucher();
    }

    public function loadVoucher()
    {
        $this->voucher = Voucher::with('merchant')
            ->where('voucher_code', $this->voucherCode)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.vouchers.profile', [
            'voucher' => $this->voucher,
        ])->layout('components.layouts.app');
    }
}
