<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg md:text-xl text-gray-800 leading-tight">
                    {{ $settingId ? __('Edit Setting') : __('Create Setting') }}
                </h2>
                <a href="{{ route('admin.settings.index') }}" class="text-orange-500 font-medium hover:text-orange-600 hover:scale-105 transition-all duration-300">
                    <span class="flex items-center gap-1 text-sm md:text-base line-clamp-1 text-right">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        Back to Settings
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
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            placeholder="Setting Name"
                            type="text" 
                            id="name"
                            wire:model.blur="name" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
                        >
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Display name for this setting.</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            placeholder="Setting description"
                            id="description"
                            wire:model.blur="description" 
                            rows="3"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional description explaining what this setting does.</p>
                    </div>

                    <!-- Key -->
                    <div class="mb-4">
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                            Key <span class="text-red-500">*</span>
                        </label>
                        <input 
                            placeholder="setting_key_name"
                            type="text" 
                            id="key"
                            wire:model.blur="key" 
                            @if($settingId) disabled @endif
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('key') border-red-500 @enderror @if($settingId) bg-gray-100 cursor-not-allowed @endif"
                        >
                        @error('key') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Setting key (unique identifier). Cannot be changed after creation.</p>
                    </div>

                    <!-- Value -->
                    <div class="mb-4">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <textarea 
                            placeholder="Setting value"
                            id="value"
                            wire:model.blur="value" 
                            rows="6"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500 @error('value') border-red-500 @enderror"
                        ></textarea>
                        @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Setting value. Can be left empty.</p>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model.blur="status" 
                                class="w-6 h-6 rounded-none border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-md text-gray-700">Active</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-8">Enable or disable this setting.</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4">
                        <a 
                            href="{{ route('admin.settings.index') }}" 
                            class="px-6 py-2 border border-gray-300 rounded-full text-gray-700 text-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-orange-500 text-white cursor-pointer rounded-full text-lg hover:bg-orange-600 transition"
                        >
                            {{ $settingId ? 'Update' : 'Create' }} Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
