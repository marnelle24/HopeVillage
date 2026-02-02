<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $news->title }}
                </h2>
                <a href="{{ route('admin.news.edit', $news->id) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto md:px-0 lg:px-0 px-4">
            <a href="{{ route('admin.news.index') }}" class="flex items-center gap-1 text-orange-600 hover:text-orange-900 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to News
            </a>

            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                @if($news->thumbnail_url)
                    <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="w-full h-64 md:h-80 object-cover border-b border-gray-200">
                @endif
                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2.5 py-1 text-sm font-semibold rounded-full {{ $news->status === 'published' ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-yellow-100 text-yellow-800 border border-yellow-400' }}">
                            {{ ucfirst($news->status) }}
                        </span>
                        @foreach($news->categories as $cat)
                            <span class="px-2.5 py-1 text-sm rounded-full bg-gray-100 text-gray-700 border border-gray-300">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mb-6">
                        {{-- {{ $news->created_at->format('d M Y') }} --}}
                        @if($news->published_at)
                            · Published {{ $news->published_at->format('d M Y') }}
                        @endif
                        @if($news->creator)
                            · By {{ $news->creator->name }}
                        @endif
                    </p>
                    @if($news->body)
                        <div class="prose prose-sm sm:prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
                            {!! $news->body !!}
                        </div>
                    @else
                        <p class="text-gray-500 italic">No content.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
