<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Merchant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('merchant.merchant-switcher')
            
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-start gap-3">
                        @php
                            $currentMerchant = auth()->user()->currentMerchant();
                        @endphp
                        @if($currentMerchant && $currentMerchant->logo_url)
                            <img src="{{ $currentMerchant->logo_url }}" alt="{{ $currentMerchant->name }}" class="w-16 h-16 object-contain rounded-lg border border-gray-300">
                        @endif
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                            @if($currentMerchant)
                                <p class="text-gray-600 mt-1">{{ $currentMerchant->name }}</p>
                            @endif
                        </div>
                    </div>
                    @if($currentMerchant)
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-blue-100 text-blue-800 border border-blue-300 rounded-lg">
                                <span class="font-semibold text-sm">Merchant User</span>
                            </div>
                        </div>
                    @endif
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
                                {{ $currentMerchant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        <p class="text-sm text-blue-700 mt-2">{{ $currentMerchant->merchant_code }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Recent Vouchers</h2>
                        <a href="{{ route('merchant.vouchers.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                            Manage Vouchers
                        </a>
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
                                        <a href="{{ route('merchant.vouchers.edit', $voucher->voucher_code) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Edit →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">No vouchers yet</p>
                            <a href="{{ route('merchant.vouchers.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Create Your First Voucher
                            </a>
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
</x-app-layout>
