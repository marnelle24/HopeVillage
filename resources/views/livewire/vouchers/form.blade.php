<div>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center lg:px-0 px-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $voucherCode ? __('Edit Voucher') : __('Create Voucher') }}
            </h2>
            <a href="{{ route('admin.vouchers.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                ← Back to Vouchers
            </a>
        </div>
    </x-slot>

    <div class="py-12 lg:px-0 px-4">
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
                                        <img src="{{ $existingVoucherImage }}" alt="Current voucher image" class="w-full max-w-md h-64 mb-4 object-cover rounded-lg border border-gray-300">
                                        <button 
                                            type="button" 
                                            wire:click="removeVoucherImage" 
                                            title="Remove Image"
                                            wire:confirm="Are you sure you want to remove the voucher image?"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            wire:loading.class.remove="opacity-100 cursor-pointer"
                                            class="flex gap-1 items-center justify-center absolute -top-1.5 -right-2 hover:scale-105 transition-all duration-300 text-red-600 hover:text-red-800 text-xs border border-red-300 bg-red-100 hover:bg-red-200/75 p-1 rounded-full"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
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
                            <!-- Merchant -->
                            <div class="mb-4">
                                <label for="merchant_id" class="block text-sm font-medium text-gray-700 mb-2">Merchant <span class="text-red-500">*</span></label>
                                <select 
                                    id="merchant_id"
                                    wire:model.blur="merchant_id" 
                                    class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('merchant_id') border-red-500 @enderror"
                                >
                                    <option value="">Select a Merchant</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                                @error('merchant_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                <input 
                                    placeholder="Voucher Name"
                                    type="text" 
                                    id="name"
                                    wire:model.blur="name" 
                                    class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
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
                                    class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                                ></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Discount Type, Value, and Usage Limit -->
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                                <div class="col-span-2">
                                    <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                                    <select 
                                        id="discount_type"
                                        wire:model.live="discount_type" 
                                        class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('discount_type') border-red-500 @enderror"
                                    >
                                        @foreach($discountTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('discount_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2">
                                    <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">Discount Value <span class="text-red-500">*</span></label>
                                    <input 
                                        placeholder="{{ $discount_type === 'percentage' ? '0.00' : '0.00' }}"
                                        type="number" 
                                        id="discount_value"
                                        wire:model.blur="discount_value" 
                                        step="0.01"
                                        min="0"
                                        {{ $discount_type === 'item' ? 'readonly' : '' }}
                                        class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('discount_value') border-red-500 @enderror {{ $discount_type === 'item' ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                    >
                                    @if($discount_type === 'item')
                                        <p class="text-xs text-gray-500 mt-1">
                                            100% off the price of the item
                                        </p>
                                    @elseif($discount_type === 'percentage')
                                        <p class="text-xs text-gray-500 mt-1">
                                            Enter percentage (e.g., 10 for 10%)
                                        </p>
                                    @elseif($discount_type === 'fixed')
                                        <p class="text-xs text-gray-500 mt-1">
                                            Enter fixed amount (e.g., 5.00 for $5)
                                        </p>
                                    @endif
                                    @error('discount_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
        
                                <!-- Usage Limit -->
                                <div class="col-span-1">
                                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                                    <input 
                                        placeholder="Max"
                                        type="number" 
                                        id="usage_limit"
                                        wire:model.blur="usage_limit" 
                                        min="1"
                                        class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('usage_limit') border-red-500 @enderror"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">
                                        (Optional)
                                    </p>
                                    @error('usage_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
        
                            </div>
        
                            <!-- Min Purchase and Max Discount -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-2">Minimum Purchase</label>
                                    <input 
                                        placeholder="0.00"
                                        type="number" 
                                        id="min_purchase"
                                        wire:model.blur="min_purchase" 
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('min_purchase') border-red-500 @enderror"
                                    >
                                    @error('min_purchase') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-2">Maximum Discount</label>
                                    <input 
                                        placeholder="0.00"
                                        type="number" 
                                        id="max_discount"
                                        wire:model.blur="max_discount" 
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('max_discount') border-red-500 @enderror"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Only applicable for percentage discounts</p>
                                    @error('max_discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
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
                                            class="w-full px-4 py-2 pr-10 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('valid_from') border-red-500 @enderror"
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
                                            class="w-full px-4 py-2 pr-10 border rounded-lg text-gray-700 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 @error('valid_until') border-red-500 @enderror"
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

                            <!-- Visible to (Type of Work) -->
                            <div class="mt-8 mb-4">
                                <div class="mb-2 flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Make this voucher available
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">(Select which member types can see this voucher. Leave all unchecked for visible to all.)</p>
                                </div>
                                <div class="border border-gray-300 rounded-lg p-4 @error('visibilityToTypeOfWork') border-red-500 @enderror">
                                    <div class="space-y-2">
                                        @foreach($typeOfWorkOptions ?? [] as $option)
                                            <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model.live="visibilityToTypeOfWork"
                                                    value="{{ $option }}"
                                                    class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                                                >
                                                <span class="ml-3 text-sm font-medium text-gray-900">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                @error('visibilityToTypeOfWork') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                @if(count($visibilityToTypeOfWork ?? []) > 0)
                                    <span class="text-xs text-gray-500 font-medium mt-2 block">Selected ({{ count($visibilityToTypeOfWork) }}):</span>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($visibilityToTypeOfWork ?? [] as $selected)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 border border-orange-300 text-orange-800 rounded-full text-xs">
                                                {{ $selected }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
        
                            <!-- Is Active -->
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="is_active" 
                                        class="w-6 h-6 rounded-none border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-md text-gray-700">Active</span>
                                </label>
                            </div>
        
                            <!-- Submit Buttons -->
                            <div class="flex justify-end gap-4 mb-4 mt-10">
                                <a 
                                    href="{{ route('admin.vouchers.index') }}" 
                                    class="px-6 py-2 border border-gray-300 rounded-full text-gray-700 text-lg hover:bg-gray-100 transition"
                                >
                                    Cancel
                                </a>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-orange-500 text-white text-lg rounded-full hover:bg-orange-600 transition"
                                >
                                    {{ $voucherCode ? 'Update' : 'Create' }} Voucher
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
