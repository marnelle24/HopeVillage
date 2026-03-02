@php
    if (!$voucher->is_active) {
        $status = 'Pending Approval';
        $statusClass = 'bg-yellow-100 text-yellow-800';
    } elseif ($voucher->isValid()) {
        $status = 'Active';
        $statusClass = 'bg-green-100 text-green-800';
    } else {
        $status = $voucher->getStatusReason() ?? 'Inactive';
        $statusClass = 'bg-red-100 text-red-800';
    }
@endphp

<x-merchant.voucher-ticket
    :voucher="$voucher"
    type="merchant"
    :merchant-label="$merchant->name"
    :status-label="$status"
    :status-class="$statusClass"
>
    <x-slot:actions>
        @if($merchant->is_active)
            <a
                href="{{ route('merchant.vouchers.profile', $voucher->voucher_code) }}"
                title="View Voucher Details"
                class="py-1 px-2 rounded-full border border-orange-500 text-orange-600 text-xs font-semibold transition-colors"
            >
                Details
            </a>
        @else
            <span class="py-2 px-3 rounded-full border border-gray-300 text-gray-400 text-xs font-semibold cursor-not-allowed" title="Your merchant account is pending approval">
                Locked
            </span>
        @endif
    </x-slot:actions>

    <x-slot:footer>
        <div class="flex flex-wrap items-center gap-2">
            @if($merchant->is_active)
                <button
                    wire:click="edit"
                    title="Edit Voucher"
                    class="text-sm text-gray-600 hover:text-sky-600 hover:border-sky-300 transition-colors"
                >
                    Edit
                </button>
                <span class="text-sm text-gray-600">|</span>
                <button
                    wire:confirm="Are you sure you want to delete this voucher?"
                    wire:click="delete"
                    title="Delete Voucher"
                    class="text-sm text-gray-600 hover:text-red-600 hover:border-red-300 transition-colors"
                >
                    Delete
                </button>
                <span class="text-sm text-gray-600">|</span>
                <button
                    class="text-sm text-gray-600 hover:text-yellow-700 hover:border-yellow-300 transition-colors"
                    title="Click to view QR Code: {{ $voucher->voucher_code }}"
                    @click="$dispatch('open-qr-modal', {
                        qrCode: '{{ $voucher->voucher_code }}',
                        qrImage: '{{ $qrCodeImageFull }}',
                        title: '{{ $voucher->name }}'
                    })"
                >
                    View QR Code
                </button>
            @else
                <span class="py-1.5 px-2.5 rounded-full border border-gray-300 text-xs text-gray-400 cursor-not-allowed" title="Your merchant account is pending approval">
                    Edit
                </span>
                <span class="py-1.5 px-2.5 rounded-full border border-gray-300 text-xs text-gray-400 cursor-not-allowed" title="Your merchant account is pending approval">
                    Delete
                </span>
            @endif

            @if($voucher->usage_limit)
                <span class="ml-auto text-xs text-gray-600 bg-gray-100 rounded-full px-2.5 py-1">
                    {{ $voucher->usage_count }}/{{ $voucher->usage_limit }} used
                </span>
            @endif
        </div>
    </x-slot:footer>
</x-merchant.voucher-ticket>

