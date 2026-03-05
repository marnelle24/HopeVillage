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
    public ?string $type = null;

    public function mount(string $value, string $type): void
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function redeem(): void
    {
        $user = auth()->user();
        $userQrCode = (string) ($user?->qr_code ?? '');

        // QR payload format: <type>/<FIN>/<voucher_code>
        // type = voucher
        // FIN = fin of the member who claimed the voucher
        // voucher_code = the voucher code as it was created
        $this->qrPayload = $this->value .'_'. $userQrCode;
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


