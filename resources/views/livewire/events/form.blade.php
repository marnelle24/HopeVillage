<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Events
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $eventId ? __('Edit Event') : __('Create Event') }} - {{ $location->name }}
                </h2>
            </div>
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
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                        <input 
                            placeholder="Event Title"
                            type="text" 
                            id="title"
                            wire:model.blur="title" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                        >
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            placeholder="Event Description"
                            id="description"
                            wire:model.blur="description" 
                            rows="4"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                id="start_date"
                                wire:model.blur="start_date" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror"
                            >
                            @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                            <input 
                                type="time" 
                                id="start_time"
                                wire:model.blur="start_time" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-500 @enderror"
                            >
                            @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                id="end_date"
                                wire:model.blur="end_date" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 @enderror"
                            >
                            @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time <span class="text-red-500">*</span></label>
                            <input 
                                type="time" 
                                id="end_time"
                                wire:model.blur="end_time" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_time') border-red-500 @enderror"
                            >
                            @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Venue -->
                    <div class="mb-4">
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                        <input 
                            placeholder="Event Venue"
                            type="text" 
                            id="venue"
                            wire:model.blur="venue" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('venue') border-red-500 @enderror"
                        >
                        @error('venue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Max Participants -->
                    <div class="mb-4">
                        <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                        <input 
                            placeholder="Maximum number of participants"
                            type="number" 
                            id="max_participants"
                            wire:model.blur="max_participants" 
                            min="1"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_participants') border-red-500 @enderror"
                        >
                        @error('max_participants') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Event Code -->
                    <div class="mb-4">
                        <label for="event_code" class="block text-sm font-medium text-gray-700 mb-2">Event Code (for QR)</label>
                        <input 
                            placeholder="Leave empty to auto-generate"
                            type="text" 
                            id="event_code"
                            wire:model.blur="event_code" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('event_code') border-red-500 @enderror"
                        >
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate a unique code. Format: EVT-XXXXXXXX</p>
                        @error('event_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select 
                            id="status"
                            wire:model.blur="status" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
                        >
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4">
                        <a 
                            href="{{ route('admin.locations.events.index', $location->location_code) }}" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                        >
                            {{ $eventId ? 'Update' : 'Create' }} Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
