<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:justify-between justify-center items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('News Categories') }}
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.news.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Back to News
                    </a>
                    <a href="{{ route('admin.news-categories.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Add Category
                    </a>
                </div>
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
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative md:mx-0 mx-4"
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search categories..."
                    class="w-full md:max-w-md px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                >
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg md:mx-0 mx-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $cat)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cat->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cat->slug ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cat->news_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.news-categories.edit', $cat->id) }}" class="text-orange-600 hover:text-orange-900 mr-4">Edit</a>
                                    <button wire:click="delete({{ $cat->id }})" wire:confirm="Are you sure? Articles will be unlinked from this category." class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No categories yet. <a href="{{ route('admin.news-categories.create') }}" class="text-orange-600">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="mt-6 md:mx-0 mx-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
