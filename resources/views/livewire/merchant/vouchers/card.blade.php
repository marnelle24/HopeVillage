<div class="w-full bg-white overflow-hidden border-2 border-gray-300 rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
    @if($voucher->image_url)
        <div class="relative">
            <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full md:h-40 h-56 object-cover object-center border border-gray-300 rounded-t-lg bg-white transition-all duration-200">
            <span class="text-xs absolute bottom-2 right-2 bg-orange-100/80 rounded-lg flex gap-1 items-center font-semibold px-2 py-1 text-gray-700 border border-orange-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                {{ $voucher->voucher_code }}
            </span> 
        </div>
    @else
        <div class="flex-1 flex items-start h-full p-4">
            <div class="w-full max-w-md h-64 mb-4 bg-gray-200 rounded-lg flex flex-col items-center justify-center border border-gray-300">
                <p class="text-gray-400 text-sm">IMG</p>
                <p class="text-gray-400 text-sm">Upload</p>
            </div>
        </div>
    @endif
    <div class="flex-1 flex items-start h-full p-4">
        <div class="flex-1">
            <div class="flex flex-col items-start gap-1">
                <div class="my-2">
                    <h3 class="md:text-lg text-2xl font-bold text-gray-800">{{ $voucher->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $voucher->description }}</p>
                </div>

                <div class="flex gap-1 bg-purple-100 rounded-lg py-1 px-3">
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
                </div>
                {{-- add the validity date range --}}
                <div class="flex flex-col items-start gap-1">
                    <p class="text-sm text-gray-600">Period:</p>
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-xs italic text-gray-500">From: {{ $voucher->valid_from ? $voucher->valid_from->format('d M Y g:i A') : 'N/A' }}</span>
                        <span class="text-xs italic text-gray-500">Until: {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y g:i A') : 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-1">
                    @php
                        if (!$voucher->is_active) {
                            $status = 'Inactive';
                            $statusClass = 'bg-red-200 text-red-700';
                        } elseif ($voucher->isValid()) {
                            $status = 'Active';
                            $statusClass = 'bg-green-200 text-green-700';
                        } else {
                            $statusReason = $voucher->getStatusReason();
                            $status = $statusReason ?? 'Inactive';
                            $statusClass = 'bg-yellow-200 text-yellow-700';
                        }
                    @endphp
                    <span class="flex items-center gap-1 {{ $statusClass }} mt-2 rounded-lg px-2.5 py-1 text-xs" title="{{ $voucher->is_active ? ($voucher->isValid() ? 'Voucher is active and valid' : 'Voucher is active but ' . strtolower($status)) : 'Voucher is inactive' }}">
                        <span class="text-xs">{{ $status }}</span>
                    </span>
                    @if($voucher->usage_limit)
                        <span class="flex items-center gap-1 text-gray-600 mt-2 bg-gray-200 rounded-lg py-1 px-3 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span class="text-sm">{{ $voucher->usage_count }}/{{ $voucher->usage_limit }}</span>
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-end gap-2 mt-4">
                    @if($merchant->is_active)
                        <button 
                            wire:click="edit"
                            title="Edit Voucher"
                            class="flex gap-1 items-center text-xs hover:scale-110 hover:text-sky-600 cursor-pointer text-gray-600 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            Edit
                        </button>
                        <span class="text-gray-600 text-xs">|</span>
                        <button 
                            wire:confirm="Are you sure you want to delete this voucher?" 
                            title="Delete Voucher"
                            wire:click="delete"
                            class="flex gap-1 items-center text-xs hover:scale-110 hover:text-red-600 cursor-pointer text-gray-600 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Delete
                        </button>
                        <span class="text-gray-600 text-xs">|</span>
                        <button 
                            wire:confirm="Are you sure you want to delete this voucher?" 
                            class="flex gap-1 items-center text-xs hover:scale-110 hover:text-yellow-700 cursor-pointer text-gray-600 transition-all duration-300"
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

