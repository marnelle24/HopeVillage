<?php

namespace App\Livewire\Member\AdminVouchers;

use App\Models\AdminVoucher;
use App\Services\PointsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Browse extends Component
{
    public function claim(int $adminVoucherId): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->dispatch('notify', type: 'error', message: 'You must be logged in to claim vouchers.');
            return;
        }

        $adminVoucher = AdminVoucher::query()->whereKey($adminVoucherId)->first();
        if (!$adminVoucher || !$adminVoucher->isValid()) {
            $this->dispatch('notify', type: 'error', message: 'Admin voucher is not available.');
            return;
        }

        // Check if already claimed
        if ($user->adminVouchers()->where('admin_vouchers.id', $adminVoucherId)->exists()) {
            $this->dispatch('notify', type: 'info', message: 'You already claimed this admin voucher.');
            return;
        }

        // Check sufficient points balance
        if ($user->total_points < $adminVoucher->points_cost) {
            $this->dispatch('notify', type: 'error', message: 'Insufficient points. You need ' . number_format($adminVoucher->points_cost) . ' points to claim this voucher.');
            return;
        }

        try {
            DB::transaction(function () use ($user, $adminVoucher) {
                // Deduct points
                app(PointsService::class)->deductAdminVoucherClaim($user, $adminVoucher);

                // Attach voucher to user
                $user->adminVouchers()->attach($adminVoucher->id, [
                    'status' => 'claimed',
                    'claimed_at' => now(),
                ]);

                // Increment usage count
                $adminVoucher->increment('usage_count');
            });

            $this->dispatch('notify', type: 'success', message: 'Admin voucher claimed! ' . number_format($adminVoucher->points_cost) . ' points deducted.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to claim voucher: ' . $e->getMessage());
        }
    }

    public function getActiveAdminVouchersProperty(): Collection
    {
        return AdminVoucher::query()
            ->with('merchants')
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    public function getClaimableAdminVouchersProperty(): Collection
    {
        $user = auth()->user();

        $adminVouchers = $this->activeAdminVouchers->filter(fn (AdminVoucher $v) => $v->isValid());

        if (!$user) {
            return $adminVouchers->values();
        }

        $claimedIds = $user->adminVouchers()->pluck('admin_vouchers.id')->all();

        return $adminVouchers
            ->reject(fn (AdminVoucher $v) => in_array($v->id, $claimedIds, true))
            ->values();
    }

    public function render()
    {
        return view('livewire.member.admin-vouchers.browse');
    }
}

