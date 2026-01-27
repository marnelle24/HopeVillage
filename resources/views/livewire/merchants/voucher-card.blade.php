@if($voucher)
    @php
        $isAdminVoucher = $type === 'admin';
        $overlayColor = $isAdminVoucher ? 'blue' : 'orange';
        $borderColor = $isAdminVoucher ? 'border-blue-500/60' : 'border-orange-300';
        $textColor = $isAdminVoucher ? 'text-blue-400' : 'text-orange-400';
        $hoverTextColor = $isAdminVoucher ? 'group-hover:text-blue-600' : 'group-hover:text-orange-600';
        $overlayGradient = $isAdminVoucher ? 'from-blue-500/20 to-blue-600/10' : 'from-orange-100/20 to-orange-200/10';
        
        // Calculate validity period
        $validFrom = $isAdminVoucher ? $voucher->valid_from : $voucher->valid_from;
        $validUntil = $isAdminVoucher ? $voucher->valid_until : $voucher->valid_until;
        $validDays = null;
        if ($validFrom && $validUntil) {
            $validDays = $validFrom->diffInDays($validUntil);
        }
        
        // Get status
        if ($isAdminVoucher) {
            $statusReason = $voucher->getStatusReason();
            $isExpired = $statusReason === 'Expired' || ($voucher->valid_until && $voucher->valid_until->isPast());
            $isFull = $voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit;
            $isInactive = !$voucher->is_active;
            $isDisabled = $isExpired || $isInactive;
            
            if ($isExpired) {
                $statusLabel = 'Expired';
                $statusClass = 'bg-red-100 text-red-800 border border-red-500';
            } elseif ($isFull) {
                $statusLabel = 'Full';
                $statusClass = 'bg-orange-100 text-orange-800 border border-orange-500';
            } else {
                $statusLabel = $voucher->is_active && $voucher->isValid() ? 'Active' : 'Inactive';
                $statusClass = $voucher->is_active && $voucher->isValid() ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-gray-100 text-gray-800 border border-gray-500';
            }
        } else {
            $statusReason = $voucher->getStatusReason();
            $isExpired = $statusReason === 'Expired' || ($voucher->valid_until && $voucher->valid_until->isPast());
            $isInactive = !$voucher->is_active;
            $isDisabled = $isExpired || $isInactive;
            $statusClass = $voucher->is_active && $voucher->isValid() ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-red-100 text-red-800 border border-red-500';
            $statusLabel = $voucher->is_active && $voucher->isValid() ? 'Active' : 'Inactive';
        }
        
        // Get route
        $route = $isAdminVoucher 
            ? route('admin.admin-vouchers.profile', $voucher->voucher_code)
            : route('admin.vouchers.profile', $voucher->voucher_code);
    @endphp

    @php
        $isDisabled = isset($isDisabled) ? $isDisabled : false;
    @endphp
    <div class="relative w-full bg-white overflow-hidden border-2 {{ $borderColor }} rounded-lg group {{ $isDisabled ? 'cursor-not-allowed opacity-60 grayscale' : 'hover:shadow-lg hover:-translate-y-0.5' }} transition-all duration-300">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-linear-to-br {{ $overlayGradient }} opacity-50 {{ $isDisabled ? '' : 'group-hover:opacity-70' }} transition-opacity duration-300 pointer-events-none"></div>
        
        <!-- Content -->
        <a href="{{ $isDisabled ? '#' : $route }}" class="relative block p-4 {{ $isDisabled ? 'pointer-events-none cursor-not-allowed' : '' }}">
            <div class="flex items-start gap-4">
                {{-- QR Code on the left --}}
                @if($qrCodeImage)
                    <div class="shrink-0">
                        <img src="{{ $qrCodeImage }}" alt="Voucher QR Code" class="w-20 h-20 object-contain border border-gray-300 rounded-lg bg-white">
                    </div>
                @endif
                
                {{-- Content on the right --}}
                <div class="flex-1 flex flex-col gap-3">
                    <!-- Header with Name and Status -->
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="flex-1 text-gray-900 text-2xl font-bold line-clamp-2 transition-colors duration-300">
                            {{ $voucher->name }}
                        </h3>
                        <span class="inline-flex text-xs leading-5 font-semibold rounded-full px-2.5 py-1 {{ $statusClass }} shrink-0">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <!-- Validity Period -->
                    @if($validFrom || $validUntil)
                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-medium text-gray-500">
                                Validity Period:
                                @if($isExpired)
                                    <span class="text-sm text-red-600 font-semibold">EXPIRED</span>
                                @else
                                    @if($validDays !== null)
                                        @if($validDays < 1)
                                        <span class="text-sm text-yellow-600 font-semibold">
                                            Within the day only
                                        </span>
                                        @else   
                                            <span class="text-sm text-gray-700 font-semibold">
                                                {{ $validDays }} days
                                            </span>
                                        @endif
                                    @endif
                                @endif
                            </span>
                            <div class="flex flex-col">
                                <span class="text-sm {{ $isExpired ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $validFrom ? $validFrom->format('d M Y g:i A') : 'N/A' }}
                                    @if($validFrom && $validUntil) - @endif
                                    {{ $validUntil ? $validUntil->format('d M Y g:i A') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Add the Total Cost of the Voucher (Admin Vouchers only) --}}
                    @if($isAdminVoucher)
                        @php
                            $totalRevenue = $voucher->amount_cost * $redeemedCount;
                        @endphp
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-500">Cost per Voucher:</span>
                                <span class="text-sm text-gray-700 font-semibold">
                                    ${{ number_format($voucher->amount_cost, 2) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-500">Total Revenue:</span>
                                <span class="text-sm text-green-700 font-semibold">
                                    ${{ number_format($totalRevenue, 2) }}
                                </span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
            <!-- Claimed and Redeemed Counts -->
            <div class="flex justify-between items-center gap-4 pt-4 mt-4 border-t border-gray-300">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1">
                        <svg class="size-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="text-sm text-gray-700">
                            <span class="font-semibold">{{ $claimedCount }}</span> Claimed
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="size-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span class="text-sm text-gray-600">
                            <span class="font-semibold">{{ $redeemedCount }}</span> Redeemed
                        </span>
                    </div>
                    <!-- Usage Limit -->
                    @if($voucher->usage_limit)
                        <div class="flex items-center gap-1">
                            <svg class="size-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span class="text-sm text-gray-700 font-semibold">
                                {{ $voucher->usage_count }} / {{ $voucher->usage_limit }}
                            </span>
                            <span class="text-sm font-medium text-gray-500">Usage</span>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">

                    <!-- Cost Information (Admin Vouchers only) -->
                    @if($isAdminVoucher)
                        <div class="flex items-center gap-2">
                            {{-- @if($voucher->points_cost > 0) --}}
                                <span class="inline-flex items-center gap-1 text-xs text-gray-700 bg-orange-200 rounded-lg py-1 px-2 font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                    {{ number_format($voucher->points_cost) }} Points
                                </span>
                            {{-- @endif --}}
                            {{-- @if($voucher->amount_cost > 0) --}}
                                <span class="inline-flex items-center gap-1 text-xs text-gray-700 bg-teal-200 rounded-lg py-1 px-2 font-semibold">
                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                    </svg>
                                    ${{ number_format($voucher->amount_cost, 2) }}
                                </span>
                            {{-- @endif --}}
                        </div>
                    @endif
                </div>
            </div>
        </a>
    </div>
@endif
