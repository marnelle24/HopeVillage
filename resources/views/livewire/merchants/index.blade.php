<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Merchants Management') }}
            </h2>
            <a href="{{ route('admin.merchants.create') }}" class="md:block hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                Add New Merchant
            </a>
            <a href="{{ route('admin.merchants.create') }}" class="md:hidden block bg-indigo-600 hover:bg-indigo-500 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div 
                    x-data="{ 
                        show: @entangle('showMessage').live,
                        timeoutId: null
                    }"
                    x-init="
                        $watch('show', value => {
                            if (value && !timeoutId) {
                                timeoutId = setTimeout(() => {
                                    show = false;
                                    timeoutId = null;
                                }, 3000);
                            } else if (!value && timeoutId) {
                                clearTimeout(timeoutId);
                                timeoutId = null;
                            }
                        });
                        if (show) {
                            timeoutId = setTimeout(() => {
                                show = false;
                                timeoutId = null;
                            }, 3000);
                        }
                    "
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-out duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" 
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search merchants..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    <div>
                        <select 
                            wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending Approval @if($pendingCount > 0)({{ $pendingCount }})@endif</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:px-0 px-4">
                @forelse($merchants as $merchant)
                    <div class="w-full bg-blue-50 overflow-hidden border-2 border-gray-300 flex md:flex-row flex-col md:justify-between justify-start items-center rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="flex-1 flex items-start h-full w-full">
                            @if($merchant->logo_url)
                                <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->name }}" class="w-32 h-full object-cover rounded-lg">
                            @else
                                <span class="text-blue-800 opacity-50 drop-shadow-lg text-3xl font-bold w-32 h-full bg-blue-200 rounded-l-lg flex items-center justify-center">
                                    {{ strtoupper(substr($merchant->name, 0, 2)) }}
                                </span>
                            @endif
                            <div class="flex-1 flex md:flex-row flex-col justify-between">
                                <div class="px-4 py-4">
                                    <div class="flex md:flex-row flex-col md:items-center items-start gap-2">
                                        <h3 class="md:text-lg text-xl font-bold text-gray-500">{{ $merchant->name }}</h3>
                                        <span class="flex items-center gap-1 text-gray-600 bg-transparent border border-gray-500 rounded-lg py-1 px-2 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                            <span class="text-xs">{{ $merchant->merchant_code }}</span>
                                        </span>
                                        {{-- @if(!$merchant->is_active)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded-lg text-xs font-semibold">
                                                Pending Approval
                                            </span>
                                        @endif --}}
                                    </div>
                                    <p class="text-sm flex items-center gap-1 text-gray-600 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        @if($merchant->address)
                                            {{ $merchant->address }}
                                            @if($merchant->city || $merchant->province || $merchant->postal_code)
                                                {{ trim(implode(', ', array_filter([$merchant->city, $merchant->province, $merchant->postal_code]))) }}
                                            @endif
                                        @else
                                            <span class="text-gray-400">No address provided</span>
                                        @endif
                                    </p>
                                    <div class="flex md:flex-row flex-col md:items-center items-start gap-1 md:space-x-2 space-x-0 mt-2">
                                        @if($merchant->email)
                                            <div class="flex items-center gap-1 mt-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                                <span class="text-sm text-gray-600">{{ $merchant->email }}</span>
                                            </div>
                                        @endif
                                        @if($merchant->phone)
                                            <div class="flex items-center gap-1 mt-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                </svg>
                                                <span class="text-sm text-gray-600">{{ $merchant->phone }}</span>
                                            </div>
                                        @endif
                                        <div class="flex">
                                            <span class="flex items-center gap-1 text-gray-600 mt-2 bg-gray-200 border border-gray-300 rounded-lg py-1 px-3 text-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m-8.25 3.75h16.5m-16.5 3.75h16.5" />
                                                </svg>
                                                <span class="text-sm">{{ $merchant->vouchers_count }} Voucher(s)</span>
                                            </span>
                                        </div>
                                        <div class="flex gap-1 items-end">
                                            @php
                                                if ($merchant->is_active) {
                                                    $status = 'Active';
                                                    $statusClass = 'bg-green-100 text-green-800 border border-green-300/50';
                                                } else {
                                                    $status = 'Pending Approval';
                                                    $statusClass = 'bg-red-100 text-red-800 border border-red-300';
                                                }
                                            @endphp
                                            <span class="flex items-center gap-1 {{ $statusClass }} mt-2 rounded-lg px-2.5 py-1 text-xs">
                                                <span class="text-sm">{{ $status }}</span>
                                            </span>

                                            @if(!$merchant->is_active)
                                                <button 
                                                    wire:click="approve('{{ $merchant->merchant_code }}')"
                                                    wire:confirm="Are you sure you want to approve this merchant application?"
                                                    title="Approve Merchant"
                                                    class="flex items-center gap-1 hover:drop-shadow hover:-translate-y-0.5 justify-center text-green-600 transition-all duration-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                                    </svg>
                                                    <span class="text-xs">Approve</span>
                                                </button>
                                                <span class="text-xs">|</span>
                                                <button 
                                                    wire:click="reject('{{ $merchant->merchant_code }}')"
                                                    wire:confirm="Are you sure you want to reject this merchant application?"
                                                    title="Reject Merchant"
                                                    class="flex items-center gap-1 hover:drop-shadow hover:-translate-y-0.5 justify-center text-red-600 transition-all duration-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                                                    </svg>
                                                    <span class="text-xs">Reject</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 flex items-start justify-end gap-2 flex-wrap">
                                    <a href="{{ route('admin.merchants.profile', $merchant->merchant_code) }}" 
                                        title="View Profile"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-indigo-500 hover:bg-indigo-600 text-white transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <button 
                                        wire:click="edit('{{ $merchant->merchant_code }}')"
                                        title="Edit Merchant"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-blue-500 hover:bg-sky-600 text-white transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    @if($merchant->is_active)
                                        <button 
                                            wire:confirm="Are you sure you want to archive this merchant?" 
                                            title="Archive Merchant"
                                            wire:click="delete('{{ $merchant->merchant_code }}')"
                                            class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-red-400 hover:bg-red-500 text-white transition-all duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                        No merchants found.
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="mt-6 md:px-0 px-4">
                {{ $merchants->links() }}
            </div>
        </div>
    </div>
</div>
