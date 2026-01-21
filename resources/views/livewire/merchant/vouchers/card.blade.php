<div class="w-full bg-white overflow-hidden border-2 border-gray-400/30 shadow-md rounded-2xl group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
    @if($voucher->image_url)
        <div class="relative overflow-hidden">
            <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full h-30 object-cover object-center border border-gray-300 rounded-t-2xl bg-white transition-all duration-300">
            <div class="absolute inset-0 bg-linear-to-t from-black/80 to-black/30 transition-all duration-300 rounded-t-2xl"></div>
            <h3 class="absolute bottom-2 left-4 text-xl font-bold text-white font-archivo-black">{{ $voucher->name }}</h3>
        </div>
    @else
        <div class="relative overflow-hidden flex-1 flex items-start h-full">
            <div class="w-full max-w-md h-30 bg-gray-200 rounded-t-2xl flex flex-col items-center justify-center border border-gray-300">
                <p class="text-gray-400/40 text-3xl font-bold font-mono">VOUCHER</p>
                <p class="text-gray-400/60 text-sm font-mono">Image</p>
            </div>
            <div class="absolute inset-0 bg-linear-to-t from-black/70 to-black/30 transition-all duration-300 rounded-t-2xl"></div>
            <h3 class="absolute bottom-2 left-4 text-xl font-bold text-white font-archivo-black">{{ $voucher->name }}</h3>
        </div>
    @endif
    <div class="flex items-center justify-between p-4">
        <div class="w-full">
            <div class="flex flex-col items-start gap-1">
                <p class="text-xs italic text-gray-600">{{ $voucher->description }}</p>

                {{-- <div class="flex gap-1 bg-purple-100 rounded-lg py-1 px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-800">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <span class="text-xs text-gray-600">
                        @if($voucher->discount_type === 'item')
                            {{ '$' . number_format($voucher->discount_value, 2) . ' worth of one item' }}
                        @elseif($voucher->discount_type === 'fixed')
                            {{ '$' . number_format($voucher->discount_value, 2) }}
                        @elseif($voucher->discount_type === 'percentage')
                            {{ $voucher->discount_value . '%' . ' Off' }}
                        @endif
                    </span>
                </div> --}}
                {{-- add the validity date range --}}
                <div class="mt-1">
                    <span class="text-xs font-bold text-gray-500">Validity</span>
                    <div class="flex flex-col items-start gap-1">
                        <div class="flex items-center gap-1">
                            <span class="text-xs italic text-gray-500">{{ $voucher->valid_from ? $voucher->valid_from->format('d M Y') : 'N/A' }}</span>
                            <span class="text-xs text-gray-600">-</span>
                            <span class="text-xs italic text-gray-500">{{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-1">
                    @php
                        if (!$voucher->is_active) {
                            $status = 'Pending Approval';
                            $statusClass = 'bg-yellow-200 text-yellow-700';
                        } elseif ($voucher->isValid()) {
                            $status = 'Active';
                            $statusClass = 'bg-green-200 text-green-700';
                        } else {
                            $statusReason = $voucher->getStatusReason();
                            $status = $statusReason ?? 'Inactive';
                            $statusClass = 'bg-yellow-200 text-yellow-700';
                        }
                    @endphp
                    <span class="flex items-center gap-1 {{ $statusClass }} mt-2 rounded-lg px-2.5 py-1 text-xs" title="{{ $voucher->is_active ? ($voucher->isValid() ? 'Voucher is active and valid' : 'Voucher is active but ' . strtolower($status)) : 'Voucher is pending administrator approval' }}">
                        <span class="text-xs">{{ $status }}</span>
                    </span>
                    @if($voucher->usage_limit)
                        <span class="flex items-center gap-1 text-gray-600 mt-2 bg-gray-200 rounded-lg py-1 px-3 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span class="text-xs">{{ $voucher->usage_count }}/{{ $voucher->usage_limit }}</span>
                        </span>
                    @endif
                </div>
                <div class="flex justify-center items-center gap-3 mt-4">
                    @if($merchant->is_active)
                        <a 
                            href="{{ route('merchant.vouchers.profile', $voucher->voucher_code) }}"
                            title="View Voucher Details"
                            class="flex items-center text-xs bg-orange-500 hover:bg-orange-600 px-2 py-1 rounded-lg active:scale-95 active:bg-orange-600 md:hover:scale-110 text-white transition-all duration-200 touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Details
                        </a>
                        <button 
                            wire:click="edit"
                            title="Edit Voucher"
                            class="flex items-center text-xs bg-gray-100 px-2 py-1 rounded-lg active:scale-95 active:bg-sky-100 active:text-sky-600 md:hover:scale-110 md:hover:text-sky-600 cursor-pointer text-gray-600 transition-all duration-200 touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            Edit
                        </button>
                        {{-- <span class="text-gray-600 text-xs">|</span> --}}
                        <button 
                            wire:confirm="Are you sure you want to delete this voucher?" 
                            title="Delete Voucher"
                            wire:click="delete"
                            class="flex items-center text-xs bg-gray-100 px-2 py-1 rounded-lg active:scale-95 active:bg-red-100 active:text-red-600 md:hover:scale-110 md:hover:text-red-600 cursor-pointer text-gray-600 transition-all duration-200 touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Delete
                        </button>
                        {{-- <span class="text-gray-600 text-xs">|</span> --}}
                        <button 
                            wire:confirm="Are you sure you want to delete this voucher?" 
                            class="flex items-center text-xs bg-gray-100 px-2 py-1 rounded-lg active:scale-95 active:bg-yellow-100 active:text-yellow-700 md:hover:scale-110 md:hover:text-yellow-700 cursor-pointer text-gray-600 transition-all duration-200 touch-manipulation"
                            title="Click to view QR Code: {{ $voucher->voucher_code }}"
                            @click="$dispatch('open-qr-modal', { 
                                qrCode: '{{ $voucher->voucher_code }}',
                                qrImage: '{{ $qrCodeImageFull }}',
                                title: '{{ $voucher->name }}'
                            })"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                            </svg>

                            QR Code
                        </button>

                    @else
                        <span class="rounded-full p-3 flex items-center justify-center bg-gray-400 cursor-not-allowed text-white" title="Your merchant account is pending approval">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                        </span>
                        <span class="rounded-full p-3 flex items-center justify-center bg-gray-400 cursor-not-allowed text-white" title="Your merchant account is pending approval">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

