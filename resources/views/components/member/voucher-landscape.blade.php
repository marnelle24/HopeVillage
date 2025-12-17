@php
    $days = $daysRemaining;
    $isExpired = is_int($days) && $days < 0;
    $isToday = $days === 0;
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm {{ $dimmed ? 'opacity-70' : '' }}">
    <div class="flex items-start space-x-6">
        <div class="flex flex-col items-center justify-center">
            <svg class="h-16" fill="#000000" viewBox="0 0 32 32" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g transform="matrix(1,0,0,1,-48,-240)"> <path d="M77,253C75.344,253 74,254.344 74,256C74,257.656 75.344,259 77,259L77,263C77,264.105 76.105,265 75,265C70.157,265 57.843,265 53,265C51.895,265 51,264.105 51,263C51,261.255 51,259 51,259C52.656,259 54,257.656 54,256C54,254.344 52.656,253 51,253L51,249C51,247.895 51.895,247 53,247C57.843,247 70.157,247 75,247C76.105,247 77,247.895 77,249C77,250.745 77,253 77,253Z" style="fill:#90e0ef;"></path> <path d="M77,254C77.552,254 78,253.552 78,253L78,249C78,247.343 76.657,246 75,246L53,246C51.343,246 50,247.343 50,249L50,253C50,253.552 50.448,254 51,254C52.104,254 53,254.896 53,256C53,257.104 52.104,258 51,258C50.448,258 50,258.448 50,259L50,263C50,264.657 51.343,266 53,266L75,266C76.657,266 78,264.657 78,263L78,259C77.99,258.412 77.628,258.103 77,258C75.896,258 75,257.104 75,256C75,254.896 75.896,254 77,254ZM70,248L70,250C70,250.552 69.552,251 69,251C68.448,251 68,250.552 68,250L68,248L53,248C52.448,248 52,248.448 52,249C52,249 52,252.126 52,252.126C53.724,252.571 55,254.137 55,256C55,257.863 53.724,259.429 52,259.874C52,259.874 52,263 52,263C52,263.552 52.448,264 53,264L68,264L68,262C68,261.448 68.448,261 69,261C69.552,261 70,261.448 70,262L70,264C70,264 75,264 75,264C75.552,264 76,263.552 76,263L76,259.874C74.276,259.429 73,257.862 73,256C73,254.137 74.276,252.571 76,252.126L76,249C76,248.448 75.552,248 75,248L70,248ZM68,254L68,258C68,258.552 68.448,259 69,259C69.552,259 70,258.552 70,258L70,254C70,253.448 69.552,253 69,253C68.448,253 68,253.448 68,254Z" style="fill:#1990a7;"></path> </g> </g>
            </svg>
            <p class="text-[9px] text-gray-500 font-mono truncate">{{ $voucher->voucher_code }}</p>
        </div>
        <div class="flex-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $voucher->name }}</p>
                    <p class="mt-0.5 text-xs text-gray-500 truncate">
                        {{ $voucher->merchant?->name ?? 'Merchant' }}
                    </p>
                </div>
        
                @if($redeemed)
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-slate-100 text-slate-800 border border-slate-200">
                        Redeemed
                    </span>
                @elseif(is_null($days))
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-700 border border-gray-200">
                        No expiry
                    </span>
                @elseif($isExpired)
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200">
                        Expired
                    </span>
                @elseif($isToday)
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Last day
                    </span>
                @else
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $days }} day{{ $days === 1 ? '' : 's' }} left
                    </span>
                @endif
            </div>
        
            @if($voucher->description)
                <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $voucher->description }}</p>
            @endif
        
            <div class="mt-4 flex items-center justify-between gap-3">
                @if (trim($slot) !== '')
                    <div class="shrink-0">
                        {{ $slot }}
                    </div>
                @endif
                @if($redeemed)
                    <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg bg-slate-100 text-slate-800 border border-slate-200">
                        Redeemed @if($redeemedDate) • {{ $redeemedDate }}@endif
                    </span>
                @endif
            </div>
        </div>
    </div>

</div>


