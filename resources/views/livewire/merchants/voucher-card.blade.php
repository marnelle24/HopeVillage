@if($voucher)
    @php
        $isAdminVoucher = $type === 'admin';
        $statusReason = $voucher->getStatusReason();
        $isExpired = $statusReason === 'Expired' || ($voucher->valid_until && $voucher->valid_until->isPast());
        $isInactive = !$voucher->is_active;
        $isFull = $isAdminVoucher && $voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit;

        if ($isExpired) {
            $statusLabel = 'Expired';
            $statusClass = 'bg-red-100 text-red-800';
        } elseif ($isFull) {
            $statusLabel = 'Full';
            $statusClass = 'bg-orange-100 text-orange-800';
        } elseif ($isInactive) {
            $statusLabel = 'Inactive';
            $statusClass = 'bg-gray-100 text-gray-800';
        } elseif ($voucher->isValid()) {
            $statusLabel = 'Active';
            $statusClass = 'bg-green-100 text-green-800';
        } else {
            $statusLabel = $statusReason ?? 'Inactive';
            $statusClass = 'bg-red-100 text-red-800';
        }
    @endphp

    <x-merchant.voucher-ticket
        :voucher="$voucher"
        :type="$isAdminVoucher ? 'admin' : 'merchant'"
        :merchant-label="$isAdminVoucher ? 'Admin Voucher' : optional($voucher->merchant)->name"
        :status-label="$statusLabel"
        :status-class="$statusClass"
    >
        <x-slot:footer>
            <div class="flex items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="font-semibold">{{ $claimedCount }}</span>
                    <span>Claimed</span>
                    <span class="text-gray-300">|</span>
                    <span class="font-semibold">{{ $redeemedCount }}</span>
                    <span>Redeemed</span>
                </div>
                @if($voucher->usage_limit)
                    <span class="text-gray-600 bg-gray-100 rounded-full px-2.5 py-1">
                        {{ $voucher->usage_count }}/{{ $voucher->usage_limit }} used
                    </span>
                @endif
            </div>
        </x-slot:footer>
    </x-merchant.voucher-ticket>
@endif
