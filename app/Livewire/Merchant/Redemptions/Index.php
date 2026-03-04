<?php

namespace App\Livewire\Merchant\Redemptions;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public string $sortOption = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortOption' => ['except' => ''],
    ];

    private const VALID_SORT_OPTIONS = [
        'member_name_asc', 'member_name_desc',
        'voucher_name_asc', 'voucher_name_desc',
        'voucher_code_asc', 'voucher_code_desc',
        'redeemed_at_asc', 'redeemed_at_desc',
    ];

    public function mount(): void
    {
        if (!in_array($this->sortOption, self::VALID_SORT_OPTIONS, true)) {
            $this->sortOption = '';
        }
    }

    public function updatedSortOption(): void
    {
        if (!in_array($this->sortOption, self::VALID_SORT_OPTIONS, true)) {
            $this->sortOption = 'redeemed_at_desc';
        }
    }

    private function getSortBy(): string
    {
        $parts = explode('_', $this->sortOption);
        $direction = array_pop($parts);
        return implode('_', $parts) ?: 'redeemed_at';
    }

    private function getSortDirection(): string
    {
        $parts = explode('_', $this->sortOption);
        $direction = array_pop($parts);
        return ($direction ?? 'desc') === 'asc' ? 'asc' : 'desc';
    }

    private function getMerchantSortColumn(): string
    {
        return match ($this->getSortBy()) {
            'member_name' => 'users.name',
            'voucher_name' => 'vouchers.name',
            'voucher_code' => 'vouchers.voucher_code',
            default => 'user_voucher.redeemed_at',
        };
    }

    private function getAdminSortColumn(): string
    {
        return match ($this->getSortBy()) {
            'member_name' => 'users.name',
            'voucher_name' => 'admin_vouchers.name',
            'voucher_code' => 'admin_vouchers.voucher_code',
            default => 'user_admin_voucher.redeemed_at',
        };
    }

    public function render()
    {
        $merchant = auth()->user()->currentMerchant();

        if (!$merchant) {
            abort(403, 'No merchant associated with your account. Please contact an administrator.');
        }

        $merchantQuery = DB::table('user_voucher')
            ->join('vouchers', 'vouchers.id', '=', 'user_voucher.voucher_id')
            ->join('users', 'users.id', '=', 'user_voucher.user_id')
            ->whereNull('vouchers.deleted_at')
            ->whereNull('users.deleted_at')
            ->where('vouchers.merchant_id', $merchant->id)
            ->where('user_voucher.status', 'redeemed');

        if ($this->search !== '') {
            $merchantQuery->where('users.name', 'like', '%' . $this->search . '%');
        }

        $merchantRedemptions = $merchantQuery
            ->select(
                'users.name as member_name',
                'vouchers.name as voucher_name',
                'vouchers.voucher_code',
                'user_voucher.redeemed_at'
            )
            ->orderBy($this->getMerchantSortColumn(), $this->getSortDirection())
            ->get();

        $adminQuery = DB::table('user_admin_voucher')
            ->join('admin_vouchers', 'admin_vouchers.id', '=', 'user_admin_voucher.admin_voucher_id')
            ->join('users', 'users.id', '=', 'user_admin_voucher.user_id')
            ->whereNull('admin_vouchers.deleted_at')
            ->whereNull('users.deleted_at')
            ->where('user_admin_voucher.redeemed_at_merchant_id', $merchant->id)
            ->where('user_admin_voucher.status', 'redeemed');

        if ($this->search !== '') {
            $adminQuery->where('users.name', 'like', '%' . $this->search . '%');
        }

        $adminRedemptions = $adminQuery
            ->select(
                'users.name as member_name',
                'admin_vouchers.name as voucher_name',
                'admin_vouchers.voucher_code',
                'user_admin_voucher.redeemed_at'
            )
            ->orderBy($this->getAdminSortColumn(), $this->getSortDirection())
            ->get();

        return view('livewire.merchant.redemptions.index', [
            'merchantRedemptions' => $merchantRedemptions,
            'adminRedemptions' => $adminRedemptions,
            'merchant' => $merchant,
        ])->layout('components.layouts.app');
    }
}
