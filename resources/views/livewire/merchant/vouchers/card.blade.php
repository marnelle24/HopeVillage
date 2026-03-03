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
    <x-slot:footer>
        <div class="flex flex-wrap items-center gap-2">
            @if($merchant->is_active)
                <a
                    href="{{ route('merchant.vouchers.profile', $voucher->voucher_code) }}"
                    title="View Voucher Details"
                    class="text-xs text-gray-600 hover:text-orange-600 hover:border-orange-300 transition-colors"
                >
                    Details
                </a>
                <span class="text-xs text-gray-600">|</span>
                <button
                    wire:click="edit"
                    title="Edit Voucher"
                    class="text-xs text-gray-600 hover:text-sky-600 hover:border-sky-300 transition-colors"
                >
                    Edit
                </button>
                <span class="text-xs text-gray-600">|</span>
                <button
                    wire:confirm="Are you sure you want to delete this voucher?"
                    wire:click="delete"
                    title="Delete Voucher"
                    class="text-xs text-gray-600 hover:text-red-600 hover:border-red-300 transition-colors"
                >
                    Delete
                </button>
                <span class="text-xs text-gray-600">|</span>
                <button
                    class="text-xs text-gray-600 hover:text-yellow-700 hover:border-yellow-300 transition-colors"
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
        </div>
    </x-slot:footer>
</x-merchant.voucher-ticket>

