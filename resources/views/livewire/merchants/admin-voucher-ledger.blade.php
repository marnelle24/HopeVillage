<div>
    @if ($showMessage && $syncMessage)
        <div
            x-data="{ show: @entangle('showMessage').live }"
            x-show="show"
            x-transition
            class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
        >
            {{ $syncMessage }}
        </div>
    @endif

    <!-- Filters and Sync -->
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select
                    wire:model.live="dateFilter"
                    class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
                    <option value="all">All time</option>
                    <option value="3months">Last 3 months</option>
                    <option value="6months">Last 6 months</option>
                    <option value="12months">Last 12 months</option>
                    <option value="custom">Custom range</option>
                </select>
            </div>
            @if ($dateFilter === 'custom')
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">From month</label>
                    <div class="relative">
                        <input
                            type="month"
                            id="monthFrom"
                            wire:model.live="monthFrom"
                            class="w-full px-4 py-2 pr-9 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                        <button
                            type="button"
                            onclick="document.getElementById('monthFrom').showPicker?.() || document.getElementById('monthFrom').click()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                            aria-label="Open calendar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">To month</label>
                    <div class="relative">
                        <input
                            type="month"
                            id="monthTo"
                            wire:model.live="monthTo"
                            class="w-full px-4 py-2 pr-9 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                        <button
                            type="button"
                            onclick="document.getElementById('monthTo').showPicker?.() || document.getElementById('monthTo').click()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                            aria-label="Open calendar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
            <div class="{{$dateFilter === 'custom' ? 'col-span-1' : 'col-span-3'}}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search merchant</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="merchantSearch"
                    placeholder="Merchant name or code..."
                    class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
            </div>
            <div class="col-span-1 flex flex-col justify-end gap-2">
                <button
                    type="button"
                    wire:click="syncLedger"
                    class="w-full px-4 py-2 text-sm cursor-pointer font-medium text-white bg-orange-500 rounded-full hover:bg-orange-600 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                >
                    Sync Ledger
                </button>
            </div>
        </div>
        <label class="flex items-start gap-2 mt-4">
            <input
                type="checkbox"
                wire:model.live="outstandingOnly"
                class="mt-0.5 p-2 rounded border-gray-300 text-orange-500 focus:ring-orange-500"
            >
            <span class="text-sm text-gray-700">Show with outstanding balance only</span>
        </label>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg md:mx-0 mx-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Merchant</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Voucher</th>
                        <th class="px-1 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">&nbsp;</th>
                        <th class="px-1 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">&nbsp;</th>
                        <th class="px-1 py-1 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($entries as $entry)
                        @php
                            $totalReimbursed = (float) ($entry->reimbursements_sum_amount ?? 0);
                            $outstanding = (float) $entry->total_amount_dispensed - $totalReimbursed;
                        @endphp
                        <tr class="{{$outstanding == 0 ? 'bg-green-100/50 hover:bg-green-50/50' : 'bg-red-100/50 hover:bg-red-50/50'}}">
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $entry->period_month->format('F Y') }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="flex items-center justify-center text-sm font-medium text-gray-900 line-clamp-1 bg-yellow-100 rounded-full px-2 py-1">{{ $entry->merchant?->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-3 py-4 text-sm">
                                <div class="w-60">
                                    <div class="bg-teal-50 rounded-lg border border-gray-200 overflow-hidden shadow-xs w-full">
                                        <div class="flex">
                                            <div class="w-[40px] min-w-[40px] bg-teal-500 text-white px-2 py-3 flex flex-col items-center justify-center text-center relative border-y border-teal-500">
                                                <div class="absolute -left-1 top-0 bottom-0 w-2 bg-white mask-[radial-gradient(circle_at_center,transparent_4px,black_5px)] mask-size-[8px_12px] mask-repeat-y"></div>
                                                <div class="h-5 w-5 flex items-center justify-center">
                                                    <svg class="size-5 stroke-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1.5 11h11L19 7M9 11v4m6-4v4"></path>
                                                    </svg>
                                                </div>
                                            </div>
    
                                            <div class="flex p-3 flex-col items-start justify-between gap-3 w-full">
                                                <div class="min-w-0">
                                                    <p class="flex flex-col items-start justify-start text-md font-semibold text-gray-900 leading-tight line-clamp-1">
                                                        {{ $entry->adminVoucher?->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="text-[0.65rem] text-gray-700 mt-1">
                                                        {{ $entry->adminVoucher?->voucher_code ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left font-medium text-gray-900">
                                <div class="flex flex-col items-start justify-start gap-1">
                                    <p>
                                        <span class="text-xs text-gray-500">Total Redeemed:</span> {{ $entry->total_redemptions }}
                                    </p>
                                    <p>
                                        <span class="text-xs text-gray-500">Cost per voucher:</span> ${{ number_format((float) $entry->adminVoucher?->amount_cost, 2) }}
                                    </p>
                                    <p>
                                        <span class="text-xs text-gray-500">Total Dispensed:</span> ${{ number_format((float) $entry->total_amount_dispensed, 2) }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left font-medium text-gray-900">
                                <div class="flex flex-col items-start justify-start gap-1">
                                    <p>
                                        <span class="text-xs text-gray-500">Total Reimbursed:</span> ${{ number_format($totalReimbursed, 2) }}
                                    </p>
                                    <p>
                                        <span class="text-xs text-gray-500">Balance:</span> 
                                        <span class="{{ $outstanding > 0 ? 'text-red-500' : 'text-gray-600' }}">${{ number_format($outstanding, 2) }}</span>
                                        
                                    </p>
                                </div>
                            </td>
                            <td class="px-1 py-4 whitespace-nowrap text-right align-super">
                                <div
                                    x-data="{
                                        open: false,
                                        position: { top: 0, right: 0 },
                                        setPosition($el) {
                                            const rect = $el.getBoundingClientRect();
                                            this.position = {
                                                top: rect.bottom,
                                                right: window.innerWidth - rect.right - window.scrollX
                                            };
                                        }
                                    }"
                                    class="relative inline-block text-left items-start"
                                    @click.away="open = false"
                                >
                                    <!-- 3-dots Button -->
                                    <button
                                        @click="open = !open; $nextTick(() => { if(open) setPosition($el); })"
                                        x-ref="button"
                                        class="inline-flex items-center justify-center text-gray-400 hover:text-gray-600 hover:scale-110 transition-all focus:outline-none focus:ring-0 focus:ring-offset-0 cursor-pointer"
                                        title="Actions"
                                    >
                                        <svg class="w-7 h-7 stroke-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div
                                        x-show="open"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        x-cloak
                                        :style="`position: fixed; top: ${position.top}px; right: ${position.right}px;`"
                                        class="w-52 z-[9999] origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    >
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            @if ($outstanding > 0)
                                                <button
                                                    wire:click="openReimburseModal({{ $entry->id }})"
                                                    @click="open = false"
                                                    class="flex w-full items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                                    role="menuitem"
                                                >
                                                    <svg class="w-4 h-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                    <span>Add Reimbursement</span>
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                            @else
                                                <div class="px-4 py-2 text-xs text-gray-500">
                                                    Fully reimbursed
                                                </div>
                                                <div class="border-t border-gray-100 my-1"></div>
                                            @endif
                                            <button
                                                wire:click="openHistoryModal({{ $entry->id }})"
                                                @click="open = false"
                                                class="flex w-full items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                                role="menuitem"
                                            >
                                                <svg class="w-4 h-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                <span>{{ $outstanding > 0 ? 'View Reimbursements' : 'Reimbursement History' }}</span>
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a 
                                                href="{{ route('admin.admin-voucher-ledger.transaction-history-pdf', $entry->id) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                @click="open = false"
                                                class="flex w-full items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                </svg>

                                                <span>Transaction History</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500">
                                No ledger entries found. Click "Sync Ledger" to populate from redemptions.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($entries->hasPages())
        <div class="mt-6 md:mx-0 mx-4">
            {{ $entries->links() }}
        </div>
    @endif

    <!-- Add Reimbursement Modal (teleported to body to avoid overflow/stacking issues) -->
    @if ($showReimburseModal && $selectedLedgerEntry)
        @teleport('body')
        <div
            class="fixed inset-0 z-[9999] overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-500/60 transition-opacity" wire:click="closeReimburseModal" aria-hidden="true"></div>
                <div class="relative z-10 mx-auto w-full max-w-lg transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">
                        Add Reimbursement
                    </h3>
                    <p class="mb-4 flex items-center justify-start gap-1">
                        <span class="text-sm text-gray-500">Merchant Name:</span>
                        <svg class="size-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        <span class="text-md text-gray-500">
                            {{ $selectedLedgerEntry->merchant?->name }}
                        </span>
                    </p>
                    <p class="flex items-center justify-start gap-1 text-gray-500 mb-4">
                        <span class="text-sm text-gray-500">Voucher Name:</span>
                        <span class="text-md text-gray-500">
                            {{ $selectedLedgerEntry->adminVoucher?->name }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-600 mb-4">
                        Outstanding Payable: <strong>${{ number_format((float) $selectedLedgerEntry->total_amount_dispensed - (float) ($selectedLedgerEntry->reimbursements_sum_amount ?? 0), 2) }}</strong>
                    </p>
                    <form wire:submit="saveReimbursement">
                        <div class="space-y-4">
                            <div>
                                <label for="reimbAmount" class="block text-sm font-medium text-gray-700">Amount</label>
                                <input
                                    type="number"
                                    id="reimbAmount"
                                    placeholder="Enter amount"
                                    wire:model="reimbAmount"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="mt-1 block w-full rounded-full text-gray-700 border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                >
                                @error('reimbAmount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reimbDate" class="block text-sm font-medium text-gray-700">Date</label>
                                <div class="relative mt-1">
                                    <input
                                        type="date"
                                        id="reimbDate"
                                        wire:model="reimbDate"
                                        required
                                        placeholder="Enter date"
                                        class="block w-full rounded-full text-gray-700 border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 pr-9"
                                    >
                                    <button
                                        type="button"
                                        onclick="(function(){var el=document.getElementById('reimbDate');try{if(el.showPicker)el.showPicker();else el.click();}catch(e){el.focus();el.click();}})()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Open calendar"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                    </button>
                                </div>
                                @error('reimbDate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reimbNotes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                                <textarea
                                    id="reimbNotes"
                                    wire:model="reimbNotes"
                                    rows="2"
                                    class="mt-1 block w-full rounded-lg text-gray-700 border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                ></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                wire:click="closeReimburseModal"
                                class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-full text-sm font-medium text-white bg-orange-500 hover:bg-orange-600"
                            >
                                Save Reimbursement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- Reimbursement History Modal -->
    @if ($showHistoryModal && $historyLedgerEntry)
        @teleport('body')
        <div
            class="fixed inset-0 z-[9999] overflow-y-auto"
            aria-labelledby="history-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-500/60 transition-opacity" wire:click="closeHistoryModal" aria-hidden="true"></div>
                <div class="relative z-10 mx-auto w-full max-w-2xl transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="history-modal-title">
                            Reimbursement History
                        </h3>
                        <button
                            type="button"
                            wire:click="closeHistoryModal"
                            class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">
                        {{ $historyLedgerEntry->merchant?->name }} — {{ $historyLedgerEntry->adminVoucher?->name }} ({{ $historyLedgerEntry->period_month->format('F Y') }})
                    </p>
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Added by</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($historyLedgerEntry->reimbursements->sortByDesc('reimbursed_at') as $reimbursement)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $reimbursement->reimbursed_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 whitespace-nowrap">
                                            ${{ number_format((float) $reimbursement->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $reimbursement->createdBy?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                                            {{ $reimbursement->notes ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                type="button"
                                                class="flex gap-1 items-center text-orange-600 hover:text-orange-800 text-xs font-medium cursor-pointer hover:underline hover:scale-105 transition-all duration-300"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                                <span class="text-sm">PDF</span>

                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                            No reimbursements recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 min-w-1/2 overflow-hidden border border-orange-100 rounded-lg">
                        <table class="min-w-full divide-y divide-orange-100">
                            <tbody class="bg-white divide-y divide-orange-100">
                                <tr class="bg-orange-50">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        Total Dispensed:
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        <strong>${{ number_format((float) $historyLedgerEntry->total_amount_dispensed, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr class="bg-orange-50">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        Total Reimbursed:
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        <strong>${{ number_format((float) ($historyLedgerEntry->reimbursements_sum_amount ?? 0), 2) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeHistoryModal"
                            class="px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>
