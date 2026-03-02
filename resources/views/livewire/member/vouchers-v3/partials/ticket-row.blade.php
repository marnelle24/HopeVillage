@props([
    'item',
    'tab',
    'userPoints' => 0,
])

@php
    $isAdmin = $item->type === 'admin';
    $accent = $isAdmin ? 'teal' : 'orange';
    $leftBg = $isAdmin ? 'bg-teal-500' : 'bg-orange-500';
    $leftIconBg = $isAdmin ? 'bg-teal-100 text-teal-600' : 'bg-orange-100 text-orange-600';
    $btnBorder = $isAdmin ? 'border-teal-500 text-teal-600' : 'border-orange-500 text-orange-600';
    $merchantLabel = $item->merchant_name ?: 'All Sellers';
@endphp

<div class="{{ $isAdmin ? 'bg-teal-50' : 'bg-orange-50' }} rounded-lg border border-gray-200 overflow-hidden shadow-xs w-full">
    <div class="flex min-h-32">
        <div class="w-[85px] min-w-[85px] {{ $leftBg }} text-white px-2 py-3 flex flex-col items-center justify-center text-center relative">
            <div class="absolute -left-1 top-0 bottom-0 w-2 bg-white mask-[radial-gradient(circle_at_center,transparent_4px,black_5px)] mask-size-[8px_12px] mask-repeat-y"></div>
            <div class="h-8 w-8 rounded-full {{ $leftIconBg }} flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1.5 11h11L19 7M9 11v4m6-4v4"></path>
                </svg>
            </div>
            <p class="text-[0.5rem] mt-2 leading-tight">{{ $merchantLabel }}</p>
        </div>

        <div class="flex p-3 flex-col items-start justify-between gap-3 w-full">
            <div class="min-w-0">
                <p class="text-md font-semibold text-gray-900 leading-tight line-clamp-1">{{ $item->name }}</p>

                @if($tab === 'active')
                    @if($isAdmin)
                        <p class="text-xs text-gray-700 mt-1">Exchange {{ number_format($item->points_cost ?? 0) }} points</p>
                    @elseif($item->discount_type === 'percentage')
                        <p class="text-xs text-gray-700 mt-1">{{ rtrim(rtrim((string) $item->discount_value, '0'), '.') }}% off</p>
                    @else
                        <p class="text-xs text-gray-700 mt-1">${{ number_format((float) $item->discount_value, 2) }} off</p>
                    @endif

                    @if(!$isAdmin && !is_null($item->min_purchase))
                        <p class="text-gray-600 mt-1 text-[0.7rem]">Min. Spend ${{ number_format((float) $item->min_purchase, 0) }}</p>
                    @elseif($isAdmin && $item->description)
                        <p class="text-gray-400 mt-1 text-[0.7rem] italic">{{ $item->description }}</p>
                    @endif
                @endif

                @if($tab === 'claimed')
                    <p class="text-gray-600 mt-1 text-[0.7rem] tracking-normal">Claimed on {{ $item->claimed_at ? \Carbon\Carbon::parse($item->claimed_at)->format('d M Y g:i A') : 'N/A' }}</p>
                    <p class="text-[0.7rem] text-gray-500 mt-1">Tap Use Now to show QR code</p>
                @endif

                @if($tab === 'redeemed')
                    <p class="text-gray-600 mt-1 text-[0.7rem]">Redeemed on {{ $item->redeemed_at ? \Carbon\Carbon::parse($item->redeemed_at)->format('d M Y g:i A') : 'N/A' }}</p>
                    @if(!empty($item->redeemed_at_merchant))
                        <p class="text-[0.7rem] text-gray-500 mt-1">Used at {{ $item->redeemed_at_merchant }}</p>
                    @endif
                @endif
            </div>

            <div class="flex items-center justify-between w-full pr-2">
                <div class="flex items-center gap-2 shrink-0">
                    @if($tab === 'active')
                        @if($isAdmin)
                            @php $hasEnoughPoints = $userPoints >= (int) ($item->points_cost ?? 0); @endphp
                            <button
                                type="button"
                                wire:click="claimAdminVoucher({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="claimAdminVoucher({{ $item->id }})"
                                @class([
                                    'py-2 px-4 rounded-full border text-xs font-semibold transition-colors shadow-sm',
                                    $btnBorder => $hasEnoughPoints,
                                    'border-gray-300 text-gray-400 cursor-not-allowed' => !$hasEnoughPoints,
                                ])
                                @if(!$hasEnoughPoints) disabled @endif
                            >
                                @if($hasEnoughPoints)
                                    <span wire:loading.remove wire:target="claimAdminVoucher({{ $item->id }})">Claim Voucher</span>
                                    <span wire:loading wire:target="claimAdminVoucher({{ $item->id }})">...</span>
                                @else
                                    No<br>Pts
                                @endif
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="claim({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="claim({{ $item->id }})"
                                class="py-2 px-4 rounded-full border {{ $btnBorder }} text-xs font-semibold transition-colors shadow-sm"
                            >
                                <span wire:loading.remove wire:target="claim({{ $item->id }})">Claim Voucher</span>
                                <span wire:loading wire:target="claim({{ $item->id }})">...</span>
                            </button>
                        @endif
                    @elseif($tab === 'claimed')
                        <button
                            type="button"
                            wire:click="showClaimedQr('{{ $item->voucher_code }}', '{{ $item->type }}')"
                            class="py-2 px-4 rounded-full border {{ $btnBorder }} text-xs font-semibold shadow-sm"
                        >
                            Use Now
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            class="py-2 px-4 border {{ $isAdmin ? 'bg-teal-100 text-teal-600 border-teal-600/50' : 'bg-orange-100 text-orange-600 border-orange-600/50' }} rounded-full text-xs font-semibold cursor-not-allowed"
                        >
                            Used
                        </button>
                    @endif
                </div>
                @if($tab !== 'redeemed' && !empty($item->valid_until))
                    <p class="text-xs text-gray-500 text-right">
                        <span class="block text-gray-500 font-normal text-[0.55rem] uppercase">Expiry</span>
                        <span class="block text-gray-700 font-normal text-[0.6rem] tracking-wide">{{ \Carbon\Carbon::parse($item->valid_until)->format('d M Y g:i A') }}</span>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
