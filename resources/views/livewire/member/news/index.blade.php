<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20">
    <div class="px-4 mt-6">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-900 mb-4">News & Articles</h1>
            <label class="relative block">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="w-full pl-10 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400"
                    placeholder="Search news & articles..."
                />
            </label>
            <p class="text-xs text-base-content/60 mt-4 ml-2 italic text-left">
                {{ $news->total() }} {{ $news->total() === 1 ? 'article' : 'articles' }} found
            </p>
        </div>

        <div class="space-y-4">
            @forelse($news as $item)
                <a href="{{ route('member.news.profile', $item->slug) }}" class="block bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 border border-gray-100">
                    <div class="w-full h-40 overflow-hidden bg-gray-100 relative" x-data="{ loaded: false }">
                        @if($item->thumbnail_url)
                            <img
                                src="{{ $item->thumbnail_url }}"
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                @load="loaded = true"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-2">
                        <h2 class="font-bold text-gray-900 line-clamp-2 text-2xl mb-2 px-2">{{ $item->title }}</h2>
                        <div class="flex gap-2 flex-col text-xs text-gray-500 px-2 mb-2">
                            @if($item->published_at)
                                <span class="text-gray-500 text-sm">Published on: {{ $item->published_at->format('M d, Y') }}</span>
                            @endif
                            @if($item->categories->isNotEmpty())
                                <div class="flex flex-col">
                                    <span class="text-gray-500 text-sm">Categories:</span>
                                    <span class="text-gray-500 italic text-sm">
                                        {{ $item->categories->pluck('name')->join(', ') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No news yet</h3>
                    <p class="text-sm text-gray-500">
                        @if($search)
                            Try a different search.
                        @else
                            Check back later for updates from Hope Village.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if($news->hasPages())
            <div class="mt-6">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
