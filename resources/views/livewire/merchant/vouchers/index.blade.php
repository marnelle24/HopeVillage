<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('My Vouchers') }}
            </h2>
            @if($merchant->is_active)
                <a href="{{ route('merchant.vouchers.create') }}" class="md:block hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Create Voucher
                </a>
                <a href="{{ route('merchant.vouchers.create') }}" class="md:hidden block bg-indigo-600 hover:bg-indigo-500 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
            @else
                <span class="md:block hidden bg-gray-400 cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg" title="Your merchant account is pending approval">
                    Create Voucher
                </span>
                <span class="md:hidden block bg-gray-400 cursor-not-allowed text-white p-2 rounded-full" title="Your merchant account is pending approval">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(!$merchant->is_active)
                <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative md:mx-0 mx-4" role="alert">
                    <span class="block sm:inline">
                        <strong>Notice:</strong> Your merchant account is pending approval. You cannot create or edit vouchers until your account is approved.
                    </span>
                </div>
            @endif

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
                            placeholder="Search vouchers..." 
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
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:px-0 px-4">
                @forelse($vouchers as $voucher)
                    <div class="w-full bg-purple-50 overflow-hidden border-2 border-gray-300 flex md:flex-row flex-col md:justify-between justify-start items-center rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="flex-1 flex items-start h-full p-4">
                            <div class="flex-1">
                                <div class="flex md:flex-row flex-col md:items-center items-start gap-2">
                                    <h3 class="md:text-lg text-xl font-bold text-gray-500">{{ $voucher->name }}</h3>
                                    <span class="flex items-center gap-1 text-gray-600 bg-transparent border border-gray-500 rounded-lg py-1 px-2 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span class="text-xs">{{ $voucher->voucher_code }}</span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $voucher->description }}</p>
                                <div class="flex md:flex-row flex-col md:items-center items-start gap-2 mt-2">
                                    <div class="flex">
                                        <span class="flex items-center gap-1 text-gray-600 mt-2 bg-purple-200 rounded-lg py-1 px-3 text-xs font-semibold">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                            </svg>
                                            <span class="text-sm">
                                                @if($voucher->discount_type === 'item')
                                                    {{ '$' . number_format($voucher->discount_value, 2) . ' worth of one item' }}
                                                @elseif($voucher->discount_type === 'fixed')
                                                    {{ '$' . number_format($voucher->discount_value, 2) }}
                                                @elseif($voucher->discount_type === 'percentage')
                                                    {{ $voucher->discount_value . '%' . ' Off' }}
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    @if($voucher->usage_limit)
                                        <div class="flex">
                                            <span class="flex items-center gap-1 text-gray-600 mt-2 bg-gray-200 rounded-lg py-1 px-3 text-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                </svg>
                                                <span class="text-sm">{{ $voucher->usage_count }}/{{ $voucher->usage_limit }}</span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex">
                                        @php
                                            $status = $voucher->is_active && $voucher->isValid() ? 'Active' : 'Inactive';
                                            $statusClass = $voucher->is_active && $voucher->isValid() ? 'bg-green-100 text-green-800 border border-green-300/50' : 'bg-red-100 text-red-800 border border-red-300';
                                        @endphp
                                        <span class="flex items-center gap-1 {{ $statusClass }} mt-2 rounded-lg px-2.5 py-1 text-xs">
                                            <span class="text-sm">{{ $status }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 flex items-start justify-end gap-2">
                                @if($merchant->is_active)
                                    <button 
                                        wire:click="edit('{{ $voucher->voucher_code }}')"
                                        title="Edit Voucher"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-blue-500 hover:bg-sky-600 text-white transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button 
                                        wire:confirm="Are you sure you want to delete this voucher?" 
                                        title="Delete Voucher"
                                        wire:click="delete('{{ $voucher->voucher_code }}')"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-red-400 hover:bg-red-500 text-white transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
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
                @empty
                    <div class="col-span-full text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                        <p class="text-gray-500 mb-4">No vouchers found.</p>
                        @if($merchant->is_active)
                            <a href="{{ route('merchant.vouchers.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Create Your First Voucher
                            </a>
                        @else
                            <div class="inline-block">
                                <span class="bg-gray-400 cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg" title="Your merchant account is pending approval">
                                    Create Your First Voucher
                                </span>
                                <p class="text-sm text-yellow-600 mt-2">Your merchant account is pending approval</p>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="mt-6 md:px-0 px-4">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
</div>
