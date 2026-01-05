<div class="pb-16">
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
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-full text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                        >
                    </div>
                    <div>
                        <select 
                            wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-full text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                        >
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-4 md:px-0 px-4">
                @forelse($vouchers as $voucher)
                    <livewire:merchant.vouchers.card :voucher-code="$voucher->voucher_code" :key="'voucher-' . $voucher->id" />
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

    <!-- QR Code Full Screen Modal -->
    <div
        x-data="{ 
            open: false,
            qrCode: '',
            qrImage: '',
            title: '',
            init() {
                this.$watch('open', (value) => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });
                
                window.addEventListener('open-qr-modal', (e) => {
                    this.qrCode = e.detail.qrCode;
                    this.qrImage = e.detail.qrImage;
                    this.title = e.detail.title;
                    this.open = true;
                });
            }
        }"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[9999] bg-gray-300/50 flex items-center justify-center p-4"
        @keydown.escape.window="open = false"
        @click.self="open = false"
        style="display: none;"
    >
        <div
            @click.stop
            class="bg-white rounded-lg shadow-2xl max-w-md w-full p-8 relative"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Close button -->
            <button
                @click="open = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition"
                aria-label="Close"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="title"></h3>
                <p class="text-sm text-gray-600 mb-6" x-text="'Code: ' + qrCode"></p>
                
                <div class="flex justify-center mb-6">
                    <img 
                        :src="qrImage" 
                        :alt="'QR Code for ' + qrCode"
                        class="w-80 h-80 border-4 border-gray-200 rounded-lg bg-white p-4 shadow-lg"
                    >
                </div>
                
                <div class="flex items-center justify-center gap-2 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="font-mono font-semibold" x-text="qrCode"></span>
                </div>
                
                <button
                    @click="open = false"
                    class="mt-6 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
