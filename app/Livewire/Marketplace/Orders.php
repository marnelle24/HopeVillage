<?php

namespace App\Livewire\Marketplace;

use App\Models\MarketplaceOrder;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending_pickup';

    public string $memberQrLookup = '';

    public ?int $selectedMemberId = null;

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'qr-code-scanned' => 'onQrCodeScanned',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->canAccessAdminMarketplace(), 403);
    }

    public function onQrCodeScanned($value = null): void
    {
        if ($value === null) {
            return;
        }
        if (is_array($value)) {
            $value = $value[0] ?? reset($value);
        }
        $this->memberQrLookup = trim((string) $value);
        $this->lookupMember();
    }

    public function lookupMember(): void
    {
        $code = trim($this->memberQrLookup);
        if ($code === '') {
            $this->selectedMemberId = null;

            return;
        }

        $member = User::query()
            ->where('user_type', 'member')
            ->where('qr_code', $code)
            ->first();

        $this->selectedMemberId = $member?->id;
        if (! $member) {
            $this->dispatch('notify', type: 'error', message: 'No member found with this QR code.');
        }
    }

    public function clearMember(): void
    {
        $this->memberQrLookup = '';
        $this->selectedMemberId = null;
    }

    public function fulfill(int $orderId): void
    {
        abort_unless(auth()->user()?->can('marketplace.edit'), 403);

        try {
            $order = MarketplaceOrder::query()->findOrFail($orderId);
            $order->fulfillByAdmin(auth()->user());
            $this->dispatch('notify', type: 'success', message: 'Order marked as collected.');
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function cancelOrder(int $orderId, ?string $notes = null): void
    {
        abort_unless(auth()->user()?->can('marketplace.edit'), 403);

        try {
            $order = MarketplaceOrder::query()->findOrFail($orderId);
            $order->cancelByAdmin(auth()->user(), $notes);
            $this->dispatch('notify', type: 'success', message: 'Order cancelled and points refunded.');
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $query = MarketplaceOrder::query()
            ->with(['user', 'orderItems.marketplaceItem', 'fulfilledByUser'])
            ->orderByDesc('updated_at');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->selectedMemberId) {
            $query->where('user_id', $this->selectedMemberId);
        }

        $orders = $query->paginate(15);

        $selectedMember = $this->selectedMemberId
            ? User::query()->find($this->selectedMemberId)
            : null;

        return view('livewire.marketplace.orders', [
            'orders' => $orders,
            'selectedMember' => $selectedMember,
        ])->layout('layouts.app');
    }
}
