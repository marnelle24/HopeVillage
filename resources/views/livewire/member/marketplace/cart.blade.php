<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20 px-4 py-6 space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Your cart') }}</h2>
        <a href="{{ route('member.marketplace.index') }}" class="text-xs flex gap-1 items-center bg-orange-500 text-white px-3 py-1.5 rounded-full hover:bg-orange-600 hover:text-orange-200 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 19.5v-15m0 0l-6.75 6.75M12.75 4.5l6.75 6.75" />
            </svg>
            {{ __('Continue shopping') }}
        </a>
    </div>

    @if ($cart && $cart->orderItems->isNotEmpty())
        <div class="bg-white rounded-2xl shadow border border-gray-100 divide-y divide-gray-100">
            @foreach ($cart->orderItems as $line)
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex gap-3">
                        <div class="w-16 h-16 rounded-lg bg-gray-100 shrink-0 overflow-hidden">
                            @if ($line->marketplaceItem?->image_url)
                                <img src="{{ $line->marketplaceItem->image_url }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">{{ $line->marketplaceItem?->name ?? __('Item') }}</p>
                            <p class="text-sm text-orange-600 font-bold">{{ number_format($line->points_per_item) }} {{ __('pts') }} × {{ $line->quantity }}</p>
                            <p class="text-sm text-gray-700 font-medium">{{ __('Line total') }}: {{ number_format($line->linePointsTotal()) }} {{ __('pts') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="decrementQty({{ $line->id }})" class="px-3 py-1 rounded-full border border-gray-300 text-sm">−</button>
                        <span class="text-sm font-mono w-8 text-center">{{ $line->quantity }}</span>
                        <button type="button" wire:click="incrementQty({{ $line->id }})" class="px-3 py-1 rounded-full border border-gray-300 text-sm">+</button>
                        <button type="button" wire:click="removeLine({{ $line->id }})" class="ml-auto text-sm text-red-600 hover:underline">{{ __('Remove') }}</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">
            <div class="flex justify-between items-center">
                <span class="font-semibold text-gray-800">{{ __('Estimated total') }}</span>
                <span class="text-xl font-bold text-orange-600">{{ number_format($cart->recalculatePointsTotal()) }} {{ __('pts') }}</span>
            </div>
            <p class="text-xs text-gray-600 mt-2">{{ __('Points are deducted when you submit. Show your member QR at the counter for pickup.') }}</p>
            <button
                type="button"
                wire:click="checkout"
                class="mt-4 w-full py-3 rounded-full bg-orange-500 text-white font-bold hover:bg-orange-600"
            >
                {{ __('Submit order') }}
            </button>
        </div>
    @else
        <p class="text-center text-gray-600 py-12">{{ __('Your cart is empty.') }}</p>
        <a href="{{ route('member.marketplace.index') }}" class="block text-center text-orange-600 font-semibold">{{ __('Browse marketplace') }}</a>
    @endif

    <a href="{{ route('member.marketplace.my-orders') }}" class="block text-center text-sm text-gray-600 hover:text-orange-600">{{ __('My orders') }}</a>
</div>
