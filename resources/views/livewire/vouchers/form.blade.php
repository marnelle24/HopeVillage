<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $voucherCode ? __('Edit Voucher') : __('Create Voucher') }}
            </h2>
            <a href="{{ route('admin.vouchers.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Vouchers
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <!-- Merchant -->
                    <div class="mb-4">
                        <label for="merchant_id" class="block text-sm font-medium text-gray-700 mb-2">Merchant <span class="text-red-500">*</span></label>
                        <select 
                            id="merchant_id"
                            wire:model.blur="merchant_id" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('merchant_id') border-red-500 @enderror"
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
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
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
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Discount Type and Value -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                            <select 
                                id="discount_type"
                                wire:model.live="discount_type" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount_type') border-red-500 @enderror"
                            >
                                @foreach($discountTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('discount_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">Discount Value <span class="text-red-500">*</span></label>
                            <input 
                                placeholder="{{ $discount_type === 'percentage' ? '0.00' : '0.00' }}"
                                type="number" 
                                id="discount_value"
                                wire:model.blur="discount_value" 
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('discount_value') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $discount_type === 'percentage' ? 'Enter percentage (e.g., 10 for 10%)' : 'Enter fixed amount (e.g., 5.00 for $5)' }}
                            </p>
                            @error('discount_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('min_purchase') border-red-500 @enderror"
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
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_discount') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Only applicable for percentage discounts</p>
                            @error('max_discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Valid From and Valid Until -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                            <input 
                                type="datetime-local" 
                                id="valid_from"
                                wire:model.blur="valid_from" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('valid_from') border-red-500 @enderror"
                            >
                            @error('valid_from') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                            <input 
                                type="datetime-local" 
                                id="valid_until"
                                wire:model.blur="valid_until" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('valid_until') border-red-500 @enderror"
                            >
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
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('usage_limit') border-red-500 @enderror"
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

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4">
                        <a 
                            href="{{ route('admin.vouchers.index') }}" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                        >
                            {{ $voucherCode ? 'Update' : 'Create' }} Voucher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
