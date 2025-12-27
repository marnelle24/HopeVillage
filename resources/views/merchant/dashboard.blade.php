<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Merchant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 lg:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('merchant.merchant-switcher')
            
            @php
                $currentMerchant = auth()->user()->currentMerchant();
            @endphp
            
            <div class="mb-6">
                <div class="flex gap-4 lg:flex-row flex-col justify-between ">
                    <div class="flex items-start gap-3">
                        {{-- @if($currentMerchant && $currentMerchant->logo_url)
                            <img src="{{ $currentMerchant->logo_url }}" alt="{{ $currentMerchant->name }}" class="w-16 h-16 object-contain rounded-lg border border-gray-300">
                        @endif --}}
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                            {{-- @if($currentMerchant)
                                <p class="text-gray-600 mt-1">{{ $currentMerchant->name }}</p>
                            @endif --}}
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        @if($currentMerchant && $currentMerchant->is_active)
                            @livewire('merchant.register-merchant')
                        @endif
                    </div>
                    {{-- @if($currentMerchant)
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-blue-100 text-blue-800 border border-blue-300 rounded-lg">
                                <span class="font-semibold text-sm">Merchant User</span>
                            </div>
                        </div>
                    @endif --}}
                </div>
            </div>

            @if($currentMerchant)

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Active Vouchers Card -->
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Active Vouchers</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $currentMerchant->vouchers()->where('is_active', true)->count() }}</p>
                        <p class="text-sm text-purple-700 mt-2">Currently active vouchers</p>
                    </div>

                    <!-- Total Vouchers Card -->
                    <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-200">
                        <h3 class="text-lg font-semibold text-indigo-900 mb-2">Total Vouchers</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $currentMerchant->vouchers()->count() }}</p>
                        <p class="text-sm text-indigo-700 mt-2">All vouchers created</p>
                    </div>

                    <!-- Merchant Info Card -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Merchant Status</h3>
                        <p class="text-lg font-bold text-blue-600">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $currentMerchant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $currentMerchant->is_active ? 'Approved' : 'Pending Approval' }}
                            </span>
                        </p>
                        {{-- <p class="text-sm text-blue-700 mt-2">{{ $currentMerchant->merchant_code }}</p> --}}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Recent Vouchers</h2>
                        @if($currentMerchant->is_active)
                            <a href="{{ route('merchant.vouchers.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Manage Vouchers
                            </a>
                        @else
                            <span class="bg-gray-400 cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg" title="Your merchant account is pending approval">
                                Manage Vouchers
                            </span>
                        @endif
                    </div>

                    @php
                        $recentVouchers = $currentMerchant->vouchers()->latest()->take(5)->get();
                    @endphp

                    @if($recentVouchers->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentVouchers as $voucher)
                                <div class="border-l-4 border-purple-500 pl-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $voucher->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $voucher->description }}</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2 py-1 rounded">
                                                    {{ $voucher->discount_type === 'percentage' ? $voucher->discount_value . '%' : '$' . number_format($voucher->discount_value, 2) }} Off
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $voucher->voucher_code }}</span>
                                                @php
                                                    $isValid = $voucher->is_active && $voucher->isValid();
                                                @endphp
                                                <span class="text-xs px-2 py-1 rounded {{ $isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $isValid ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($currentMerchant->is_active)
                                            <a href="{{ route('merchant.vouchers.edit', $voucher->voucher_code) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                Edit →
                                            </a>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed text-sm font-medium" title="Your merchant account is pending approval">
                                                Edit →
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">No vouchers yet</p>
                            @if($currentMerchant->is_active)
                                <a href="{{ route('merchant.vouchers.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    Create Your First Voucher
                                </a>
                            @else
                                <div class="inline-block">
                                    <span class="bg-gray-400 cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg" title="Your merchant account is pending approval. You cannot create vouchers until your account is approved.">
                                        Create Your First Voucher
                                    </span>
                                    <p class="text-sm text-yellow-600 mt-8">Your merchant account is pending approval</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <p class="text-yellow-800">
                        <strong>Notice:</strong> Your account is not associated with a merchant. Please contact an administrator.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Floating QR Scanner Button (similar to member navigation) -->
    {{-- @if($currentMerchant && $currentMerchant->is_active)
    <div class="fixed bottom-6 right-6 z-50">
        <button
            type="button"
            @click="window.dispatchEvent(new CustomEvent('openQrScanner'))"
            aria-label="Scan Voucher QR Code"
            class="w-16 h-16 rounded-full bg-indigo-500/90 hover:bg-indigo-600 text-white shadow-lg ring-4 ring-indigo-300 flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-200"
            title="Scan Voucher QR Code"
        >
            <svg class="size-10" fill="#ffffff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.1666667,6 C16.0746192,6 16,6.07461921 16,6.16666667 L16,7.83333333 C16,7.92538079 16.0746192,8 16.1666667,8 L17.8333333,8 C17.9253808,8 18,7.92538079 18,7.83333333 L18,6.16666667 C18,6.07461921 17.9253808,6 17.8333333,6 L16.1666667,6 Z M16,18 L16,17.5 C16,17.2238576 16.2238576,17 16.5,17 C16.7761424,17 17,17.2238576 17,17.5 L17,18 L18,18 L18,17.5 C18,17.2238576 18.2238576,17 18.5,17 C18.7761424,17 19,17.2238576 19,17.5 L19,18.5 C19,18.7761424 18.7761424,19 18.5,19 L14.5,19 C14.2238576,19 14,18.7761424 14,18.5 L14,17.5 C14,17.2238576 14.2238576,17 14.5,17 C14.7761424,17 15,17.2238576 15,17.5 L15,18 L16,18 L16,18 Z M13,11 L13.5,11 C13.7761424,11 14,11.2238576 14,11.5 C14,11.7761424 13.7761424,12 13.5,12 L11.5,12 C11.2238576,12 11,11.7761424 11,11.5 C11,11.2238576 11.2238576,11 11.5,11 L12,11 L12,10 L10.5,10 C10.2238576,10 10,9.77614237 10,9.5 C10,9.22385763 10.2238576,9 10.5,9 L13.5,9 C13.7761424,9 14,9.22385763 14,9.5 C14,9.77614237 13.7761424,10 13.5,10 L13,10 L13,11 Z M18,12 L17.5,12 C17.2238576,12 17,11.7761424 17,11.5 C17,11.2238576 17.2238576,11 17.5,11 L18,11 L18,10.5 C18,10.2238576 18.2238576,10 18.5,10 C18.7761424,10 19,10.2238576 19,10.5 L19,12.5 C19,12.7761424 18.7761424,13 18.5,13 C18.2238576,13 18,12.7761424 18,12.5 L18,12 Z M13,14 L12.5,14 C12.2238576,14 12,13.7761424 12,13.5 C12,13.2238576 12.2238576,13 12.5,13 L13.5,13 C13.7761424,13 14,13.2238576 14,13.5 L14,15.5 C14,15.7761424 13.7761424,16 13.5,16 L10.5,16 C10.2238576,16 10,15.7761424 10,15.5 C10,15.2238576 10.2238576,15 10.5,15 L13,15 L13,14 L13,14 Z M16.1666667,5 L17.8333333,5 C18.4776655,5 19,5.52233446 19,6.16666667 L19,7.83333333 C19,8.47766554 18.4776655,9 17.8333333,9 L16.1666667,9 C15.5223345,9 15,8.47766554 15,7.83333333 L15,6.16666667 C15,5.52233446 15.5223345,5 16.1666667,5 Z M6.16666667,5 L7.83333333,5 C8.47766554,5 9,5.52233446 9,6.16666667 L9,7.83333333 C9,8.47766554 8.47766554,9 7.83333333,9 L6.16666667,9 C5.52233446,9 5,8.47766554 5,7.83333333 L5,6.16666667 C5,5.52233446 5.52233446,5 6.16666667,5 Z M6.16666667,6 C6.07461921,6 6,6.07461921 6,6.16666667 L6,7.83333333 C6,7.92538079 6.07461921,8 6.16666667,8 L7.83333333,8 C7.92538079,8 8,7.92538079 8,7.83333333 L8,6.16666667 C8,6.07461921 7.92538079,6 7.83333333,6 L6.16666667,6 Z M6.16666667,15 L7.83333333,15 C8.47766554,15 9,15.5223345 9,16.1666667 L9,17.8333333 C9,18.4776655 8.47766554,19 7.83333333,19 L6.16666667,19 C5.52233446,19 5,18.4776655 5,17.8333333 L5,16.1666667 C5,15.5223345 5.52233446,15 6.16666667,15 Z M6.16666667,16 C6.07461921,16 6,16.0746192 6,16.1666667 L6,17.8333333 C6,17.9253808 6.07461921,18 6.16666667,18 L7.83333333,18 C7.92538079,18 8,17.9253808 8,17.8333333 L8,16.1666667 C8,16.0746192 7.92538079,16 7.83333333,16 L6.16666667,16 Z M13,6 L10.5,6 C10.2238576,6 10,5.77614237 10,5.5 C10,5.22385763 10.2238576,5 10.5,5 L13.5,5 C13.7761424,5 14,5.22385763 14,5.5 L14,7.5 C14,7.77614237 13.7761424,8 13.5,8 C13.2238576,8 13,7.77614237 13,7.5 L13,6 Z M10.5,8 C10.2238576,8 10,7.77614237 10,7.5 C10,7.22385763 10.2238576,7 10.5,7 L11.5,7 C11.7761424,7 12,7.22385763 12,7.5 C12,7.77614237 11.7761424,8 11.5,8 L10.5,8 Z M5.5,14 C5.22385763,14 5,13.7761424 5,13.5 C5,13.2238576 5.22385763,13 5.5,13 L7.5,13 C7.77614237,13 8,13.2238576 8,13.5 C8,13.7761424 7.77614237,14 7.5,14 L5.5,14 Z M9.5,14 C9.22385763,14 9,13.7761424 9,13.5 C9,13.2238576 9.22385763,13 9.5,13 L10.5,13 C10.7761424,13 11,13.2238576 11,13.5 C11,13.7761424 10.7761424,14 10.5,14 L9.5,14 Z M11,18 L11,18.5 C11,18.7761424 10.7761424,19 10.5,19 C10.2238576,19 10,18.7761424 10,18.5 L10,17.5 C10,17.2238576 10.2238576,17 10.5,17 L12.5,17 C12.7761424,17 13,17.2238576 13,17.5 C13,17.7761424 12.7761424,18 12.5,18 L11,18 Z M9,11 L9.5,11 C9.77614237,11 10,11.2238576 10,11.5 C10,11.7761424 9.77614237,12 9.5,12 L8.5,12 C8.22385763,12 8,11.7761424 8,11.5 L8,11 L7.5,11 C7.22385763,11 7,10.7761424 7,10.5 C7,10.2238576 7.22385763,10 7.5,10 L8.5,10 C8.77614237,10 9,10.2238576 9,10.5 L9,11 Z M5,10.5 C5,10.2238576 5.22385763,10 5.5,10 C5.77614237,10 6,10.2238576 6,10.5 L6,11.5 C6,11.7761424 5.77614237,12 5.5,12 C5.22385763,12 5,11.7761424 5,11.5 L5,10.5 Z M15,10.5 C15,10.2238576 15.2238576,10 15.5,10 C15.7761424,10 16,10.2238576 16,10.5 L16,12.5 C16,12.7761424 15.7761424,13 15.5,13 C15.2238576,13 15,12.7761424 15,12.5 L15,10.5 Z M17,15 L17,14.5 C17,14.2238576 17.2238576,14 17.5,14 L18.5,14 C18.7761424,14 19,14.2238576 19,14.5 C19,14.7761424 18.7761424,15 18.5,15 L18,15 L18,15.5 C18,15.7761424 17.7761424,16 17.5,16 L15.5,16 C15.2238576,16 15,15.7761424 15,15.5 L15,14.5 C15,14.2238576 15.2238576,14 15.5,14 C15.7761424,14 16,14.2238576 16,14.5 L16,15 L17,15 Z M3,6.5 C3,6.77614237 2.77614237,7 2.5,7 C2.22385763,7 2,6.77614237 2,6.5 L2,4.5 C2,3.11928813 3.11928813,2 4.5,2 L6.5,2 C6.77614237,2 7,2.22385763 7,2.5 C7,2.77614237 6.77614237,3 6.5,3 L4.5,3 C3.67157288,3 3,3.67157288 3,4.5 L3,6.5 Z M17.5,3 C17.2238576,3 17,2.77614237 17,2.5 C17,2.22385763 17.2238576,2 17.5,2 L19.5,2 C20.8807119,2 22,3.11928813 22,4.5 L22,6.5 C22,6.77614237 21.7761424,7 21.5,7 C21.2238576,7 21,6.77614237 21,6.5 L21,4.5 C21,3.67157288 20.3284271,3 19.5,3 L17.5,3 Z M6.5,21 C6.77614237,21 7,21.2238576 7,21.5 C7,21.7761424 6.77614237,22 6.5,22 L4.5,22 C3.11928813,22 2,20.8807119 2,19.5 L2,17.5 C2,17.2238576 2.22385763,17 2.5,17 C2.77614237,17 3,17.2238576 3,17.5 L3,19.5 C3,20.3284271 3.67157288,21 4.5,21 L6.5,21 Z M21,17.5 C21,17.2238576 21.2238576,17 21.5,17 C21.7761424,17 22,17.2238576 22,17.5 L22,19.5 C22,20.8807119 20.8807119,22 19.5,22 L17.5,22 C17.2238576,22 17,21.7761424 17,21.5 C17,21.2238576 17.2238576,21 17.5,21 L19.5,21 C20.3284271,21 21,20.3284271 21,19.5 L21,17.5 Z"></path> </g>
            </svg>
        </button>
    </div> --}}
    {{-- @endif --}}
</x-app-layout>
