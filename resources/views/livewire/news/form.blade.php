<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $newsId ? __('Update') . ' "' . trim($title) . '"' : __('Create news article') }}
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

            <a href="{{ route('admin.news.index') }}" class="flex items-center gap-1 text-orange-600 hover:text-orange-900 mb-6 md:mx-0 mx-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to News
            </a>

            <form wire:submit="save">
                <div class="flex gap-4 lg:flex-row flex-col md:px-0 px-4">
                    <div class="lg:w-1/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Thumbnail</label>
                            @if($existingThumbnail && !$thumbnail)
                                <div class="mb-4 relative">
                                    <img src="{{ $existingThumbnail }}" alt="Current thumbnail" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                                    <button
                                        type="button"
                                        wire:click="removeThumbnail"
                                        title="Remove Thumbnail"
                                        wire:confirm="Are you sure you want to remove the thumbnail?"
                                        wire:loading.attr="disabled"
                                        class="flex gap-1 items-center justify-center absolute -top-1.5 -right-2 hover:scale-105 transition-all text-red-600 hover:text-red-800 text-xs border border-red-300 bg-red-100 hover:bg-red-200/75 p-1 rounded-full"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            @if($thumbnail)
                                <div class="mt-2">
                                    <img src="{{ $thumbnail->temporaryUrl() }}" alt="Preview" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                                </div>
                            @endif
                            @if(!$thumbnail && !$existingThumbnail)
                                <div class="w-full h-64 mb-4 bg-gray-200 rounded-lg flex flex-col items-center justify-center border border-gray-300">
                                    <p class="text-gray-400 text-sm">IMG</p>
                                    <p class="text-gray-400 text-sm">Upload</p>
                                </div>
                            @endif
                            <input
                                type="file"
                                wire:model="thumbnail"
                                accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold bg-gray-200/50 border border-gray-300 rounded-lg file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-100"
                            >
                            @error('thumbnail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 mt-4">
                            <label class="flex items-center justify-between text-sm font-medium text-gray-700 mb-2">
                                Categories
                                @if(!$categories->isEmpty())
                                    <a href="{{ route('admin.news-categories.index') }}" class="text-orange-600">+ Add New</a>
                                @endif
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @forelse($categories as $cat)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model="categoryIds" value="{{ $cat->id }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 text-center">
                                        No categories yet. <br>
                                        <a href="{{ route('admin.news-categories.index') }}" class="text-orange-600">Manage categories</a>
                                    </p>
                                @endforelse
                            </div>
                            @error('categoryIds') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            <div class="relative">
                                <input type="datetime-local" id="published_at" wire:model.blur="published_at" class="w-full px-4 py-2 pr-10 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('published_at') border-red-500 @enderror">
                                <button type="button" onclick="(function(){var el=document.getElementById('published_at');try{if(el.showPicker)el.showPicker();else el.click();}catch(e){el.focus();el.click();}})()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Open calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </button>
                            </div>
                            @error('published_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="lg:w-2/3 w-full">
                        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    id="title"
                                    placeholder="Article Title"
                                    wire:model.blur="title"
                                    class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('title') border-red-500 @enderror"
                                >
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-gray-400 text-xs">(optional, auto from title)</span></label>
                                <input
                                    type="text"
                                    id="slug"
                                    placeholder="url-slug"
                                    wire:model.blur="slug"
                                    class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('slug') border-red-500 @enderror"
                                >
                                @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Body</label>
                                <x-editor wire:model="body" class="text-gray-900" />
                                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-4 pt-4">
                                <a href="{{ route('admin.news.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 transition">
                                    {{ $newsId ? 'Update' : 'Create' }} News
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
