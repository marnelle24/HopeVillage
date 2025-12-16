<div>
    <div class="mb-8">
        <p class="text-xl font-bold text-gray-700 mb-1">My Events</p>
        <p class="text-xs text-gray-600">Events you have registered for will appear here.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($registeredEvents as $registration)
            @php
                $event = $registration->event;
                if (!$event) {
                    continue;
                }

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
            @endphp

            <a href="{{ route('member.events.profile', $event->event_code) }}" class="group" aria-label="View {{ $event->title }}">
                <div class="bg-white hover:bg-orange-50 border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative">
                    <div class="h-48 w-full bg-gray-100 relative z-10">
                        @if(!empty($eventImageUrl ?? null))
                            <img
                                src="{{ $eventImageUrl ?? '' }}"
                                alt="{{ $event->title }}"
                                class="h-48 w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="h-48 w-full flex items-center justify-center text-xs text-gray-400">
                                No image
                            </div>
                        @endif
                    </div>
                    <div class="p-4 relative z-10">
                        <p class="text-md font-bold text-gray-900 leading-snug">
                            {{ \Illuminate\Support\Str::words($event->title, 8, '...') }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">{{ $schedule }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $event->location?->name ?? 'Unknown location' }}
                            @if($event->venue)
                                • {{ $event->venue }}
                            @endif
                        </p>
                        <p class="mt-3 text-[11px] text-gray-400">
                            Registered: {{ optional($registration->registered_at)->format('M d, Y g:i A') ?? '—' }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                No Event found
            </div>
        @endforelse
    </div>
</div>


