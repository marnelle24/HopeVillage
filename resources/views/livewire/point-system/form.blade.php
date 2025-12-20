<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $configId ? __('Edit Point System Configuration') : __('Create Point System Configuration') }}
            </h2>
            <a href="{{ route('admin.point-system.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Point System
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
                    <!-- Activity Type -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <label for="activity_type_id" class="block text-sm font-medium text-gray-700">Activity Type <span class="text-red-500">*</span></label>
                            <button 
                                type="button"
                                wire:click="openActivityTypeModal"
                                class="text-xs text-orange-600 hover:text-orange-700 font-medium flex items-center gap-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create New Activity Type
                            </button>
                        </div>
                        <select 
                            id="activity_type_id"
                            wire:model.blur="activity_type_id" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('activity_type_id') border-red-500 @enderror"
                        >
                            <option value="">Select an Activity Type</option>
                            @foreach($activityTypes as $activityType)
                                <option value="{{ $activityType->id }}">{{ $activityType->name }} - {{ $activityType->description }}</option>
                            @endforeach
                        </select>
                        @error('activity_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @if (session()->has('activity_type_created'))
                            <p class="text-green-600 text-sm mt-1">{{ session('activity_type_created') }}</p>
                        @endif
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <select 
                            id="location_id"
                            wire:model.live="location_id" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('location_id') border-red-500 @enderror"
                        >
                            <option value="">No specific location (Global)</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Leave empty for global configuration</p>
                        @error('location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Amenity -->
                    <div class="mb-4">
                        <label for="amenity_id" class="block text-sm font-medium text-gray-700 mb-2">Amenity</label>
                        <select 
                            id="amenity_id"
                            wire:model.blur="amenity_id" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('amenity_id') border-red-500 @enderror"
                            @if(!$location_id) disabled @endif
                        >
                            <option value="">No specific amenity</option>
                            @foreach($amenities as $amenity)
                                <option value="{{ $amenity->id }}">{{ $amenity->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select a location first to filter amenities</p>
                        @error('amenity_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Points -->
                    <div class="mb-4">
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-2">Points <span class="text-red-500">*</span></label>
                        <input 
                            placeholder="Enter points amount"
                            type="number" 
                            id="points"
                            wire:model.blur="points" 
                            min="0"
                            step="1"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('points') border-red-500 @enderror"
                        >
                        <p class="text-xs text-gray-500 mt-1">Number of points to award for this activity</p>
                        @error('points') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            placeholder="Description of this point configuration"
                            id="description"
                            wire:model.blur="description" 
                            rows="4"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            href="{{ route('admin.point-system.index') }}" 
                            class="px-6 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 transition"
                        >
                            {{ $configId ? 'Update' : 'Create' }} Configuration
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Activity Type Modal -->
    <div 
        x-data="{ show: @entangle('showActivityTypeModal').live }"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        @keydown.escape.window="$wire.closeActivityTypeModal()"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeActivityTypeModal"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                @click.stop
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Activity Type</h3>
                    <button 
                        type="button"
                        wire:click="closeActivityTypeModal"
                        class="text-gray-400 hover:text-gray-500 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="createActivityType">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="newActivityTypeName" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text"
                            id="newActivityTypeName"
                            wire:model.blur="newActivityTypeName"
                            placeholder="e.g., account_verification"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('newActivityTypeName') border-red-500 @enderror"
                        >
                        @error('newActivityTypeName') 
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="newActivityTypeDescription" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea 
                            id="newActivityTypeDescription"
                            wire:model.blur="newActivityTypeDescription"
                            rows="3"
                            placeholder="Description of this activity type"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('newActivityTypeDescription') border-red-500 @enderror"
                        ></textarea>
                        @error('newActivityTypeDescription') 
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model.blur="newActivityTypeIsActive" 
                                class="w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <button 
                            type="button"
                            wire:click="closeActivityTypeModal"
                            class="px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 transition"
                        >
                            Create Activity Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

