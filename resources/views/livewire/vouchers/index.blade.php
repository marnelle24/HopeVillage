<div>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Vouchers Management') }}
                </h2>
                {{-- @if($tab === 'admin')
                    <a href="{{ route('admin.admin-vouchers.create') }}" class="md:block hidden text-orange-500 font-medium hover:text-orange-700 hover:scale-105 transition-all duration-300">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Create New Admin Voucher
                        </span>
                    </a>
                    <a href="{{ route('admin.admin-vouchers.create') }}" class="md:hidden block bg-orange-500 hover:bg-orange-600 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('admin.vouchers.create') }}" class="md:block hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Add New Voucher
                    </a>
                    <a href="{{ route('admin.vouchers.create') }}" class="md:hidden block bg-indigo-600 hover:bg-indigo-500 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                @endif --}}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
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
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative md:mx-0 mx-4" 
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg mb-6 md:mx-0 mx-4">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button
                            wire:click="setTab('merchant')"
                            class="@if($tab === 'merchant') border-orange-500 text-orange-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200"
                        >
                            Merchant Vouchers
                        </button>
                        <button
                            wire:click="setTab('admin')"
                            class="@if($tab === 'admin') border-orange-500 text-orange-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200"
                        >
                            Admin Vouchers
                        </button>
                    </nav>
                </div>

                <!-- Search and Filter -->
                <div class="p-6 md:mx-0 mx-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="@if($tab === 'admin') md:col-span-3 @else md:col-span-2 @endif">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search {{ $tab === 'admin' ? 'admin' : 'merchant' }} vouchers..." 
                                class="w-full px-4 py-2 border text-gray-800 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                        </div>
                        <div>
                            <select 
                                wire:model.live="statusFilter" 
                                class="w-full px-4 py-2 border text-gray-800 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                                <option value="all">All Status</option>
                                @if($tab === 'merchant')
                                    <option value="pending">Pending Approval</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                @else
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                @endif
                            </select>
                        </div>
                        @if($tab === 'merchant')
                            <div>
                                <select 
                                    wire:model.live="merchantFilter" 
                                    class="w-full px-4 py-2 border text-gray-800 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option value="">All Merchants</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($tab === 'admin')
                            <a href="{{ route('admin.admin-vouchers.create') }}" class="text-center rounded-full md:block hidden bg-orange-500 hover:bg-orange-600 duration-300 hover:scale-105 text-white font-semibold py-2 px-4">
                                Add Admin Voucher
                            </a>
                            <a href="{{ route('admin.admin-vouchers.create') }}" class="md:hidden block bg-orange-500 hover:bg-orange-600 duration-300 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('admin.vouchers.create') }}" class="text-center rounded-full md:block hidden bg-orange-500 hover:bg-orange-600 hover:scale-105 text-white font-semibold py-2 px-4">
                                Add Voucher
                            </a>
                            <a href="{{ route('admin.vouchers.create') }}" class="md:hidden block bg-orange-500 hover:bg-orange-600 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="mt-4 flex flex-wrap gap-2 items-center">
                        <span class="text-sm text-gray-600 font-medium">Sort by:</span>
                        <button 
                            wire:click="toggleSort('start_date')"
                            class="px-3 py-1 text-sm rounded-full border transition-colors @if($sortBy === 'start_date') bg-orange-100 border-orange-500 text-orange-700 @else border-gray-300 text-gray-700 hover:bg-gray-50 @endif"
                        >
                            Start Date
                            @if($sortBy === 'start_date')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                        <button 
                            wire:click="toggleSort('created_at')"
                            class="px-3 py-1 text-sm rounded-full border transition-colors @if($sortBy === 'created_at') bg-orange-100 border-orange-500 text-orange-700 @else border-gray-300 text-gray-700 hover:bg-gray-50 @endif"
                        >
                            Created Date
                            @if($sortBy === 'created_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                        <button 
                            wire:click="toggleSort('name')"
                            class="px-3 py-1 text-sm rounded-full border transition-colors @if($sortBy === 'name') bg-orange-100 border-orange-500 text-orange-700 @else border-gray-300 text-gray-700 hover:bg-gray-50 @endif"
                        >
                            Name
                            @if($sortBy === 'name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </div>
                </div>

                <div class="lg:p-6 p-0 md:mx-0 mx-4 space-y-8">
                    @if($tab === 'merchant')
                        <!-- Merchant Vouchers - Grouped by Status -->
                        @php
                            $statusGroups = [
                                'pending' => ['label' => 'Pending For Approval', 'color' => 'red', 'vouchers' => $groupedVouchers['pending'] ?? collect()],
                                'active' => ['label' => 'Active', 'color' => 'green', 'vouchers' => $groupedVouchers['active'] ?? collect()],
                                'expired' => ['label' => 'Expired', 'color' => 'gray', 'vouchers' => $groupedVouchers['expired'] ?? collect()],
                            ];
                        @endphp

                        @foreach($statusGroups as $statusKey => $group)
                            @if($group['vouchers']->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-lg font-bold text-gray-800 px-2 md:px-0">{{ $group['label'] }}</h3>
                                    <div class="relative">
                                        <div class="overflow-x-auto scrollbar-hide scroll-smooth md:px-0 px-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                                            <div class="flex gap-4 pb-4" style="min-width: max-content;">
                                                @foreach($group['vouchers'] as $voucher)
                                                    <div class="shrink-0 w-80">
                                                        <x-voucher-card :voucher="$voucher" type="merchant" :show-actions="true" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(($groupedVouchers['pending'] ?? collect())->isEmpty() && 
                            ($groupedVouchers['active'] ?? collect())->isEmpty() && 
                            ($groupedVouchers['expired'] ?? collect())->isEmpty())
                            <div class="text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                                No merchant vouchers found.
                            </div>
                        @endif
                    @else
                        <!-- Admin Vouchers - Grouped by Status -->
                        @php
                            $statusGroups = [
                                'pending' => ['label' => 'Pending', 'color' => 'red', 'vouchers' => $groupedAdminVouchers['pending'] ?? collect()],
                                'active' => ['label' => 'Active', 'color' => 'green', 'vouchers' => $groupedAdminVouchers['active'] ?? collect()],
                                'expired' => ['label' => 'Expired', 'color' => 'gray', 'vouchers' => $groupedAdminVouchers['expired'] ?? collect()],
                            ];
                        @endphp

                        @foreach($statusGroups as $statusKey => $group)
                            @if($group['vouchers']->isNotEmpty())
                                <div class="space-y-3">
                                    <h3 class="text-lg font-bold text-gray-800 px-2 md:px-0">{{ $group['label'] }}</h3>
                                    <div class="relative">
                                        <div class="overflow-x-auto scrollbar-hide scroll-smooth md:px-0 px-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                                            <div class="flex gap-4 pb-4" style="min-width: max-content;">
                                                @foreach($group['vouchers'] as $adminVoucher)
                                                    <div class="shrink-0 w-80 max-w-[320px]">
                                                        <x-voucher-card :voucher="$adminVoucher" type="admin" :show-actions="true" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(($groupedAdminVouchers['pending'] ?? collect())->isEmpty() && 
                            ($groupedAdminVouchers['active'] ?? collect())->isEmpty() && 
                            ($groupedAdminVouchers['expired'] ?? collect())->isEmpty())
                            <div class="text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                                No admin vouchers found.
                            </div>
                        @endif
                    @endif
                </div>
        </div>
    </div>
</div>
