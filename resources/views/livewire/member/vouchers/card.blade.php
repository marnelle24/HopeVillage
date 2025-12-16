<div
    x-data="{ flipped: false }"
    class="shrink-0"
    style="perspective: 1000px;"
>
    <div
        class="relative w-28 h-28 rounded-full"
        @click="flipped = !flipped"
        role="button"
        tabindex="0"
        @keydown.enter.prevent="flipped = !flipped"
        @keydown.space.prevent="flipped = !flipped"
        aria-label="Voucher V{{ $value }}"
    >
        <div
            class="absolute inset-0 transition-transform duration-500 [transform-style:preserve-3d]"
            :class="flipped ? '[transform:rotateY(180deg)]' : ''"
        >
            <!-- Front -->
            <div class="absolute inset-0 [backface-visibility:hidden] flex flex-col items-center justify-center rounded-full border border-[#1990a7] bg-[#90e0ef]/40 hover:bg-orange-50 transition-all duration-300">
                <svg class="size-8" fill="#000000" viewBox="0 0 32 32" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g transform="matrix(1,0,0,1,-48,-240)"> <path d="M77,253C75.344,253 74,254.344 74,256C74,257.656 75.344,259 77,259L77,263C77,264.105 76.105,265 75,265C70.157,265 57.843,265 53,265C51.895,265 51,264.105 51,263C51,261.255 51,259 51,259C52.656,259 54,257.656 54,256C54,254.344 52.656,253 51,253L51,249C51,247.895 51.895,247 53,247C57.843,247 70.157,247 75,247C76.105,247 77,247.895 77,249C77,250.745 77,253 77,253Z" style="fill:#90e0ef;"></path> <path d="M77,254C77.552,254 78,253.552 78,253L78,249C78,247.343 76.657,246 75,246L53,246C51.343,246 50,247.343 50,249L50,253C50,253.552 50.448,254 51,254C52.104,254 53,254.896 53,256C53,257.104 52.104,258 51,258C50.448,258 50,258.448 50,259L50,263C50,264.657 51.343,266 53,266L75,266C76.657,266 78,264.657 78,263L78,259C77.99,258.412 77.628,258.103 77,258C75.896,258 75,257.104 75,256C75,254.896 75.896,254 77,254ZM70,248L70,250C70,250.552 69.552,251 69,251C68.448,251 68,250.552 68,250L68,248L53,248C52.448,248 52,248.448 52,249C52,249 52,252.126 52,252.126C53.724,252.571 55,254.137 55,256C55,257.863 53.724,259.429 52,259.874C52,259.874 52,263 52,263C52,263.552 52.448,264 53,264L68,264L68,262C68,261.448 68.448,261 69,261C69.552,261 70,261.448 70,262L70,264C70,264 75,264 75,264C75.552,264 76,263.552 76,263L76,259.874C74.276,259.429 73,257.862 73,256C73,254.137 74.276,252.571 76,252.126L76,249C76,248.448 75.552,248 75,248L70,248ZM68,254L68,258C68,258.552 68.448,259 69,259C69.552,259 70,258.552 70,258L70,254C70,253.448 69.552,253 69,253C68.448,253 68,253.448 68,254Z" style="fill:#1990a7;"></path> </g> </g>
                </svg>
                <p class="text-md text-[#1990a7] drop-shadow-sm font-bold">V{{ $value }}</p>
                <p class="text-xs text-[#1990a7] drop-shadow-sm font-semibold">Voucher</p>
            </div>

            <!-- Back -->
            <div class="absolute inset-0 [backface-visibility:hidden] [transform:rotateY(180deg)] flex flex-col items-center justify-center rounded-full border border-[#1990a7] bg-slate-700 opacity-70 transition-all duration-300">
                <button
                    type="button"
                    wire:click="redeem"
                    @click.stop
                    class="mt-2 px-3 py-1.5 text-[10px] font-semibold rounded-lg bg-slate-500 border border-white hover:bg-slate-600 text-white"
                >
                    Redeem Now
                </button>
                <p class="mt-2 text-[10px] text-white">Tap to flip back</p>
            </div>
        </div>
    </div>

    @if($showQr)
        <div class="fixed inset-0 z-[80] bg-black/70 flex items-center justify-center p-4" wire:click="closeQr">
            <div class="bg-white rounded-2xl p-5 w-full max-w-sm" wire:click.stop>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-gray-800">Voucher QR Code</p>
                    <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="closeQr" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 flex flex-col items-center gap-3">
                    @if($qrImage)
                        <div class="bg-white p-3 rounded-xl border border-gray-200">
                            <img src="{{ $qrImage }}" alt="Voucher QR" class="w-64 h-64">
                        </div>
                    @endif
                    <p class="text-[11px] text-gray-500 font-mono break-all">{{ $qrPayload }}</p>
                </div>
            </div>
        </div>
    @endif
</div>


