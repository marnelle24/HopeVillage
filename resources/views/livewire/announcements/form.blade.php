<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $announcementId ? __('Update') . ' "' . trim($title) . '"' : __('Create announcement') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div
                    x-data="{ show: @entangle('showMessage').live, timeoutId: null }"
                    x-init="
                        $watch('show', value => {
                            if (value && !timeoutId) { timeoutId = setTimeout(() => { show = false; timeoutId = null; }, 3000); }
                            else if (!value && timeoutId) { clearTimeout(timeoutId); timeoutId = null; }
                        });
                        if (show) { timeoutId = setTimeout(() => { show = false; timeoutId = null; }, 3000); }
                    "
                    x-show="show"
                    x-transition
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-1 text-orange-600 hover:text-orange-900 mb-6 md:mx-0 mx-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Announcements
            </a>

            <form wire:submit="save">
                <div class="flex gap-4 lg:flex-row flex-col md:px-0 px-4">
                    <div class="lg:w-1/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banner / Image</label>
                            @if($existingBanner && !$banner)
                                <div class="mb-4 relative">
                                    <img src="{{ $existingBanner }}" alt="Current banner" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                                    <button
                                        type="button"
                                        wire:click="removeBanner"
                                        title="Remove Banner"
                                        wire:confirm="Are you sure you want to remove the banner?"
                                        wire:loading.attr="disabled"
                                        class="flex gap-1 items-center justify-center absolute -top-1.5 -right-2 hover:scale-105 transition-all text-red-600 hover:text-red-800 text-xs border border-red-300 bg-red-100 hover:bg-red-200/75 p-1 rounded-full"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            @if($banner)
                                <div class="mt-2">
                                    <img src="{{ $banner->temporaryUrl() }}" alt="Preview" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                                </div>
                            @endif
                            @if(!$banner && !$existingBanner)
                                <div class="w-full h-64 mb-4 bg-gray-200 rounded-lg flex flex-col items-center justify-center border border-gray-300">
                                    <p class="text-gray-400 text-sm">Banner</p>
                                    <p class="text-gray-400 text-sm">Upload</p>
                                </div>
                            @endif
                            <input
                                type="file"
                                wire:model="banner"
                                accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold bg-gray-200/50 border border-gray-300 rounded-lg file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-100"
                            >
                            @error('banner') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label for="link_url" class="block text-sm font-medium text-gray-700 mb-2">Link when banner is clicked</label>
                            <input
                                type="url"
                                id="link_url"
                                placeholder="https://..."
                                wire:model.blur="link_url"
                                class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('link_url') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Optional. Users will be redirected here when they click the banner in the lightbox.</p>
                            @error('link_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" wire:model.blur="status" class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('status') border-red-500 @enderror">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published at</label>
                            <input type="datetime-local" id="published_at" wire:model.blur="published_at" class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('published_at') border-red-500 @enderror">
                            @error('published_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">Starts at (optional)</label>
                            <input type="datetime-local" id="starts_at" wire:model.blur="starts_at" class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('starts_at') border-red-500 @enderror">
                            @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">Ends at (optional)</label>
                            <input type="datetime-local" id="ends_at" wire:model.blur="ends_at" class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('ends_at') border-red-500 @enderror">
                            @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="lg:w-2/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    id="title"
                                    placeholder="Announcement Title"
                                    wire:model.blur="title"
                                    class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('title') border-red-500 @enderror"
                                >
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Body</label>
                                <x-editor wire:model="body" class="text-gray-900" />
                                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-4 pt-4">
                                <a href="{{ route('admin.announcements.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 transition">
                                    {{ $announcementId ? 'Update' : 'Create' }} Announcement
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
