<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:justify-between justify-center items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Events Management') }}
                </h2>
                <a href="{{ route('admin.locations.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg mr-2">
                    View Locations
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div 
                    x-data="{ 
                        show: @entangle('showMessage').live,
                        timeoutId: null
                    }"
                    x-init="
                        $watch('show', value => {
                            if (value && !timeoutId) {
                                timeoutId = setTimeout(() => {
                                    show = false;
                                    timeoutId = null;
                                }, 3000);
                            } else if (!value && timeoutId) {
                                clearTimeout(timeoutId);
                                timeoutId = null;
                            }
                        });
                        if (show) {
                            timeoutId = setTimeout(() => {
                                show = false;
                                timeoutId = null;
                            }, 3000);
                        }
                    "
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-out duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative md:mx-0 mx-4" 
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 text-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-3">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search events..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                    </div>
                    <div class="md:col-span-2">
                        <select 
                            wire:model.live="locationFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <select 
                            wire:model.live="statusFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:px-0 px-4">
                @forelse($events as $event)
                    @php
                        $thumbnailHeight = 'h-[200px]';
                        $isPastEvent = $event->end_date->isPast();
                        if ($isPastEvent) {
                            $status = 'Finished';
                            $statusClass = 'bg-gray-100 border border-gray-400 text-gray-800';
                        } else {
                            $status = ucfirst($event->status);
                            $statusClass = match($event->status) {
                                'published' => 'bg-green-100 border border-green-500 text-green-800',
                                'cancelled' => 'bg-red-100 border border-red-400 text-red-800',
                                'completed' => 'bg-gray-100 border border-gray-400 text-gray-800',
                                default => 'bg-yellow-100 border border-yellow-400 text-yellow-800',
                            };
                        }

                        // Location map thumbnail (static image)
                        $mapThumbnailUrl = null;
                        $location = $event->location;
                        if ($location) {
                            $apiKey = config('services.google_maps.api_key');
                            if ($apiKey) {
                                if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                                    $lat = $location->latitude;
                                    $lng = $location->longitude;
                                    $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=400x200&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                                } elseif (!empty($location->address)) {
                                    $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                    $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=400x200&markers=color:red|{$address}&key={$apiKey}";
                                }
                            }
                        }
                    @endphp
                    @if(!$event->location->deleted_at)
                        <div class="bg-white group overflow-hidden shadow-md flex flex-col rounded-t-2xl group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative">
                            <a href="{{ route('admin.events.profile', $event->event_code) }}" class="absolute inset-0 z-0"></a>
                            <div class="w-full {{ $thumbnailHeight }} border border-gray-300 rounded-t-2xl relative z-10">
                                @if($event->thumbnail_url)
                                    <img src="{{ $event->thumbnail_url }}" alt="{{ $event->title }}" class="w-full {{ $thumbnailHeight }} border-b border-gray-300 object-cover rounded-t-2xl">
                                @else
                                    <div class="w-full {{ $thumbnailHeight }} bg-linear-to-br from-indigo-100 to-purple-100 flex items-center justify-center rounded-t-2xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-indigo-300">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col h-full group-hover:bg-orange-50 transition-all duration-300 relative z-10">
                                <a href="{{ route('admin.events.profile', $event->event_code) }}" class="flex-1">
                                    <h3 class="group-hover:text-orange-400 transition-colors duration-300 md:text-lg text-xl font-bold text-gray-900 mb-2 line-clamp-2 cursor-pointer">{{ $event->title }}</h3>
                                    <table class="w-full">
                                        <tr>
                                            <td class="md:text-sm text-xs text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">{{ $event->location->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">
                                                {{ $event->start_date->format('d M Y') }}
                                                @if($event->start_date->format('Y-m-d') === $event->end_date->format('Y-m-d'))
                                                    {{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                                @else
                                                    - {{ $event->end_date->format('d M Y') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if($event->venue)
                                        <tr>
                                            <td class="text-sm text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">{{ $event->venue }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-sm text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">
                                                @if($event->max_participants && $event->max_participants > 0)
                                                    {{ $event->registrations->count() }} / {{ $event->max_participants }}
                                                @else
                                                    Open to all
                                                    <span class="text-sm text-gray-400 italic">(no limit)</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </a>
                                <div class="mt-2 flex justify-between items-baseline">
                                    <span class="px-2.5 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $status }}</span>

                                    <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                        <a 
                                            href="{{ route('admin.locations.events.edit', [$event->location->location_code, $event->id]) }}" 
                                            title="Edit Event"
                                            class="bg-blue-700/60 hover:bg-sky-600 hover:scale-105 text-white p-2 rounded-full transition-all duration-200 relative z-20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                        <button 
                                            wire:click="delete({{ $event->id }})" 
                                            title="Delete Event"
                                            wire:confirm="Are you sure you want to delete this event?"
                                            class="bg-red-600/60 hover:bg-red-700 hover:scale-105 text-white p-2 rounded-full transition-all duration-200 relative z-20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                        No events found.
                        <p class="mt-4">
                            <a href="{{ route('admin.locations.index') }}" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all duration-300 text-sm border border-gray-300 rounded-lg p-2">View Locations</a>
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="mt-6 md:mx-0 mx-4">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
