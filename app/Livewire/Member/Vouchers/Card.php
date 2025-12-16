<?php

namespace App\Livewire\Member\Vouchers;

use App\Services\QrCodeService;
use Livewire\Component;

class Card extends Component
{
    public string $value;

    public bool $showQr = false;
    public ?string $qrImage = null;
    public ?string $qrPayload = null;

    public function mount(string $value): void
    {
        $this->value = $value;
    }

    public function redeem(): void
    {
        $userId = (string) auth()->id();

        // NOTE: This is a UI-only QR payload for now (no server-side redemption flow exists yet).
        $this->qrPayload = "HV_VOUCHER:{$this->value}|USER:{$userId}";
        $this->qrImage = app(QrCodeService::class)->generateQrCodeImage($this->qrPayload, 420);
        $this->showQr = true;
    }

    public function closeQr(): void
    {
        $this->showQr = false;
    }

    public function render()
    {
        return view('livewire.member.vouchers.card');
    }
}


