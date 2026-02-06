<div>
    <div
        x-data="{ open: @entangle('open').live }"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
        @keydown.escape.window="$wire.close()"
        @click.self="$wire.close()"
        style="display: none;"
    >
        <div
            @click.stop
            class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative z-[10000]"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <button
                type="button"
                wire:click="close"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition"
                aria-label="Close"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">
                    Manually Add Activity
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Add an activity for <span class="font-medium text-gray-700">{{ $member->name }}</span> and award points.
                </p>

                @if($error)
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $error }}
                    </div>
                @endif

                @if($successMessage)
                    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ $successMessage }}
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="close"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition"
                        >
                            Close
                        </button>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-4">
                        <div>
                            <label for="pointSystemConfigId" class="block text-sm font-medium text-gray-700 mb-1">Activity & points <span class="text-red-500">*</span></label>
                            <select
                                id="pointSystemConfigId"
                                wire:model="pointSystemConfigId"
                                class="w-full px-3 py-2 border border-gray-300 text-gray-800 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                required
                            >
                                <option value="">— Select activity —</option>
                                @foreach($this->pointSystemConfigs as $config)
                                    <option value="{{ $config->id }}" class="text-gray-800">
                                        {{ $config->activityType?->description ?? $config->description ?? 'Activity' }}
                                        {{-- @if($config->location)
                                            ({{ $config->location->name }})
                                        @endif --}}
                                        — {{ $config->points }} pts
                                    </option>
                                @endforeach
                            </select>
                            {{-- Activity Date --}}
                            @error('pointSystemConfigId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="activityDateTime" class="block text-sm font-medium text-gray-700 mb-1">Date & time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input
                                    type="datetime-local"
                                    id="activityDateTime"
                                    x-ref="datetimeInput"
                                    wire:model="activityDateTime"
                                    class="w-full pl-3 pr-3 py-2 border border-gray-300 text-gray-800 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm [color-scheme:light]"
                                    required
                                >
                                <label
                                    for="activityDateTime"
                                    class="absolute inset-y-0 right-0 flex w-12 cursor-pointer items-center justify-center text-gray-400 hover:text-gray-600"
                                    @click.prevent="if ($refs.datetimeInput?.showPicker) $refs.datetimeInput.showPicker()"
                                >
                                </label>
                            </div>
                            @error('activityDateTime')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button
                                type="button"
                                wire:click="close"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="submit">Add Activity</span>
                                <span wire:loading wire:target="submit" class="inline-flex items-center gap-1">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
