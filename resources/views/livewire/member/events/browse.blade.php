<div id="events">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <p class="text-xl font-bold text-gray-700 mb-1">Events</p>
            <p class="text-xs text-gray-600">Browse upcoming events and tap Join to register.</p>
        </div>
    </div>
    <div class="w-full mb-8">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search events..."
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($events as $event)
            @php
                $start = $event->start_date;
                $end = $event->end_date;
                $schedule = 'TBA';
                if ($start && $end) {
                    $schedule = $start->format('M d, Y');
                    if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
                        $schedule .= ' • ' . $start->format('g:i A') . ' - ' . $end->format('g:i A');
                    } else {
                        $schedule = $start->format('M d, Y g:i A') . ' - ' . $end->format('M d, Y g:i A');
                    }
                } elseif ($start) {
                    $schedule = $start->format('M d, Y g:i A');
                }

                $isFull = $event->max_participants && $event->max_participants > 0
                    ? ($event->registrations_count >= $event->max_participants)
                    : false;
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative">
                @php
                    $eventImageUrl = $event->thumbnail_url ?? null;
                    if (!$eventImageUrl) {
                        $apiKey = config('services.google_maps.api_key');
                        $location = $event->location;
                        if ($apiKey && $location) {
                            if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                                $lat = $location->latitude;
                                $lng = $location->longitude;
                                $eventImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=420x220&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                            } elseif (!empty($location->address)) {
                                $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                $eventImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=420x220&markers=color:red|{$address}&key={$apiKey}";
                            }
                        }
                    }
                @endphp

                <div class="h-48 w-full bg-gray-100 relative">
                    @if(!empty($eventImageUrl ?? null))
                        <img src="{{ $eventImageUrl ?? '' }}" alt="{{ $event->title }}" class="h-48 w-full object-cover" loading="lazy">
                    @else
                        <div class="h-48 w-full flex items-center justify-center text-xs text-gray-400">
                            No image
                        </div>
                    @endif
                </div>

                <div class="p-4 relative z-10 group-hover:bg-orange-50 group">
                    <p class="text-lg font-bold text-gray-900 leading-snug">
                        {{ \Illuminate\Support\Str::words($event->title, 8, '...') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ $schedule }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $event->location?->name ?? 'Unknown location' }}
                        @if($event->venue)
                            • {{ $event->venue }}
                        @endif
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <p class="text-xs text-gray-500 border border-gray-200 rounded-lg px-2 py-1">
                            @if($event->max_participants && $event->max_participants > 0)
                                {{ $event->registrations_count }} / {{ $event->max_participants }}
                            @else
                                Open to all
                            @endif
                        </p>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('member.events.profile', $event->event_code) }}" class="flex gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white transition disabled:opacity-50 relative z-20">
                                View
                            </a>
                            @if($event->is_registered)
                                <button type="button" disabled class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-orange-500 border border-orange-200">
                                    Liked
                                </button>
                            @elseif($isFull)
                                <button type="button" disabled class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-orange-500 border border-orange-200">
                                    Full
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="join({{ $event->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-orange-500 hover:bg-orange-600 text-white transition disabled:opacity-50 relative z-20"
                                >
                                    <span wire:loading.remove wire:target="join({{ $event->id }})">Like</span>
                                    <span wire:loading wire:target="join({{ $event->id }})">liking...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                No upcoming events found.
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @endif
</div>


