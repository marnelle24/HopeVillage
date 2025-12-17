<div class="space-y-8">
    <!-- Claimable -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Claimable Vouchers</p>
            <p class="text-xs text-gray-600">Available now (swipe right for your claimed vouchers).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->claimableVouchers as $voucher)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $voucher->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ $voucher->merchant?->business_name ?? 'Merchant' }}
                            </p>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Claimable
                        </span>
                    </div>

                    <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $voucher->description }}</p>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <p class="text-xs text-gray-500 font-mono">{{ $voucher->voucher_code }}</p>
                        <button
                            type="button"
                            wire:click="claim({{ $voucher->id }})"
                            class="px-3 py-2 text-xs font-semibold rounded-xl bg-orange-500 text-white hover:bg-orange-600"
                        >
                            Claim
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No claimable vouchers right now.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Active -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">All Active Vouchers</p>
            <p class="text-xs text-gray-600">Currently published by merchants (may include not-yet-valid vouchers).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->activeVouchers as $voucher)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $voucher->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ $voucher->merchant?->business_name ?? 'Merchant' }}
                            </p>
                        </div>
                        @if($voucher->isValid())
                            <span class="text-[11px] font-semibold px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Valid now
                            </span>
                        @else
                            <span class="text-[11px] font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-700 border border-gray-200">
                                Not valid yet / expired
                            </span>
                        @endif
                    </div>

                    <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $voucher->description }}</p>
                    <p class="mt-4 text-xs text-gray-500 font-mono">{{ $voucher->voucher_code }}</p>
                </div>
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No active vouchers found.
                </div>
            @endforelse
        </div>
    </div>
</div>


