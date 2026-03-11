<div 
    wire:poll.5s
    class="space-y-10"
>
    <!-- Claimed Vouchers (Merged - Merchant & Admin) -->
    <div>
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Claimed Vouchers</h2>
            <p class="text-xs text-gray-600">Your claimed vouchers and ready to redeem. <br />
                Click the voucher to show the QR code and redeem.</p>
        </div>

        <div class="flex flex-nowrap gap-4 min-h-[200px] items-start overflow-x-auto overflow-y-hidden pb-4 scrollbar-hide snap-x snap-mandatory"
            x-data="{ 
                startX: null,
                onTouchStart(e) { this.startX = e.touches?.[0]?.clientX ?? null },
                onTouchEnd(e) {
                    if (this.startX === null) return;
                    const endX = e.changedTouches?.[0]?.clientX ?? null;
                    if (endX === null) return;
                    const dx = endX - this.startX;
                    // Allow natural scrolling
                    this.startX = null;
                },
            }"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
        >
            @forelse($this->allClaimedVouchers as $voucher)
                <div class="shrink-0 w-48 snap-center">
                    @if($voucher->type === 'merchant')
                        @livewire('member.vouchers.card', ['value' => (string) $voucher->voucher_code, 'type' => 'merchant'], key('member-claimed-voucher-card-v2-' . $voucher->id))
                    @else
                        @livewire('member.vouchers.card', ['value' => (string) $voucher->voucher_code, 'type' => 'admin'], key('member-claimed-admin-voucher-card-v2-' . $voucher->id))
                    @endif
                </div>
            @empty
                <div class="w-full border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h7zm-7 4a2 2 0 100 4h7a2 2 0 100-4M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No claimed vouchers yet</p>
                    <p class="text-sm text-gray-400 mt-1">Claim vouchers from the Available tab</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Redeemed Vouchers (Merged - Merchant & Admin) -->
    <div>
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Redeemed Vouchers</h2>
            <p class="text-xs text-gray-600">History of all vouchers you've already used</p>
        </div>

        <div class="flex flex-nowrap gap-4 min-h-[280px] items-stretch overflow-x-auto overflow-y-hidden pb-4 scrollbar-hide snap-x snap-mandatory"
            x-data="{ 
                startX: null,
                onTouchStart(e) { this.startX = e.touches?.[0]?.clientX ?? null },
                onTouchEnd(e) {
                    if (this.startX === null) return;
                    const endX = e.changedTouches?.[0]?.clientX ?? null;
                    if (endX === null) return;
                    const dx = endX - this.startX;
                    // Allow natural scrolling
                    this.startX = null;
                },
            }"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
        >
            @forelse($this->allRedeemedVouchers as $voucher)
                <div class="shrink-0 w-48 snap-center">
                    @if($voucher->type === 'merchant')
                        <!-- Merchant Voucher - Orange Gradient -->
                        <div class="h-full border border-orange-200 rounded-2xl bg-white overflow-hidden opacity-90 hover:opacity-100 transition-all duration-300 hover:shadow-lg">
                            @if($voucher->image_url)
                                <div class="relative h-20 overflow-hidden">
                                    <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full h-full object-cover grayscale">
                                    <div class="absolute inset-0 bg-gradient-to-t from-orange-900/40 to-transparent"></div>
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Redeemed</span>
                                    </div>
                                </div>
                            @else
                                <div class="relative h-20 bg-gradient-to-br from-orange-400 via-orange-300 to-orange-200 flex items-center justify-center">
                                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-600">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                                    </svg>
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 bg-gray-500 text-white text-[11px] font-semibold rounded-full">Redeemed</span>
                                    </div>
                                </div>
                            @endif
                            <div class="p-3">
                                <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">{{ $voucher->name }}</h3>
                                @if($voucher->type === 'merchant' && isset($voucher->merchant))
                                    <p class="text-xs text-gray-600 mb-2">Redeemed at: {{ $voucher->merchant->name }}</p>
                                @endif
                                {{-- @if($voucher->description)
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $voucher->description }}</p>
                                @endif --}}
                                <div class="flex items-center justify-start gap-1 text-[11px] text-gray-500 mt-4 pt-3 border-t border-orange-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Redeemed on {{ $voucher->redeemed_at ? \Carbon\Carbon::parse($voucher->redeemed_at)->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Admin Voucher - Blue Gradient -->
                        <div class="h-full border border-blue-200 rounded-2xl bg-white overflow-hidden opacity-90 hover:opacity-100 transition-all duration-300 hover:shadow-lg">
                            @if($voucher->image_url)
                                <div class="relative h-20 overflow-hidden">
                                    <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full h-full object-cover grayscale">
                                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/40 to-transparent"></div>
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Redeemed</span>
                                    </div>
                                </div>
                            @else
                                <div class="relative h-20 bg-gradient-to-br from-blue-400 via-blue-300 to-blue-200 flex items-center justify-center">
                                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue-600/70">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                                    </svg>
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Redeemed</span>
                                    </div>
                                </div>
                            @endif
                            <div class="p-3">
                                <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">{{ $voucher->name }}</h3>
                                @if($voucher->type === 'admin' && isset($voucher->redeemed_merchant) && $voucher->redeemed_merchant)
                                    <p class="text-xs text-gray-600 mb-3">You redeemed this voucher at <span class="font-bold text-gray-600">{{ $voucher->redeemed_merchant->name }}</span></p>
                                @endif
                                <div class="flex items-start justify-start gap-1 text-[11px] text-gray-500 mt-4 pt-3 border-t border-blue-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="leading-tight">You redeemed this voucher on {{ $voucher->redeemed_at ? \Carbon\Carbon::parse($voucher->redeemed_at)->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="w-full border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No redeemed vouchers yet</p>
                    <p class="text-sm text-gray-400 mt-1">Your redeemed vouchers will appear here</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

