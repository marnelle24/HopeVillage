<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Upcoming Events
        </h2>
        <div class="space-y-3">
            @forelse($upcomingEvents as $event)
                <div class="flex items-start gap-3 p-3 rounded-lg bg-base-200 hover:bg-base-300 transition-colors">
                    <div class="shrink-0">
                        <div class="badge badge-info">{{ $event->start_date->format('M d') }}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm truncate">{{ $event->title }}</h3>
                        <p class="text-xs text-base-content/70">{{ $event->location->name ?? 'N/A' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-base-content/60">
                                {{ $event->registrations_count }} registered
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center mt-4 text-base-content/70 bg-base-200 py-8 border border-base-300 rounded-lg p-4">No upcoming events</p>
            @endforelse
        </div>
    </div>
</div>
