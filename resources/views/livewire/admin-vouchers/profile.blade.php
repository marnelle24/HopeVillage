<div>
    <x-slot name="header">
        <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ $voucher->name }} - Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.admin-vouchers.edit', $voucher->voucher_code) }}" class="bg-slate-600 md:text-base text-xs hover:bg-slate-700 text-white font-normal py-2 px-4 rounded-lg">
                    Edit Voucher
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.admin-vouchers.index') }}" class="text-gray-600 hover:text-gray-900 md:mx-0 mx-4">
                ← Back to Admin Vouchers
            </a>
            
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-4 gap-6 md:mx-0 mx-4">
                <!-- Left Column - Voucher Details -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Voucher Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Voucher Information</h3>
                        
                        <div class="space-y-4">
                            @if($voucher->image_url)
                                <div class="mb-4">
                                    <img src="{{ $voucher->image_url }}" alt="{{ $voucher->name }}" class="w-full max-w-md h-64 object-cover rounded-lg border border-gray-300">
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-gray-500">Name</label>
                                <p class="text-gray-900 font-semibold text-lg">{{ $voucher->name }}</p>
                            </div>
                            
                            @if($voucher->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="text-gray-900">{{ $voucher->description }}</p>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Voucher Code</label>
                                    <p class="text-gray-900 font-mono">{{ $voucher->voucher_code }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p>
                                        @php
                                            $isValid = $voucher->is_active && $voucher->isValid();
                                            $statusClass = $isValid ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-red-100 text-red-800 border border-red-500';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-md leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $isValid ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Points Cost</label>
                                    <p class="text-gray-900 font-semibold text-lg flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-orange-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                        {{ number_format($voucher->points_cost) }} Points
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Amount Cost</label>
                                    <p class="text-gray-900 font-semibold text-lg flex items-center gap-1">
                                        <svg class="w-5 h-5 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                        </svg>
                                        ${{ number_format($voucher->amount_cost, 2) }}
                                    </p>
                                </div>
                            </div>

                            @if($voucher->valid_from || $voucher->valid_until)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($voucher->valid_from)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Valid From</label>
                                    <p class="text-gray-900">{{ $voucher->valid_from->format('d M Y g:i A') }}</p>
                                </div>
                                @endif
                                @if($voucher->valid_until)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Valid Until</label>
                                    <p class="text-gray-900">{{ $voucher->valid_until->format('d M Y g:i A') }}</p>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($voucher->usage_limit)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Usage</label>
                                <p class="text-gray-900">{{ $voucher->usage_count }} / {{ $voucher->usage_limit }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Merchant Redemption Summary Card -->
                    @livewire('admin-vouchers.merchant-redemption-summary', ['voucherCode' => $voucher->voucher_code], key('merchant-redemption-summary-' . $voucher->voucher_code))

                    <!-- Member Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Voucher Engagement</h3>
                        
                        <div 
                            x-data="{ 
                                tab: 'claimed',
                                setTab(t) { this.tab = t; }
                            }"
                        >
                            <!-- Tabs -->
                            <div class="mb-4 flex items-center gap-2 border-b">
                                <button
                                    type="button"
                                    @click="setTab('claimed')"
                                    :class="tab === 'claimed' ? 'border-b-2 border-orange-500 text-orange-600 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-4 py-2 text-sm transition"
                                >
                                    Claimed ({{ count($claimedMembers) }})
                                </button>
                                <button
                                    type="button"
                                    @click="setTab('redeemed')"
                                    :class="tab === 'redeemed' ? 'border-b-2 border-orange-500 text-orange-600 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-4 py-2 text-sm transition"
                                >
                                    Redeemed ({{ count($redeemedMembers) }})
                                </button>
                            </div>

                            <!-- Claimed Tab -->
                            <div x-show="tab === 'claimed'" x-cloak>
                                @if(count($claimedMembers) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIN</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Claimed At</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($claimedMembers as $member)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">{{ $member['name'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500 font-mono">{{ $member['fin'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">{{ $member['email'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">
                                                                {{ $member['claimed_at'] ? \Carbon\Carbon::parse($member['claimed_at'])->format('d M Y g:i A') : 'N/A' }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <p>No members have claimed this voucher yet.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Redeemed Tab -->
                            <div x-show="tab === 'redeemed'" x-cloak>
                                @if(count($redeemedMembers) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIN</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Claimed At</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Redeemed At</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($redeemedMembers as $member)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">{{ $member['name'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500 font-mono">{{ $member['fin'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">{{ $member['email'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">
                                                                {{ $member['claimed_at'] ? \Carbon\Carbon::parse($member['claimed_at'])->format('d M Y g:i A') : 'N/A' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">
                                                                {{ $member['redeemed_at'] ? \Carbon\Carbon::parse($member['redeemed_at'])->format('d M Y g:i A') : 'N/A' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-500">
                                                                @if($member['merchant'])
                                                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs">
                                                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                                                        </svg>
                                                                        {{ $member['merchant']->name }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-gray-400">N/A</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <p>No members have redeemed this voucher yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions -->
                <div class="space-y-6">
                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('admin.admin-vouchers.edit', $voucher->voucher_code) }}" class="flex items-center justify-center gap-2 w-full bg-indigo-500 hover:bg-indigo-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                Edit Voucher
                            </a>
                        </div>
                    </div>

                    <!-- Redeemable Merchants Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Redeemable Merchants</h3>
                        
                        @if($voucher->merchants->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($voucher->merchants as $merchant)
                                    <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <svg class="w-5 h-5 text-indigo-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $merchant->name }}</p>
                                            @if($merchant->email)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $merchant->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">No merchants assigned</p>
                            </div>
                        @endif
                    </div>

                    <!-- Voucher Details Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Voucher Details</h3>
                        
                        <div class="space-y-4">
                            @if($voucher->createdBy)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Created By</label>
                                <p class="text-gray-900 mt-1">{{ $voucher->createdBy->name }}</p>
                            </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-gray-500">Created on</label>
                                <p class="text-gray-900 mt-1">{{ $voucher->created_at->format('d M Y g:i A') }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Last Updated on</label>
                                <p class="text-gray-900 mt-1">{{ $voucher->updated_at->format('d M Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
