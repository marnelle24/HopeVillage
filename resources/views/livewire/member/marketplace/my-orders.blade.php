<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20 px-4 py-6 space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">{{ __('My Order History') }}</h2>
        <a href="{{ route('member.marketplace.index') }}" class="text-sm font-semibold border border-orange-600 rounded-lg px-2 py-1 text-orange-600 hover:text-orange-800">{{ __('Shop') }}</a>
    </div>

    <div class="space-y-4">
        @forelse ($orders as $order)
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <p class="text-orange-600 font-bold mt-2">
                            {{__('Total')}}: {{ number_format($order->points_total) }} {{ __('points') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $order->updated_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @php
                        $status = $order->status;
                        $statusClass = '';
                        if ($order->status === 'pending_pickup') {
                            $status = 'Pending Pickup';
                            $statusClass = 'bg-amber-100 text-amber-900';
                        } elseif ($order->status === 'fulfilled') {
                            $status = 'Fulfilled';
                            $statusClass = 'bg-green-100 text-green-900';
                        } elseif ($order->status === 'cancelled') {
                            $status = 'Cancelled';
                            $statusClass = 'bg-gray-200 text-gray-800';
                        }
                    @endphp
                    <span class="text-xs font-semibold px-2 py-1 border border-gray-200 rounded-full whitespace-nowrap {{$statusClass}}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <ul class="mt-3 text-sm text-gray-700 space-y-1">
                    @foreach ($order->orderItems as $line)
                        <li>{{ $line->marketplaceItem?->name ?? __('Item') }} × {{ $line->quantity }}</li>
                    @endforeach
                </ul>
                @if ($order->status === 'pending_pickup')
                    <p class="mt-3 text-xs text-amber-800 bg-amber-50 rounded-lg p-2">{{ __('Show your QR code at the counter so staff can mark your order as collected.') }}</p>
                @endif
            </div>
        @empty
            <p class="text-center text-gray-600 py-12">{{ __('No orders yet.') }}</p>
        @endforelse
    </div>

    <div>
        {{ $orders->links() }}
    </div>
</div>
