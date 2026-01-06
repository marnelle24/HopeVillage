<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $voucherCode ? __('Edit Admin Voucher') : __('Create Admin Voucher') }}
            </h2>
            <a href="{{ route('admin.admin-vouchers.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Admin Vouchers
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

                    <!-- Points Cost -->
                    <div class="mb-4">
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

                    <!-- Allowed Merchants -->
                    <div class="mb-4">
                        <label for="merchants" class="block text-sm font-medium text-gray-700 mb-2">Allowed Merchants <span class="text-red-500">*</span></label>
                        <select 
                            id="merchants"
                            wire:model.blur="selectedMerchants" 
                            multiple
                            size="8"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('selectedMerchants') border-red-500 @enderror"
                        >
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple merchants</p>
                        @error('selectedMerchants') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @if(count($selectedMerchants) > 0)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($selectedMerchants as $merchantId)
                                    @php
                                        $merchant = $merchants->firstWhere('id', $merchantId);
                                    @endphp
                                    @if($merchant)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-sm">
                                            {{ $merchant->name }}
                                            <button type="button" wire:click="$set('selectedMerchants', array_values(array_filter($selectedMerchants, fn($id) => $id != $merchantId)))" class="text-indigo-600 hover:text-indigo-800">
                                                ×
                                            </button>
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
                            <input 
                                type="datetime-local" 
                                id="valid_from"
                                wire:model.blur="valid_from" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('valid_from') border-red-500 @enderror"
                            >
                            @error('valid_from') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                            <input 
                                type="datetime-local" 
                                id="valid_until"
                                wire:model.blur="valid_until" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('valid_until') border-red-500 @enderror"
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

