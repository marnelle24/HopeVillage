<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg md:text-xl text-gray-800 leading-tight">
                    {{ $voucherCode ? __('Edit Admin Voucher') : __('Create Admin Voucher') }}
                </h2>
                <a href="{{ route('admin.admin-vouchers.index') }}" class="text-orange-500 font-medium hover:text-orange-600 hover:scale-105 transition-all duration-300">
                    <span class="flex items-center gap-1 text-sm md:text-base line-clamp-1 text-right">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        Back to Admin Vouchers
                    </span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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

            <form wire:submit="save">
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                    <div class="flex gap-4 lg:flex-row flex-col">
                        <div class="w-full lg:w-1/3">
                            <!-- Voucher Image Upload -->
                            <div class="mb-4">
                                <label for="voucherImage" class="block text-sm font-medium text-gray-700 mb-2">Voucher Image</label>
                                
                                @if($existingVoucherImage && !$voucherImage)
                                    <div class="mb-4 relative inline-block">
                                        <img src="{{ $existingVoucherImage }}" alt="Current Image" class="w-full max-w-md h-64 object-cover rounded-lg border border-gray-300">
                                        <button 
                                            type="button" 
                                            wire:click="removeVoucherImage"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition"
                                            title="Remove Image"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
        
                                @if($voucherImage)
                                    <div class="mt-2 mb-4">
                                        <img src="{{ $voucherImage->temporaryUrl() }}" alt="Preview" class="w-full max-w-md h-64 object-cover rounded-lg border border-gray-300">
                                    </div>
                                @endif
        
                                @if(!$voucherImage && !$existingVoucherImage)
                                    <div class="w-full max-w-md h-64 mb-4 bg-gray-200 rounded-lg flex flex-col items-center justify-center border border-gray-300">
                                        <p class="text-gray-400 text-sm">IMG</p>
                                        <p class="text-gray-400 text-sm">Upload</p>
                                    </div>
                                @endif
        
                                <input 
                                    type="file" 
                                    id="voucherImage"
                                    wire:model="voucherImage" 
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full max-w-md text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold bg-gray-200/50 border border-gray-300 rounded-lg file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200"
                                >
                                @error('voucherImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 2MB. Accepted formats: JPEG, PNG, WebP</p>
                            </div>
                        </div>

                        <div class="w-full lg:w-2/3">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input 
                            placeholder="Admin Voucher Name"
                            type="text" 
                            id="name"
                            wire:model.blur="name" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
                        >
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            placeholder="Description"
                            id="description"
                            wire:model.blur="description" 
                            rows="4"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Points Cost and Amount Cost -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="points_cost" class="block text-sm font-medium text-gray-700 mb-2">Points Cost <span class="text-red-500">*</span></label>
                            <input 
                                placeholder="0"
                                type="number" 
                                id="points_cost"
                                wire:model.blur="points_cost" 
                                min="0"
                                step="1"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('points_cost') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Points required for members to claim this voucher</p>
                            @error('points_cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="amount_cost" class="block text-sm font-medium text-gray-700 mb-2">Amount Cost</label>
                            <input 
                                placeholder="0.00"
                                type="number" 
                                id="amount_cost"
                                wire:model.blur="amount_cost" 
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('amount_cost') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Monetary cost of the voucher (optional)</p>
                            @error('amount_cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Allowed Merchants -->
                    <div class="mb-4">
                        <div class="mb-2 flex gap-1">
                            <label class="block text-sm font-medium text-gray-700">
                                Allowed Merchants <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">(Select one or more merchants where this voucher can be redeemed)</p>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto @error('selectedMerchants') border-red-500 @enderror">
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($merchants as $merchant)
                                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live.debounce.500ms="selectedMerchants"
                                            value="{{ $merchant->id }}"
                                            class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                                        >
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $merchant->name }}</p>
                                            @if($merchant->email)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $merchant->email }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('selectedMerchants') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @if(count($selectedMerchants) > 0)
                            <span class="text-xs text-gray-500 font-medium">Selected ({{ count($selectedMerchants) }}):</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedMerchants as $merchantId)
                                    @php
                                        $merchant = $merchants->firstWhere('id', $merchantId);
                                    @endphp
                                    @if($merchant)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 border border-orange-300 text-orange-800 rounded-full text-xs">
                                            {{ $merchant->name }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Valid From and Valid Until -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                            <div class="relative">
                                <input 
                                    type="datetime-local" 
                                    id="valid_from"
                                    wire:model.blur="valid_from" 
                                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('valid_from') border-red-500 @enderror"
                                >
                                <button type="button" onclick="(function(){var el=document.getElementById('valid_from');try{if(el.showPicker)el.showPicker();else el.click();}catch(e){el.focus();el.click();}})()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Open calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </button>
                            </div>
                            @error('valid_from') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                            <div class="relative">
                                <input 
                                    type="datetime-local" 
                                    id="valid_until"
                                    wire:model.blur="valid_until" 
                                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('valid_until') border-red-500 @enderror"
                                >
                                <button type="button" onclick="(function(){var el=document.getElementById('valid_until');try{if(el.showPicker)el.showPicker();else el.click();}catch(e){el.focus();el.click();}})()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Open calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </button>
                            </div>
                            @error('valid_until') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Usage Limit -->
                    <div class="mb-4">
                        <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                        <input 
                            placeholder="Leave empty for unlimited"
                            type="number" 
                            id="usage_limit"
                            wire:model.blur="usage_limit" 
                            min="1"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('usage_limit') border-red-500 @enderror"
                        >
                        @error('usage_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model.blur="is_active" 
                                class="w-6 h-6 rounded-none border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-md text-gray-700">Active</span>
                        </label>
                    </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4">
                        <a 
                            href="{{ route('admin.admin-vouchers.index') }}" 
                            class="px-6 py-2 border border-gray-300 rounded-full text-gray-700 text-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-orange-500 text-white cursor-pointer rounded-full text-lg hover:bg-orange-600 transition"
                        >
                            {{ $voucherCode ? 'Update' : 'Create' }} Voucher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

