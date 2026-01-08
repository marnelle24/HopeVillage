<div 
    wire:poll.5s
    class="space-y-8"
>
    <!-- Merchant Vouchers Section -->
    <div>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Merchant Vouchers</h2>
                <p class="text-xs text-gray-600">Claim these vouchers from our partner merchants</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->claimableVouchers as $voucher)
                <div class="group relative border border-gray-200 rounded-2xl bg-white overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Voucher Image -->
                    @if($voucher->image_url)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute top-3 right-3">
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Available</span>
                            </div>
                        </div>
                    @else
                        <div class="relative h-48 bg-gradient-to-br from-orange-100 via-orange-50 to-yellow-50 flex items-center justify-center">
                            <div class="text-center">
                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mx-auto text-orange-400 mb-2">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                                </svg>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Available</span>
                            </div>
                        </div>
                    @endif

                    <!-- Voucher Content -->
                    <div class="p-4">
                        <div class="mb-2">
                            <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">{{ $voucher->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $voucher->merchant->name ?? 'Merchant' }}</p>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $voucher->description }}</p>
                        
                        <!-- Discount Info -->
                        <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-600">Discount</span>
                                <span class="font-bold text-orange-600">
                                    @if($voucher->discount_type === 'percentage')
                                        {{ $voucher->discount_value }}% OFF
                                    @else
                                        ${{ number_format($voucher->discount_value, 2) }} OFF
                                    @endif
                                </span>
                            </div>
                            @if($voucher->min_purchase)
                                <p class="text-xs text-gray-500 mt-1">Min. purchase: ${{ number_format($voucher->min_purchase, 2) }}</p>
                            @endif
                        </div>

                        <!-- Validity -->
                        @if($voucher->valid_until)
                            <div class="mb-4 flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Valid until {{ $voucher->valid_until->format('M d, Y') }}</span>
                            </div>
                        @endif

                        <!-- Claim Button -->
                        <button
                            type="button"
                            wire:click="claim({{ $voucher->id }})"
                            wire:loading.attr="disabled"
                            wire:target="claim({{ $voucher->id }})"
                            class="w-full px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="claim({{ $voucher->id }})">Claim Voucher</span>
                            <span wire:loading wire:target="claim({{ $voucher->id }})" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Claiming...
                            </span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h7zm-7 4a2 2 0 100 4h7a2 2 0 100-4M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No claimable vouchers available</p>
                    <p class="text-sm text-gray-400 mt-1">Check back later for new vouchers</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Admin Vouchers Section -->
    <div class="mt-12">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Points Exchange Vouchers</h2>
                <p class="text-xs text-gray-600">Exchange your points for exclusive vouchers</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->claimableAdminVouchers as $adminVoucher)
                <div class="group relative border border-gray-200 rounded-2xl bg-white overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Voucher Image -->
                    @if($adminVoucher->image_url)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $adminVoucher->image_url }}" alt="{{ $adminVoucher->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute top-3 right-3">
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs font-semibold rounded-full">Points Exchange</span>
                            </div>
                        </div>
                    @else
                        <div class="relative h-48 bg-gradient-to-br from-purple-100 via-purple-50 to-pink-50 flex items-center justify-center">
                            <div class="text-center">
                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mx-auto text-purple-400 mb-2">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                                </svg>
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs font-semibold rounded-full">Points Exchange</span>
                            </div>
                        </div>
                    @endif

                    <!-- Voucher Content -->
                    <div class="p-4">
                        <div class="mb-2">
                            <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">{{ $adminVoucher->name }}</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $adminVoucher->description }}</p>
                        
                        <!-- Points Cost -->
                        <div class="mb-4 p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                    Points Required
                                </span>
                                <span class="font-bold text-purple-600">{{ number_format($adminVoucher->points_cost) }}</span>
                            </div>
                        </div>

                        <!-- Redeemable At -->
                        @if($adminVoucher->merchants->isNotEmpty())
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 mb-1">Redeemable at:</p>
                                <p class="text-xs text-gray-700 font-medium">{{ $adminVoucher->merchants->pluck('name')->join(', ') }}</p>
                            </div>
                        @endif

                        <!-- Validity -->
                        @if($adminVoucher->valid_until)
                            <div class="mb-4 flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Valid until {{ $adminVoucher->valid_until->format('M d, Y') }}</span>
                            </div>
                        @endif

                        <!-- Claim Button -->
                        @auth
                            @php
                                $user = auth()->user();
                                $hasEnoughPoints = $user && $user->total_points >= $adminVoucher->points_cost;
                            @endphp
                            <button
                                type="button"
                                wire:click="claimAdminVoucher({{ $adminVoucher->id }})"
                                wire:loading.attr="disabled"
                                wire:target="claimAdminVoucher({{ $adminVoucher->id }})"
                                @class([
                                    'w-full px-4 py-3 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed',
                                    'bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700' => $hasEnoughPoints,
                                    'bg-gray-300 text-gray-500 cursor-not-allowed' => !$hasEnoughPoints,
                                ])
                                @if(!$hasEnoughPoints) disabled title="Insufficient points. You need {{ number_format($adminVoucher->points_cost) }} points." @endif
                            >
                                @if($hasEnoughPoints)
                                    <span wire:loading.remove wire:target="claimAdminVoucher({{ $adminVoucher->id }})">Exchange {{ number_format($adminVoucher->points_cost) }} Points</span>
                                    <span wire:loading wire:target="claimAdminVoucher({{ $adminVoucher->id }})" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Exchanging...
                                    </span>
                                @else
                                    Insufficient Points
                                @endif
                            </button>
                        @else
                            <button
                                type="button"
                                disabled
                                class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed"
                            >
                                Login to Claim
                            </button>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No points exchange vouchers available</p>
                    <p class="text-sm text-gray-400 mt-1">Check back later for new vouchers</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

