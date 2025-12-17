<div class="space-y-10">
    <!-- Claimed -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Claimed Vouchers</p>
            <p class="text-xs text-gray-600">Tap a voucher to flip, then click Redeem to show the QR code.</p>
        </div>

        <div class="flex flex-nowrap gap-2 min-h-[140px] items-center overflow-x-auto overflow-y-hidden">
            @forelse($this->claimed as $voucher)
                @livewire('member.vouchers.card', ['value' => (string) $voucher->voucher_code], key('member-claimed-voucher-card-' . $voucher->id))
            @empty
                <div class="border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                    No claimed vouchers yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Redeemed -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Redeemed Vouchers</p>
            <p class="text-xs text-gray-600">History of vouchers you’ve already used.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->redeemed as $voucher)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 opacity-70">
                    <p class="text-sm font-bold text-gray-800">{{ $voucher->name }}</p>
                    <p class="mt-0.5 text-xs text-gray-500">
                        {{ $voucher->merchant?->business_name ?? 'Merchant' }}
                    </p>
                    <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $voucher->description }}</p>
                    <p class="mt-4 text-xs text-gray-500 font-mono">{{ $voucher->voucher_code }}</p>
                </div>
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No redeemed vouchers yet.
                </div>
            @endforelse
        </div>
    </div>
</div>


