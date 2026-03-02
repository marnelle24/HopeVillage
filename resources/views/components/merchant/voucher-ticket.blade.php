@props([
    'voucher',
    'type' => 'merchant', // merchant | admin
    'merchantLabel' => null,
    'statusLabel' => null,
    'statusClass' => null,
])

@php
    $isAdmin = $type === 'admin';
    $leftBg = $isAdmin ? 'bg-teal-500' : 'bg-orange-500';
    $leftIconBg = $isAdmin ? 'bg-teal-100 text-teal-600' : 'bg-orange-100 text-orange-600';
    $merchantLabel = $merchantLabel
        ?? ($isAdmin ? 'Admin Voucher' : optional($voucher->merchant)->name);
    $merchantLabel = filled($merchantLabel) ? $merchantLabel : 'All Sellers';

    $isValidVoucher = method_exists($voucher, 'isValid') ? (bool) $voucher->isValid() : false;
    $computedStatusLabel = $statusLabel ?? ((($voucher->is_active ?? false) && $isValidVoucher) ? 'Active' : 'Inactive');
    $computedStatusClass = $statusClass
        ?? (((($voucher->is_active ?? false) && $isValidVoucher) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));

    if ($isAdmin) {
        if (!is_null($voucher->points_cost ?? null)) {
            $valueText = 'Exchange ' . number_format((int) $voucher->points_cost) . ' points';
        } else {
            $valueText = 'Admin reward voucher';
        }
    } else {
        if (($voucher->discount_type ?? null) === 'percentage') {
            $valueText = rtrim(rtrim((string) ($voucher->discount_value ?? 0), '0'), '.') . '% off';
        } elseif (($voucher->discount_type ?? null) === 'item') {
            $valueText = '$' . number_format((float) ($voucher->discount_value ?? 0), 2) . ' item value';
        } else {
            $valueText = '$' . number_format((float) ($voucher->discount_value ?? 0), 2) . ' off';
        }
    }

    $secondaryText = null;
    if (!$isAdmin && !is_null($voucher->min_purchase ?? null)) {
        $secondaryText = 'Min. Spend $' . number_format((float) $voucher->min_purchase, 0);
    } elseif ($isAdmin && filled($voucher->description ?? null)) {
        $secondaryText = $voucher->description;
    }

    $expiryText = !empty($voucher->valid_until)
        ? \Carbon\Carbon::parse($voucher->valid_until)->format('d M Y g:i A')
        : null;
@endphp

<div class="{{ $isAdmin ? 'bg-teal-50' : 'bg-orange-50' }} rounded-lg border border-gray-200 overflow-hidden shadow-xs w-full">
    <div class="flex min-h-32">
        <div class="w-[85px] min-w-[85px] {{ $leftBg }} text-white px-2 py-3 flex flex-col items-center justify-center text-center relative">
            <div class="absolute -left-1 top-0 bottom-0 w-2 bg-white mask-[radial-gradient(circle_at_center,transparent_4px,black_5px)] mask-size-[8px_12px] mask-repeat-y"></div>
            <div class="h-8 w-8 rounded-full {{ $leftIconBg }} flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1.5 11h11L19 7M9 11v4m6-4v4"></path>
                </svg>
            </div>
            <p class="text-[0.5rem] mt-2 leading-tight">{{ $merchantLabel }}</p>
        </div>

        <div class="flex p-3 flex-col items-start justify-between gap-3 w-full">
            <div class="min-w-0">
                <p class="text-md font-semibold text-gray-900 leading-tight line-clamp-1">{{ $voucher->name }}</p>
                <p class="text-xs text-gray-700 mt-1">{{ $valueText }}</p>

                @if($secondaryText)
                    <p class="text-gray-600 mt-1 text-[0.7rem] {{ $isAdmin ? 'italic' : '' }}">{{ $secondaryText }}</p>
                @elseif(filled($voucher->description ?? null))
                    <p class="text-gray-500 mt-1 text-[0.7rem] line-clamp-2">{{ $voucher->description }}</p>
                @endif
            </div>

            <div class="flex items-center justify-between w-full pr-2 gap-3">
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs px-2 py-1 rounded-full border border-gray-300 {{ $computedStatusClass }}">
                        {{ $computedStatusLabel }}
                    </span>

                    @isset($actions)
                        {{ $actions }}
                    @endisset
                </div>

                @if($expiryText)
                    <p class="text-xs text-gray-500 text-right">
                        <span class="block text-gray-500 font-normal text-[0.7rem] uppercase">Expiry</span>
                        <span class="block text-gray-700 font-normal text-[0.8rem] tracking-wide">{{ $expiryText }}</span>
                    </p>
                @endif
            </div>

            @isset($footer)
                <div class="w-full border-t border-gray-200 pt-2">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
