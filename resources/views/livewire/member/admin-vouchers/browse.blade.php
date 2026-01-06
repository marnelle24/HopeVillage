<div class="space-y-8">
    <!-- Claimable Admin Vouchers -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Claimable Admin Vouchers</p>
            <p class="text-xs text-gray-600">Exchange your points for these exclusive vouchers.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->claimableAdminVouchers as $adminVoucher)
                <div class="border border-gray-300 rounded-2xl bg-white overflow-hidden hover:shadow-lg transition-shadow">
                    @if($adminVoucher->image_url)
                        <img src="{{ $adminVoucher->image_url }}" alt="{{ $adminVoucher->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-orange-400">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H0V6C1.10457 6 2 6.89543 2 8C2 9.10457 1.10457 10 0 10V14H16V10C14.8954 10 14 9.10457 14 8C14 6.89543 14.8954 6 16 6V2ZM8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" fill="currentColor"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $adminVoucher->name }}</h3>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $adminVoucher->description }}</p>
                        
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                                {{ number_format($adminVoucher->points_cost) }} Points
                            </span>
                        </div>

                        <div class="mb-3">
                            <p class="text-xs text-gray-500 mb-1">Redeemable at:</p>
                            <p class="text-xs text-gray-700 font-medium">{{ $adminVoucher->merchants->pluck('name')->join(', ') }}</p>
                        </div>

                        @auth
                            @php
                                $user = auth()->user();
                                $hasEnoughPoints = $user && $user->total_points >= $adminVoucher->points_cost;
                            @endphp
                            <button
                                type="button"
                                x-data
                                @click.prevent.stop="if (confirm('Claim this voucher for {{ number_format($adminVoucher->points_cost) }} points?')) { $wire.claim({{ $adminVoucher->id }}) }"
                                @class([
                                    'w-full px-3 py-2 text-xs font-semibold rounded-xl text-white transition',
                                    'bg-orange-500 hover:bg-orange-600' => $hasEnoughPoints,
                                    'bg-gray-400 cursor-not-allowed' => !$hasEnoughPoints,
                                ])
                                @if(!$hasEnoughPoints) disabled title="Insufficient points. You need {{ number_format($adminVoucher->points_cost) }} points." @endif
                            >
                                @if($hasEnoughPoints)
                                    Claim
                                @else
                                    Insufficient Points
                                @endif
                            </button>
                        @else
                            <button
                                type="button"
                                disabled
                                class="w-full px-3 py-2 text-xs font-semibold rounded-xl bg-gray-400 text-white cursor-not-allowed"
                            >
                                Login to Claim
                            </button>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No claimable admin vouchers right now.
                </div>
            @endforelse
        </div>
    </div>
</div>

