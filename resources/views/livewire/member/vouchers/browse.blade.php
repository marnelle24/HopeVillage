<div class="space-y-8">
    <!-- Claimable -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Claimable Vouchers</p>
            <p class="text-xs text-gray-600">Available now (swipe right for your claimed vouchers).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->claimableVouchers as $voucher)
                <x-member.voucher-landscape :voucher="$voucher">
                    <button
                        type="button"
                        x-data
                        @click.prevent.stop="if (confirm('Claim this voucher?')) { $wire.claim({{ $voucher->id }}) }"
                        class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-orange-500 text-white hover:bg-orange-600"
                    >
                        Claim
                    </button>
                </x-member.voucher-landscape>
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
                <x-member.voucher-landscape :voucher="$voucher" />
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No active vouchers found.
                </div>
            @endforelse
        </div>
    </div>
</div>


