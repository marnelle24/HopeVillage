<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:justify-between justify-center items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Announcements') }}
                </h2>
                <a href="{{ route('admin.announcements.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Create Announcement
                </a>
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

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 text-gray-800 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-4">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search announcements..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                    </div>
                    <div class="md:col-span-2">
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:px-0 px-4">
                @forelse($announcements as $item)
                    @php
                        $statusClass = $item->status === 'published'
                            ? 'bg-green-100 border border-green-500 text-green-800'
                            : 'bg-yellow-100 border border-yellow-400 text-yellow-800';
                    @endphp
                    <div class="bg-white group overflow-hidden shadow-md flex flex-col rounded-t-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative">
                        <a href="{{ route('admin.announcements.profile', $item->id) }}" class="absolute inset-0 z-0"></a>
                        <div class="w-full h-[200px] border border-gray-300 rounded-t-2xl relative z-10 overflow-hidden bg-gray-100">
                            @if($item->banner_url)
                                <img
                                    src="{{ $item->banner_url }}"
                                    alt="{{ $item->title }}"
                                    class="w-full h-full object-cover rounded-t-2xl"
                                >
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center rounded-t-2xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 flex flex-col min-h-[180px] group-hover:bg-orange-50 transition-all relative z-10">
                            <a href="{{ route('admin.announcements.profile', $item->id) }}" class="flex-1">
                                <h3 class="group-hover:text-orange-600 transition-colors md:text-xl text-2xl font-bold text-gray-900 line-clamp-2 cursor-pointer">{{ $item->title }}</h3>
                                <span class="text-xs text-gray-500">Published {{ $item->published_at ? $item->published_at->format('d M Y') : 'N/A' }}</span>
                                @if($item->creator)
                                    <span class="text-xs text-gray-500"> · {{ $item->creator->name }}</span>
                                @endif
                            </a>
                            <div class="flex justify-between items-baseline mt-2">
                                <span class="px-2.5 py-1 inline-flex text-sm font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                    <a
                                        href="{{ route('admin.announcements.edit', $item->id) }}"
                                        title="Edit"
                                        class="bg-blue-700/60 hover:bg-sky-600 text-white p-2 rounded-full transition-all relative z-20"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>
                                    <button
                                        wire:click="delete({{ $item->id }})"
                                        title="Delete"
                                        wire:confirm="Are you sure you want to delete this announcement?"
                                        class="bg-red-600/60 hover:bg-red-700 text-white p-2 rounded-full transition-all relative z-20"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-12 border-dashed border-2 border-gray-200 rounded-lg bg-white">
                        No announcements found.
                        <p class="mt-4">
                            <a href="{{ route('admin.announcements.create') }}" class="text-orange-600 hover:text-orange-700 font-medium">Create your first announcement</a>
                        </p>
                    </div>
                @endforelse
            </div>

            @if($announcements->hasPages())
                <div class="mt-6 md:mx-0 mx-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
