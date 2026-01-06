<x-app-layout>
    <div class="shrink-0 flex items-center justify-between px-4 pt-4">
        <a href="{{ route('dashboard') }}">
            {{-- <x-application-mark class="block h-9 w-auto" /> --}}
            {{-- <span class="text-xl font-bold text-gray-800">Hope Village</span> --}}
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-16">
        </a>
        {{-- add a logout button --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center hover:text-red-800 text-sm font-semibold bg-orange-500 text-white px-3 py-2 rounded-lg">
                <svg class="size-4" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" version="1.1" fill="#000000">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g style="fill:none;stroke:#ffffff;stroke-width:12px;stroke-linecap:round;stroke-linejoin:round;"> <path d="m 50,10 0,35"></path> <path d="M 26,20 C -3,48 16,90 51,90 79,90 89,67 89,52 89,37 81,26 74,20"></path> </g> </g>
                </svg>
                <span class="ml-1">Logout</span>
            </button>
        </form>
    </div>

    <div class="py-12 lg:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- @livewire('merchant.merchant-switcher') --}}
            
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
                            <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                            @if($currentMerchant)
                            <div class="mt-4">
                                {{-- <span class="text-gray-600 text-sm font-normal">Store Name:</span> --}}
                                <p class="text-gray-600 flex items-center gap-2 mt-1 font-normal bg-green-100 border border-green-300 rounded-lg px-2 py-1">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                    {{ $currentMerchant->name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($currentMerchant)

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-10">
                    <!-- Active Vouchers Card -->
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Active Vouchers</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $currentMerchant->vouchers()->where('is_active', true)->count() }}</p>
                        <p class="text-sm text-purple-700 mt-2">Currently active vouchers</p>
                    </div>

                    <!-- Total Vouchers Card -->
                    <div class="bg-teal-50 p-6 rounded-lg border border-teal-200">
                        <h3 class="text-lg font-semibold text-teal-900 mb-2">Total Vouchers</h3>
                        <p class="text-3xl font-bold text-teal-600">{{ $currentMerchant->vouchers()->count() }}</p>
                        <p class="text-sm text-teal-700 mt-2">All vouchers created</p>
                    </div>

                    <!-- Total Vouchers Card -->
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">Other Vouchers</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $currentMerchant->vouchers()->count() }}</p>
                        <p class="text-sm text-yellow-700 mt-2">Vouchers From Administrator</p>
                    </div>

                    <!-- Merchant Info Card -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Merchant Status</h3>
                        <p class="text-lg font-bold text-blue-600">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $currentMerchant->is_active ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-red-100 text-red-800 border border-red-400' }}">
                                {{ $currentMerchant->is_active ? 'Approved' : 'Pending Approval' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Vouchers</h2>
                        @if($currentMerchant->is_active)
                            <a href="{{ route('merchant.vouchers.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Manage →
                            </a>
                        @else
                            <span class="bg-gray-400 cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg" title="Your merchant account is pending approval">
                                Manage →
                            </span>
                        @endif
                    </div>

                    @php
                        $recentVouchers = $currentMerchant->vouchers()->latest()->take(5)->get();
                    @endphp

                    @if($recentVouchers->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentVouchers as $voucher)
                                <div class="border-l-2 border-purple-500 pl-4 py-3 bg-gray-100 hover:bg-gray-100 transition-colors rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $voucher->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $voucher->description }}</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2 py-1 rounded">
                                                    {{ $voucher->discount_type === 'percentage' ? $voucher->discount_value . '%' : '$' . number_format($voucher->discount_value, 2) }} Off
                                                </span>
                                                @php
                                                    $isValid = $voucher->is_active && $voucher->isValid();
                                                @endphp
                                                <span class="text-xs px-2 py-1 rounded {{ $isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $isValid ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($currentMerchant->is_active)
                                            <a href="{{ route('merchant.vouchers.edit', $voucher->voucher_code) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-2">
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

    <br />
    <br />
</x-app-layout>
