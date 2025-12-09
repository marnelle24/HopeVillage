<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $amenityId ? __('Edit Amenity') : __('Create Amenity') }}
            </h2>
            <a href="{{ route('admin.amenities.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Amenities
            </a>
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
                <div class="flex gap-4 lg:flex-row flex-col">
                    <!-- Left Side - Image Upload -->
                    <div class="lg:w-1/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                            
                            <!-- Existing Images -->
                            @if(count($existingImages) > 0)
                                <div class="mb-4 space-y-2">
                                    @foreach($existingImages as $index => $image)
                                        <div class="relative">
                                            <img src="{{ $image }}" alt="Existing image" class="w-full h-32 object-cover rounded-lg border border-gray-300">
                                            <button 
                                                type="button" 
                                                wire:click="removeImage({{ $index }})" 
                                                title="Remove Image"
                                                wire:confirm="Are you sure you want to remove this image?"
                                                class="absolute -top-1.5 -right-2 hover:scale-105 transition-all duration-300 text-red-600 hover:text-red-800 text-xs border border-red-300 bg-red-100 hover:bg-red-200/75 p-1 rounded-full"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- New Image Previews -->
                            @if($images)
                                <div class="mb-4 space-y-2">
                                    @foreach($images as $index => $image)
                                        <div class="relative">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-full h-32 object-cover rounded-lg border border-gray-300">
                                            <button 
                                                type="button" 
                                                wire:click="removeNewImage({{ $index }})" 
                                                title="Remove Image"
                                                class="absolute -top-1.5 -right-2 hover:scale-105 transition-all duration-300 text-red-600 hover:text-red-800 text-xs border border-red-300 bg-red-100 hover:bg-red-200/75 p-1 rounded-full"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(count($existingImages) == 0 && !$images)
                                <div class="w-full h-32 mb-4 bg-gray-200 rounded-lg flex flex-col items-center justify-center border border-gray-300">
                                    <p class="text-gray-400 text-sm">IMG</p>
                                    <p class="text-gray-400 text-sm">Upload</p>
                                </div>
                            @endif

                            <input 
                                type="file" 
                                wire:model="images" 
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold bg-gray-200/50 border border-gray-300 rounded-lg file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-100"
                            >
                            @error('images.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Right Side - Form Fields -->
                    <div class="lg:w-2/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                            <!-- Location -->
                            <div class="mb-4">
                                <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                                <select 
                                    id="location_id"
                                    wire:model.blur="location_id" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('location_id') border-red-500 @enderror"
                                >
                                    <option value="">Select a Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                <input 
                                    placeholder="Amenity Name"
                                    type="text" 
                                    id="name"
                                    wire:model.blur="name" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Type -->
                            <div class="grid grid-cols-1 {{ $type === 'Others' ? 'md:grid-cols-2' : 'md:grid-cols-1' }} gap-4 mb-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                                    <select 
                                        id="type"
                                        wire:model.live.debounce.500ms="type" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror"
                                    >
                                        @foreach($types as $typeOption)
                                            <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @if($type === 'Others')
                                    <div>
                                        <label for="typeOtherValue" class="block text-sm font-medium text-gray-700 mb-2">Specify Other Type <span class="text-red-500">*</span></label>
                                        <input 
                                            placeholder="e.g., Archery Field"
                                            type="text" 
                                            id="typeOtherValue"
                                            wire:model.blur="typeOtherValue" 
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('typeOtherValue') border-red-500 @enderror"
                                        >
                                        @error('typeOtherValue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif
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

                            <!-- Capacity and Hourly Rate -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                                    <input 
                                        placeholder="Capacity"
                                        type="number" 
                                        id="capacity"
                                        wire:model.blur="capacity" 
                                        min="1"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('capacity') border-red-500 @enderror"
                                    >
                                    @error('capacity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">Hourly Rate</label>
                                    <input 
                                        placeholder="0.00"
                                        type="number" 
                                        id="hourly_rate"
                                        wire:model.blur="hourly_rate" 
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('hourly_rate') border-red-500 @enderror"
                                    >
                                    @error('hourly_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Is Bookable -->
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="is_bookable" 
                                        class="w-6 h-6 rounded-none border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-md text-gray-700">Bookable</span>
                                </label>
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
                                    href="{{ route('admin.amenities.index') }}" 
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </a>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                                >
                                    {{ $amenityId ? 'Update' : 'Create' }} Amenity
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
