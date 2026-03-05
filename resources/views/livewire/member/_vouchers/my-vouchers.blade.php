<div class="space-y-10">
    <!-- Claimed -->
    <div>
        <div class="mb-3 flex items-start justify-between">
            <div>
                <p class="text-xl font-bold text-gray-700 mb-1">Claimed Vouchers</p>
                <p class="text-xs text-gray-600">Tap a voucher to flip, then click Redeem to show the QR code.</p>
            </div>
            <a href="{{ route('member.vouchers', ['status' => 'my-vouchers']) }}" class="border border-indigo-600/60 rounded-lg px-2 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700">Reload</a>
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
            <p class="text-xs text-gray-600">History of vouchers you've already used.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->redeemed as $voucher)
                <x-member.voucher-landscape
                    :voucher="$voucher"
                    :dimmed="true"
                    :redeemed="true"
                    :redeemed-at="$voucher->pivot->redeemed_at"
                />
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No redeemed vouchers yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Claimed Admin Vouchers -->
    <div>
        <div class="mb-3 flex items-start justify-between">
            <div>
                <p class="text-xl font-bold text-gray-700 mb-1">Claimed Admin Vouchers</p>
                <p class="text-xs text-gray-600">Tap a voucher to flip, then click Redeem to show the QR code.</p>
            </div>
        </div>

        <div class="flex flex-nowrap gap-2 min-h-[140px] items-center overflow-x-auto overflow-y-hidden">
            @forelse($this->claimedAdminVouchers as $adminVoucher)
                @livewire('member.vouchers.card', ['value' => (string) $adminVoucher->voucher_code], key('member-claimed-admin-voucher-card-' . $adminVoucher->id))
            @empty
                <div class="border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                    No claimed admin vouchers yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Redeemed Admin Vouchers -->
    <div>
        <div class="mb-3">
            <p class="text-xl font-bold text-gray-700 mb-1">Redeemed Admin Vouchers</p>
            <p class="text-xs text-gray-600">History of admin vouchers you've already used.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->redeemedAdminVouchers as $adminVoucher)
                <div class="border border-gray-300 rounded-2xl bg-white overflow-hidden opacity-70">
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $adminVoucher->name }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $adminVoucher->description }}</p>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-semibold">
                                {{ 'Conversion: -'.number_format($adminVoucher->points_cost) }} Points
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs font-semibold">
                                Merchant: {{ $adminVoucher->pivot->redeemed_at_merchant_id ? \App\Models\Merchant::find($adminVoucher->pivot->redeemed_at_merchant_id)->name : 'N/A' }}
                            </span>
                        </div>
                        <p class="inline-flex items-center gap-1 px-2 py-1 bg-slate-200 text-slate-500 rounded text-xs font-semibold">Redeemed on: {{ $adminVoucher->pivot->redeemed_at ? \Carbon\Carbon::parse($adminVoucher->pivot->redeemed_at)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full border border-dashed border-gray-300 rounded-2xl bg-gray-50/50 p-6 text-center text-sm text-gray-500">
                    No redeemed admin vouchers yet.
                </div>
            @endforelse
        </div>
    </div>
</div>


