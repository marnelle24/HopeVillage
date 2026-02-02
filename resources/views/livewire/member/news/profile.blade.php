<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20">
    <div class="px-4 mt-6">
        <a href="{{ route('member.news') }}" class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 font-medium mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to News
        </a>

        <article class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
            @if($news->thumbnail_url)
                <div class="w-full aspect-video overflow-hidden bg-gray-100">
                    <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-4 md:p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-3">{{ $news->title }}</h1>
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($news->published_at)
                        <span class="text-sm text-gray-500">{{ $news->published_at->format('M d, Y') }}</span>
                    @endif
                    @if($news->creator)
                        <span class="text-sm text-gray-500">· {{ $news->creator->name }}</span>
                    @endif
                </div>
                @if($news->body)
                    <div class="mb-6 prose prose-sm max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-orange-600">
                        {!! $news->body !!}
                    </div>
                    @if($news->categories->isNotEmpty())
                        @foreach($news->categories as $cat)
                            <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ $cat->name }}</span>
                        @endforeach
                    @endif
                @else
                    <p class="text-gray-500 italic">No content.</p>
                @endif
            </div>
        </article>
    </div>
</div>
