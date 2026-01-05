<?php

namespace App\Livewire\Merchant\Vouchers;

use App\Models\Voucher;
use App\Services\QrCodeService;
use Livewire\Component;

class Card extends Component
{
    public $voucherCode;
    public $voucher;
    public $merchant;

    public function mount($voucherCode)
    {
        $this->voucherCode = $voucherCode;
        $this->merchant = auth()->user()->currentMerchant();
        
        if (!$this->merchant) {
            abort(403, 'No merchant associated with your account.');
        }

        $this->voucher = Voucher::where('voucher_code', $voucherCode)
            ->where('merchant_id', $this->merchant->id)
            ->firstOrFail();
    }

    public function edit()
    {
        return redirect()->route('merchant.vouchers.edit', $this->voucherCode);
    }

    public function delete()
    {
        if (!$this->merchant->is_active) {
            abort(403, 'Your merchant account is pending approval. You cannot delete vouchers until your account is approved.');
        }

        $this->voucher->delete();
        
        session()->flash('message', 'Voucher archived successfully.');
        $this->dispatch('voucher-deleted');
    }

    public function render()
    {
        $qrCodeService = app(QrCodeService::class);
        $qrCodeImage = $qrCodeService->generateQrCodeImage($this->voucher->voucher_code, 80);
        $qrCodeImageFull = $qrCodeService->generateQrCodeImage($this->voucher->voucher_code, 400);

        return view('livewire.merchant.vouchers.card', [
            'qrCodeImageFull' => $qrCodeImageFull,
        ]);
    }
}

