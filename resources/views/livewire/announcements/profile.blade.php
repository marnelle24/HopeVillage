<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $announcement->title }}
                </h2>
                <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto md:px-0 lg:px-0 px-4">
            <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-1 text-orange-600 hover:text-orange-900 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Announcements
            </a>

            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                @if($announcement->banner_url)
                    <div class="border-b border-gray-200">
                        @if($announcement->link_url)
                            <a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer" class="block">
                                <img src="{{ $announcement->banner_url }}" alt="{{ $announcement->title }}" class="w-full h-64 md:h-80 object-cover">
                            </a>
                        @else
                            <img src="{{ $announcement->banner_url }}" alt="{{ $announcement->title }}" class="w-full h-64 md:h-80 object-cover">
                        @endif
                    </div>
                @endif
                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2.5 py-1 text-sm font-semibold rounded-full {{ $announcement->status === 'published' ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-yellow-100 text-yellow-800 border border-yellow-400' }}">
                            {{ ucfirst($announcement->status) }}
                        </span>
                        @if($announcement->link_url)
                            <a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 text-sm rounded-full bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200">
                                Link: {{ Str::limit($announcement->link_url, 40) }}
                            </a>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mb-6">
                        @if($announcement->published_at)
                            Published {{ $announcement->published_at->format('d M Y') }}
                        @endif
                        @if($announcement->creator)
                            @if($announcement->published_at) · @endif
                            By {{ $announcement->creator->name }}
                        @endif
                    </p>
                    @if($announcement->body)
                        <div class="prose prose-sm sm:prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
                            {!! $announcement->body !!}
                        </div>
                    @else
                        <p class="text-gray-500 italic">No content.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
