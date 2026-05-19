<div>
    <x-slot name="header">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-3">
            <div class="flex justify-between w-full items-center gap-3">
                {{-- <a href="{{ route('admin.marketplace.index') }}" class="text-orange-500 font-medium hover:text-orange-600 text-sm">{{ __('← Items') }}</a> --}}
                @can('marketplace.edit')
                    <a href="{{ route('admin.marketplace.cashier') }}" class="text-white bg-green-500 rounded-full px-3 py-1 font-medium hover:bg-green-600 text-sm">{{ __('Cashier Checkout') }}</a>
                @endcan
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Marketplace Order History') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-900">{{ __('Find member by QR code') }}</h3>
                <p class="text-sm text-gray-600">{{ __('For counter sales, use Marketplace Cashier (basket → member QR → points). This page lists orders: use QR below to filter by member when confirming older “pick up” orders still in pending status.') }}</p>
                <div class="flex flex-wrap gap-2">
                    <input
                        type="text"
                        wire:model="memberQrLookup"
                        wire:keydown.enter="lookupMember"
                        placeholder="{{ __('Member QR code') }}"
                        class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                    >
                    <button type="button" wire:click="lookupMember" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">{{ __('Lookup') }}</button>
                    <button type="button" @click="$dispatch('openQrScanner')" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">{{ __('Scan QR') }}</button>
                    @if ($selectedMemberId)
                        <button type="button" wire:click="clearMember" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('Clear') }}</button>
                    @endif
                </div>
                @if ($selectedMember)
                    <p class="text-sm text-green-700 font-medium">{{ __('Member') }}: {{ $selectedMember->name }} ({{ $selectedMember->qr_code }})</p>
                @elseif ($memberQrLookup !== '' && ! $selectedMemberId)
                    <p class="text-sm text-red-600">{{ __('No member found for this code.') }}</p>
                @endif
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Order status filter') }}</label>
                <select wire:model.live="statusFilter" class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="all">{{ __('All') }}</option>
                    <option value="pending_pickup">{{ __('Pending pickup') }}</option>
                    <option value="fulfilled">{{ __('Fulfilled') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse ($orders as $order)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <div class="flex flex-wrap justify-between gap-2">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-4">
                                        <p class="font-semibold text-gray-900">{{ __('Order') }} #{{ $order->id }}</p>
                                        <span class="inline-block text-xs font-semibold capitalize px-2 py-1 rounded-full
                                            @if ($order->status === 'pending_pickup') bg-amber-100 text-amber-900
                                            @elseif($order->status === 'fulfilled') bg-green-100 text-green-900
                                            @elseif($order->status === 'cancelled') bg-gray-200 text-gray-800
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $order->user?->name }} — {{ $order->user?->qr_code }}</p>
                                    <p class="text-sm text-orange-600 font-medium mt-1">Total: {{ number_format($order->points_total) }} {{ __('pts') }}</p>
                                </div>
                                <div class="text-right">
                                    @if ($order->fulfilled_at)
                                        <p class="text-xs text-gray-500 mt-1">Date: {{ $order->fulfilled_at->format('Y-m-d H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <ul class="mt-4 divide-y divide-gray-100">
                                @foreach ($order->orderItems as $line)
                                    <li class="py-2 flex justify-between text-sm">
                                        <span>{{ $line->marketplaceItem?->name ?? __('Item removed') }} × {{ $line->quantity }}</span>
                                        <span class="text-gray-600">{{ number_format($line->linePointsTotal()) }} {{ __('pts') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($order->status === 'pending_pickup')
                                @can('marketplace.edit')
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <button type="button" wire:click="fulfill({{ $order->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                            {{ __('Mark collected') }}
                                        </button>
                                        <button type="button" wire:click="cancelOrder({{ $order->id }})" wire:confirm="{{ __('Cancel this order and refund points?') }}" class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 text-sm font-medium">
                                            {{ __('Cancel & refund') }}
                                        </button>
                                    </div>
                                @endcan
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-600 col-span-full py-12 min-h-[200px] bg-gray-100 rounded-lg p-6 flex items-center justify-center">{{ __('No orders match this filter.') }}</p>
                    @endforelse
                </div>
            </div>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
