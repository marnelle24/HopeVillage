<?php

namespace App\Livewire\Member\Vouchers;

use App\Models\Voucher;
use Illuminate\Support\Collection;
use Livewire\Component;

class Browse extends Component
{
    public function claim(int $voucherId): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $voucher = Voucher::query()->whereKey($voucherId)->first();
        if (!$voucher || !$voucher->isValid()) {
            $this->dispatch('notify', type: 'error', message: 'Voucher is not available.');
            return;
        }

        // Prevent duplicates (unique constraint on pivot also enforces this).
        if ($user->vouchers()->where('vouchers.id', $voucherId)->exists()) {
            $this->dispatch('notify', type: 'info', message: 'You already claimed this voucher.');
            return;
        }

        $user->vouchers()->attach($voucherId, [
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Voucher claimed!');
    }

    public function getActiveVouchersProperty(): Collection
    {
        return Voucher::query()
            ->with('merchant')
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    public function getClaimableVouchersProperty(): Collection
    {
        $user = auth()->user();

        $vouchers = $this->activeVouchers->filter(fn (Voucher $v) => $v->isValid());

        if (!$user) {
            return $vouchers->values();
        }

        $claimedIds = $user->vouchers()->pluck('vouchers.id')->all();

        return $vouchers
            ->reject(fn (Voucher $v) => in_array($v->id, $claimedIds, true))
            ->values();
    }

    public function render()
    {
        return view('livewire.member.vouchers.browse');
    }
}


