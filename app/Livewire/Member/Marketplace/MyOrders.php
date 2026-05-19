<?php

namespace App\Livewire\Member\Marketplace;

use App\Models\MarketplaceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrders extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $user = auth()->user();

        $orders = MarketplaceOrder::query()
            ->where('user_id', $user->id)
            ->where('status', '!=', MarketplaceOrder::STATUS_CART)
            ->with(['orderItems.marketplaceItem'])
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('livewire.member.marketplace.my-orders', [
            'orders' => $orders,
        ])->layout('layouts.app', [
            'title' => __('My marketplace orders'),
        ]);
    }
}
