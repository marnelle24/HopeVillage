<div>
    <div
        x-data="{ 
            open: @entangle('open').live,
            success: @entangle('success').live,
            init() {
                // Auto-close modal after 2 seconds on success
                this.$watch('success', (value) => {
                    if (value) {
                        setTimeout(() => {
                            $wire.close();
                        }, 2000);
                    }
                });
            }
        }"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
        @keydown.escape.window="$wire.close()"
        @click.self="$wire.close()"
        style="display: none;"
    >
        <div
            @click.stop
            class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative z-[10000]"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Close button -->
            <button
                @click="$wire.close()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition"
                aria-label="Close"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="pr-8 min-h-[200px] flex items-center justify-center">
                <!-- Processing Message -->
                @if($processing)
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Processing voucher redemption...</p>
                </div>
                @elseif($success)
                <!-- Success Message -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-2xl text-green-800 font-bold mb-4">SUCCESS</p>
                </div>
                @elseif($error)
                <!-- Error Message -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <p class="text-2xl text-red-800 font-bold mb-4">FAIL</p>
                    <p class="text-sm text-red-700 px-4">{{ $error }}</p>
                    
                    <!-- Action Buttons -->
                    <div class="mt-6">
                        <button
                            @click="$wire.close()"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                        >
                            Close
                        </button>
                    </div>
                </div>
                @elseif($voucher)
                <!-- Voucher Information (for merchants or when voucher is loaded) -->
                <div class="text-center py-4 w-full">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $voucher->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $voucher->description }}</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-1">Voucher Code</p>
                        <p class="text-lg font-mono font-bold text-gray-900">{{ $voucher->voucher_code }}</p>
                    </div>
                    
                    <div class="flex items-center justify-center gap-4 mb-4">
                        {{-- <div>
                            <p class="text-xs text-gray-500">Discount</p>
                            <p class="text-lg font-bold text-indigo-600">
                                {{ $voucher->discount_type === 'percentage' ? $voucher->discount_value . '%' : '$' . number_format($voucher->discount_value, 2) }}
                            </p>
                        </div> --}}
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-lg font-bold {{ $voucher->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $voucher->is_active ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    </div>
                    
                    @if($redeemer)
                        <!-- Redeemer Information -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-4 border border-blue-200">
                            <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Redeemed By</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $redeemer->name }}
                                    </p>
                                </div>
                                @if($redeemer->fin)
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    <p class="text-sm text-gray-700 font-mono">{{ $redeemer->fin }}</p>
                                </div>
                                @endif
                                @if($redeemer->email)
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-700">{{ $redeemer->email }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @elseif($redeemerQrCode)
                        <!-- Redeemer QR Code but user not found -->
                        <div class="bg-yellow-50 rounded-lg p-4 mb-4 border border-yellow-200">
                            <p class="text-xs font-semibold text-yellow-800 mb-1">Redeemer QR Code</p>
                            <p class="text-sm text-yellow-700 font-mono">{{ $redeemerQrCode }}</p>
                            <p class="text-xs text-yellow-600 mt-2">User not found with this QR code.</p>
                        </div>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->isMerchantUser())
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">Merchant View: Voucher information displayed.</p>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="mt-6 flex gap-3 justify-center">
                        @if(auth()->check() && auth()->user()->isMerchantUser() && $redeemer && $voucher && $redeemerVoucherStatus === 'claimed' && !$processing && !$success)
                        <!-- REDEEM Button for Merchants -->
                        <button
                            @click="$wire.processRedeemerVoucherRedemption()"
                            :disabled="$wire.processing"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-lg hover:from-green-600 hover:to-green-700 active:scale-95 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>REDEEM</span>
                        </button>
                        @elseif($redeemer && $redeemerVoucherStatus === 'redeemed')
                        <!-- Already Redeemed Badge -->
                        <div class="px-6 py-3 bg-green-100 text-green-800 font-semibold rounded-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Already Redeemed</span>
                        </div>
                        @endif
                        
                        <button
                            @click="$wire.close()"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                        >
                            Close
                        </button>
                    </div>
                </div>
                @else
                <!-- Default/Loading state -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Loading...</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
