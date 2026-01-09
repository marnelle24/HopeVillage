@php

    if($type === 'merchant') {
        // $voucher = App\Models\Voucher::where('voucher_code', $value)->first();
        $voucher = auth()->user()->vouchers()->where('vouchers.voucher_code', $value)->withPivot('status', 'claimed_at', 'redeemed_at')->first();
        $status = $voucher->pivot->status;
        $claimed_at = $voucher->pivot->claimed_at;
        $redeemed_at = $voucher->pivot->redeemed_at;
        $merchant = $voucher->merchant;
    } else {
        // $voucher = App\Models\AdminVoucher::where('voucher_code', $value)->first();
        $voucher = auth()->user()->adminVouchers()->where('admin_vouchers.voucher_code', $value)->withPivot('status', 'claimed_at', 'redeemed_at')->first();
        $status = $voucher->pivot->status;
        $claimed_at = $voucher->pivot->claimed_at;
        $redeemed_at = $voucher->pivot->redeemed_at;
        $merchants = $voucher->merchants;
    }
@endphp

<div
    x-data="{ showQr: @entangle('showQr'), showOverlay: false }"
    class="shrink-0"
>

    @if($type === 'merchant')
        <div 
            @mouseleave="showOverlay = false"
            class="relative h-full border border-orange-200 group group-hover:border-orange-400/70 rounded-2xl bg-white overflow-hidden opacity-90 hover:opacity-100 transition-all duration-300 hover:shadow-lg">
            <div 
                @click.stop="showOverlay ? $wire.redeem() : (showOverlay = true)"
                :class="showOverlay ? 'bg-black/40' : ''"
                class="absolute z-50 inset-0 flex items-center justify-center hover:bg-black/40 transition-all duration-300 cursor-pointer">
                <span 
                    :class="showOverlay ? 'opacity-100 -translate-y-1' : 'opacity-0'"
                    class="text-white text-lg drop-shadow-lg font-bold transition-all duration-300 hover:opacity-100 hover:-translate-y-1">
                    Redeem Now
                </span>
            </div>
            <div class="relative h-20 bg-gradient-to-br from-orange-400 via-orange-300 to-orange-200 flex items-center justify-center">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-600/50">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                </svg>
                @if($voucher && $voucher->valid_until)
                    @php
                        $daysLeft = round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($voucher->valid_until), false));
                    @endphp
                    <div class="absolute top-2 right-2">
                        @if($daysLeft > 0)
                            <span class="px-2 py-1 bg-gray-500 text-white text-[11px] font-semibold rounded-full">{{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }} left</span>
                        @else
                            <span class="px-2 py-1 bg-red-500 text-white text-[11px] font-semibold rounded-full">Expired</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-3 bg-gradient-to-b from-white to-orange-50/30">
                <h3 class="font-bold text-sm text-gray-800 mb-1 line-clamp-1">{{ $voucher->name }}</h3>
                @if($merchant && $merchant->name)
                    <p class="text-xs text-gray-600 mb-2">You can redeem this voucher at <span class="font-bold">{{ $merchant->name }}</span></p>
                @endif
                <div class="flex items-center justify-start gap-1 text-[11px] text-gray-500 mt-4 pt-3 border-t border-orange-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Claimed on {{ $claimed_at ? \Carbon\Carbon::parse($claimed_at)->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($type === 'admin')
        <div 
            @mouseleave="showOverlay = false"
            class="relative h-full border border-blue-200 group group-hover:border-blue-400/70 rounded-2xl bg-white overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div 
                @click.stop="showOverlay ? $wire.redeem() : (showOverlay = true)"
                :class="showOverlay ? 'bg-black/40' : ''"
                class="absolute z-50 inset-0 flex items-center justify-center hover:bg-black/40 transition-all duration-300 cursor-pointer">
                <span 
                    :class="showOverlay ? 'opacity-100 -translate-y-1' : 'opacity-0'"
                    class="text-white text-lg drop-shadow-lg font-bold transition-all duration-300 hover:opacity-100 hover:-translate-y-1">
                    Redeem Now
                </span>
            </div>
            <div class="relative h-20 bg-gradient-to-br from-blue-400 via-blue-300 to-blue-200 flex items-center justify-center">
                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-600/50">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                </svg>
                @if($voucher && $voucher->valid_until)
                    @php
                        $daysLeft = round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($voucher->valid_until), false));
                    @endphp
                    <div class="absolute top-2 right-2 z-0">
                        @if($daysLeft > 0)
                            <span class="px-2 py-1 bg-gray-500 text-white text-[11px] font-semibold rounded-full">{{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }} left</span>
                        @else
                            <span class="px-2 py-1 bg-red-500 text-white text-[11px] font-semibold rounded-full">Expired</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-3 bg-gradient-to-b from-white to-blue-50/30">
                <h3 class="font-bold text-sm text-gray-800 mb-1 line-clamp-1">{{ $voucher->name }}</h3>
                {{-- add here the merchant name if available --}}
                @if($merchants && $merchants->isNotEmpty())
                    <p class="text-xs text-gray-600 mb-2">You can redeem this voucher at <span class="font-bold">{{ $merchants->pluck('name')->join(', ') }}</span></p>
                @endif
                <div class="flex items-center justify-start gap-1 text-[11px] text-gray-500 mt-4 pt-3 border-t border-blue-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Claimed on {{ $claimed_at ? \Carbon\Carbon::parse($claimed_at)->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    @endif
    <template x-teleport="body">
        <div
            x-cloak
            x-show="showQr"
            x-transition.opacity
            class="fixed inset-0 z-[90] bg-black/80 flex items-center justify-center p-6"
            @click="showQr = false"
            @keydown.escape.window="showQr = false"
        >
            <div class="bg-white w-full max-w-lg flex flex-col items-center justify-center rounded-2xl p-5" @click.stop>
                {{-- @if($voucher && $voucher->name)
                    <p class="text-sm text-gray-800">
                        Redeem 
                        <span class="font-bold">{{ $voucher->name }} </span>
                        voucher
                        @if($merchant && $merchant->name)
                            from
                            <span class="font-bold">{{ $merchant->name }}</span>
                        @endif
                    </p>
                @elseif($adminVoucher && $adminVoucher->name)
                    <p class="text-sm text-gray-800">
                        Redeem 
                        <span class="font-bold">{{ $adminVoucher->name }} </span>
                        voucher
                        @if($merchants && $merchants->isNotEmpty())
                            from
                            <span class="font-bold">{{ $merchants->pluck('name')->join(', ') }}</span>
                        @endif
                    </p>
                @endif --}}
                @if($qrImage)
                    <img src="{{ $qrImage }}" alt="Voucher QR" class="w-64">
                @endif
                <p class="mt-2 text-xs text-gray-600 text-center font-bold">{{ $value }}</p>
            </div>
        </div>
    </template>
</div>


