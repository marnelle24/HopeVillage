<?php

namespace App\View\Components\Member;

use App\Models\Voucher;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\View\Component;
use Illuminate\View\View;

class VoucherLandscape extends Component
{
    public ?int $daysRemaining = null;
    public ?string $redeemedDate = null;

    public function __construct(
        public Voucher $voucher,
        public bool $dimmed = false,
        public bool $redeemed = false,
        public mixed $redeemedAt = null,
    ) {
        $until = $this->voucher->valid_until;
        if ($until instanceof CarbonInterface) {
            // signed: negative => expired, 0 => today
            $this->daysRemaining = now()->diffInDays($until, false);
        }

        if ($this->redeemed) {
            $dt = $this->redeemedAt;
            if (is_string($dt) && $dt !== '') {
                $dt = Carbon::parse($dt);
            }
            if ($dt instanceof CarbonInterface) {
                $this->redeemedDate = $dt->format('M d, Y');
            }
        }
    }

    public function render(): View
    {
        return view('components.member.voucher-landscape');
    }
}


