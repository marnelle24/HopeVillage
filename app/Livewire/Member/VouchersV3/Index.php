<?php

namespace App\Livewire\Member\VouchersV3;

use App\Models\AdminVoucher;
use App\Models\Merchant;
use App\Models\Voucher;
use App\Services\PointsService;
use App\Services\QrCodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public string $tab = 'active';

    public bool $showQr = false;
    public ?string $qrImage = null;
    public ?string $qrVoucherName = null;
    public ?string $qrVoucherCode = null;
    public ?string $qrRedeemableAt = null;

    protected $listeners = [
        'voucher-redeemed' => 'handleVoucherRedeemed',
    ];

    public function mount(): void
    {
        $tab = request()->query('tab');
        if (in_array($tab, ['active', 'claimed', 'redeemed'], true)) {
            $this->tab = $tab;
        }
    }

    public function claim(int $voucherId): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->dispatch('notify', type: 'error', message: 'You must be logged in to claim vouchers.');
            return;
        }

        $voucher = Voucher::query()->whereKey($voucherId)->first();
        if (!$voucher || !$voucher->isValid()) {
            $this->dispatch('notify', type: 'error', message: 'Voucher is not available.');
            return;
        }

        if ($user->vouchers()->where('vouchers.id', $voucherId)->exists()) {
            $this->dispatch('notify', type: 'info', message: 'You already claimed this voucher.');
            return;
        }

        $user->vouchers()->attach($voucherId, [
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $voucher->increment('usage_count');
        app(PointsService::class)->awardVoucherClaim($user, $voucher);

        $this->dispatch('notify', type: 'success', message: 'Voucher claimed successfully!');
    }

    public function claimAdminVoucher(int $adminVoucherId): void
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

        if ($user->adminVouchers()->where('admin_vouchers.id', $adminVoucherId)->exists()) {
            $this->dispatch('notify', type: 'info', message: 'You already claimed this admin voucher.');
            return;
        }

        if ($user->total_points < $adminVoucher->points_cost) {
            $this->dispatch('notify', type: 'error', message: 'Insufficient points. You need ' . number_format($adminVoucher->points_cost) . ' points to claim this voucher.');
            return;
        }

        try {
            DB::transaction(function () use ($user, $adminVoucher) {
                app(PointsService::class)->deductAdminVoucherClaim($user, $adminVoucher);

                $user->adminVouchers()->attach($adminVoucher->id, [
                    'status' => 'claimed',
                    'claimed_at' => now(),
                ]);

                $adminVoucher->increment('usage_count');
            });

            $user->refresh();
            $this->dispatch('notify', type: 'success', message: 'Admin voucher claimed! ' . number_format($adminVoucher->points_cost) . ' points deducted.');
            $this->dispatch('points-updated');
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to claim voucher: ' . $e->getMessage());
        }
    }

    public function showClaimedQr(string $voucherCode, string $type): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        if ($type === 'merchant') {
            $voucher = $user->vouchers()
                ->with('merchant')
                ->where('vouchers.voucher_code', $voucherCode)
                ->wherePivot('status', 'claimed')
                ->first();

            if (!$voucher) {
                $this->dispatch('notify', type: 'error', message: 'Voucher is no longer claimable for QR redemption.');
                return;
            }

            $this->qrVoucherName = $voucher->name;
            $this->qrVoucherCode = $voucher->voucher_code;
            $this->qrRedeemableAt = $voucher->merchant?->name;
        } else {
            $voucher = $user->adminVouchers()
                ->with('merchants')
                ->where('admin_vouchers.voucher_code', $voucherCode)
                ->wherePivot('status', 'claimed')
                ->first();

            if (!$voucher) {
                $this->dispatch('notify', type: 'error', message: 'Voucher is no longer claimable for QR redemption.');
                return;
            }

            $this->qrVoucherName = $voucher->name;
            $this->qrVoucherCode = $voucher->voucher_code;
            $this->qrRedeemableAt = $voucher->merchants->pluck('name')->join(', ');
        }

        $this->qrImage = app(QrCodeService::class)->generateQrCodeImage($voucherCode . '_' . $user->qr_code, 420);
        $this->showQr = true;
    }

    public function closeQr(): void
    {
        $this->showQr = false;
    }

    public function handleVoucherRedeemed($data): void
    {
        $currentUser = auth()->user();
        if (!$currentUser || !isset($data['member_id']) || (int) $data['member_id'] !== (int) $currentUser->id) {
            return;
        }

        $this->dispatch(
            'notify',
            type: $data['type'] ?? 'success',
            message: $data['message'] ?? 'Voucher redemption status updated.'
        );
    }

    public function getActiveItemsProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $claimedVoucherIds = $user->vouchers()->pluck('vouchers.id')->all();
        $claimedAdminVoucherIds = $user->adminVouchers()->pluck('admin_vouchers.id')->all();

        $merchantItems = Voucher::query()
            ->with('merchant')
            ->valid()
            ->latest()
            ->get()
            ->reject(fn (Voucher $voucher) => in_array($voucher->id, $claimedVoucherIds, true))
            ->map(function (Voucher $voucher) {
                return (object) [
                    'id' => $voucher->id,
                    'type' => 'merchant',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchant?->name,
                    'description' => $voucher->description,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => $voucher->discount_value,
                    'min_purchase' => $voucher->min_purchase,
                    'points_cost' => null,
                    'valid_until' => $voucher->valid_until,
                    'created_at' => $voucher->created_at,
                ];
            });

        $adminItems = AdminVoucher::query()
            ->with('merchants')
            ->valid()
            ->latest()
            ->get()
            ->reject(fn (AdminVoucher $voucher) => in_array($voucher->id, $claimedAdminVoucherIds, true))
            ->map(function (AdminVoucher $voucher) {
                return (object) [
                    'id' => $voucher->id,
                    'type' => 'admin',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchants->pluck('name')->join(', '),
                    'description' => $voucher->description,
                    'discount_type' => null,
                    'discount_value' => null,
                    'min_purchase' => null,
                    'points_cost' => $voucher->points_cost,
                    'valid_until' => $voucher->valid_until,
                    'created_at' => $voucher->created_at,
                ];
            });

        return $merchantItems
            ->concat($adminItems)
            ->sortByDesc('created_at')
            ->values();
    }

    public function getClaimedItemsProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $merchantItems = $user->vouchers()
            ->with('merchant')
            ->wherePivot('status', 'claimed')
            ->latest('user_voucher.claimed_at')
            ->get()
            ->map(function (Voucher $voucher) {
                return (object) [
                    'id' => $voucher->id,
                    'type' => 'merchant',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchant?->name,
                    'description' => $voucher->description,
                    'claimed_at' => $voucher->pivot->claimed_at,
                    'valid_until' => $voucher->valid_until,
                    'points_cost' => null,
                ];
            });

        $adminItems = $user->adminVouchers()
            ->with('merchants')
            ->wherePivot('status', 'claimed')
            ->latest('user_admin_voucher.claimed_at')
            ->get()
            ->map(function (AdminVoucher $voucher) {
                return (object) [
                    'id' => $voucher->id,
                    'type' => 'admin',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchants->pluck('name')->join(', '),
                    'description' => $voucher->description,
                    'claimed_at' => $voucher->pivot->claimed_at,
                    'valid_until' => $voucher->valid_until,
                    'points_cost' => $voucher->points_cost,
                ];
            });

        return $merchantItems
            ->concat($adminItems)
            ->sortByDesc(fn ($item) => $item->claimed_at ? strtotime((string) $item->claimed_at) : 0)
            ->values();
    }

    public function getRedeemedItemsProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $merchantItems = $user->vouchers()
            ->with('merchant')
            ->wherePivot('status', 'redeemed')
            ->latest('user_voucher.redeemed_at')
            ->get()
            ->map(function (Voucher $voucher) {
                return (object) [
                    'id' => $voucher->id,
                    'type' => 'merchant',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchant?->name,
                    'description' => $voucher->description,
                    'valid_until' => $voucher->valid_until,
                    'redeemed_at' => $voucher->pivot->redeemed_at,
                    'redeemed_at_merchant' => $voucher->merchant?->name,
                    'points_cost' => null,
                ];
            });

        $adminItems = $user->adminVouchers()
            ->with('merchants')
            ->wherePivot('status', 'redeemed')
            ->latest('user_admin_voucher.redeemed_at')
            ->get()
            ->map(function (AdminVoucher $voucher) {
                $redeemedAtMerchant = null;
                if ($voucher->pivot->redeemed_at_merchant_id) {
                    $redeemedAtMerchant = Merchant::find($voucher->pivot->redeemed_at_merchant_id)?->name;
                }

                return (object) [
                    'id' => $voucher->id,
                    'type' => 'admin',
                    'voucher_code' => $voucher->voucher_code,
                    'name' => $voucher->name,
                    'image_url' => $voucher->image_url,
                    'merchant_name' => $voucher->merchants->pluck('name')->join(', '),
                    'description' => $voucher->description,
                    'valid_until' => $voucher->valid_until,
                    'redeemed_at' => $voucher->pivot->redeemed_at,
                    'redeemed_at_merchant' => $redeemedAtMerchant,
                    'points_cost' => $voucher->points_cost,
                ];
            });

        return $merchantItems
            ->concat($adminItems)
            ->sortByDesc(fn ($item) => $item->redeemed_at ? strtotime((string) $item->redeemed_at) : 0)
            ->values();
    }

    public function render()
    {
        return view('livewire.member.vouchers-v3.index');
    }
}
