<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $categoryId ? __('Edit category') : __('Create category') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

            <a href="{{ route('admin.news-categories.index') }}" class="flex items-center gap-1 text-orange-600 hover:text-orange-900 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Categories
            </a>

            <form wire:submit="save" class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="name"
                        placeholder="Category name"
                        wire:model.blur="name"
                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('name') border-red-500 @enderror"
                    >
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-gray-400 text-xs">(optional, auto from name)</span></label>
                    <input
                        type="text"
                        id="slug"
                        placeholder="url-slug"
                        wire:model.blur="slug"
                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 @error('slug') border-red-500 @enderror"
                    >
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.news-categories.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 transition">
                        {{ $categoryId ? 'Update' : 'Create' }} Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
