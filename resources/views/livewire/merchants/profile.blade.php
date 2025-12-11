<div>
    <x-slot name="header">
        <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ $merchant->name }} - Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.merchants.edit', $merchant->merchant_code) }}" class="bg-slate-600 md:text-base text-xs hover:bg-slate-700 text-white font-normal py-2 px-4 rounded-lg">
                    Edit Merchant
                </a>
                <a href="{{ route('admin.vouchers.index', ['merchant' => $merchant->id]) }}" class="bg-indigo-600 md:text-base text-xs hover:bg-indigo-700 text-white font-normal py-2 px-4 rounded-lg">
                    Manage Vouchers
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.merchants.index') }}" class="text-gray-600 hover:text-gray-900 md:mx-0 mx-4">
                ← Back to Merchants
            </a>
            
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6 md:mx-0 mx-4">
                <!-- Left Column - Merchant Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Merchant Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Merchant Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Name</label>
                                <p class="text-gray-900">{{ $merchant->name }}</p>
                            </div>
                            
                            @if($merchant->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="text-gray-900">{{ $merchant->description }}</p>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p>
                                        <span class="px-3 py-1 inline-flex text-md leading-5 font-semibold rounded-full {{ $merchant->is_active ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-red-100 text-red-800 border border-red-500' }}">
                                            {{ $merchant->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Merchant Code</label>
                                    <p class="text-gray-900">{{ $merchant->merchant_code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Contact Information</h3>
                        
                        <div class="space-y-4">
                            @if($merchant->contact_name)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Contact Name</label>
                                <p class="text-gray-900">{{ $merchant->contact_name }}</p>
                            </div>
                            @endif

                            @if($merchant->address)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Address</label>
                                <p class="text-gray-900">
                                    {{ $merchant->address }}
                                    @if($merchant->city || $merchant->province || $merchant->postal_code)
                                        <br>
                                        {{ trim(implode(', ', array_filter([$merchant->city, $merchant->province, $merchant->postal_code]))) }}
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($merchant->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-gray-900">{{ $merchant->phone }}</p>
                            </div>
                            @endif

                            @if($merchant->email)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-gray-900">{{ $merchant->email }}</p>
                            </div>
                            @endif

                            @if($merchant->website)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Website</label>
                                <p class="text-gray-900">
                                    <a href="{{ $merchant->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $merchant->website }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Recent Vouchers -->
                <div class="space-y-6">
                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('admin.vouchers.create', ['merchant' => $merchant->id]) }}" class="flex items-center justify-center gap-2 w-full bg-indigo-500 hover:bg-indigo-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 8V16M16 12H8M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                Create New Voucher
                            </a>
                            <a href="{{ route('admin.vouchers.index', ['merchant' => $merchant->id]) }}" class="flex items-center justify-center gap-2 w-full bg-gray-600 hover:bg-gray-700 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m-8.25 3.75h16.5m-16.5 3.75h16.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                                View All Vouchers
                            </a>
                            <button 
                                wire:click="$dispatch('open-manage-users-modal')"
                                class="flex items-center justify-center gap-2 w-full bg-purple-500 hover:bg-purple-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200"
                            >
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @if($merchant->users->count() > 0)
                                    Manage Users ({{ $merchant->users->count() }})
                                @else
                                    Add User
                                @endif
                            </button>
                            @livewire('merchants.manage-users', ['merchant_code' => $merchant->merchant_code], key('manage-users-' . $merchant->id))
                        </div>
                    </div>

                    <!-- Recent Vouchers Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Vouchers</h3>
                            <a href="{{ route('admin.vouchers.index', ['merchant' => $merchant->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                View All
                            </a>
                        </div>
                        
                        @if($merchant->vouchers->count() > 0)
                            <div class="space-y-3">
                                @foreach($merchant->vouchers as $voucher)
                                    <div class="border-l-4 border-indigo-500 pl-3 py-2 group hover:bg-gray-100 hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                                        <a href="{{ route('admin.vouchers.profile', $voucher->voucher_code) }}" class="group-hover:text-indigo-600 text-md font-semibold text-indigo-400 hover:text-indigo-600 transition-colors duration-300">
                                            {{ $voucher->name }}
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $voucher->discount_type === 'percentage' ? $voucher->discount_value . '%' : '$' . number_format($voucher->discount_value, 2) }} Off
                                            </p>
                                            @php
                                                $statusClass = $voucher->is_active && $voucher->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                            @endphp
                                            <div class="mt-2">
                                                <span class="inline-flex text-xs leading-5 font-semibold rounded-full mt-1 px-2.5 py-1 {{ $statusClass }}">
                                                    {{ $voucher->is_active && $voucher->isValid() ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 flex items-center justify-center gap-2 py-4">
                                <span class="text-gray-500 text-sm">No vouchers yet</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
