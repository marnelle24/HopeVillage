<div
    id="events-v2"
    x-data="{
        search: '',
        filteredEvents: @js($events->items()),
        allEvents: @js($events->items()),
        category: 'all',
        init() {

            // Watch for category changes
            this.$watch('category', () => {
                this.filterEvents();
            });

        },
        filterEvents() {
            let filtered = [...this.allEvents];
            
            // Filter by category (if needed in future)
            if (this.category !== 'all') {
                // Add category filtering logic here
            }
            
            this.filteredEvents = filtered;
        },
        formatDate(dateString) {
            if (!dateString) return 'TBA';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        formatDateForThumbnail(dateString) {
            if (!dateString) return { month: 'TBA', day: '', year: '' };
            const date = new Date(dateString);
            return {
                month: date.toLocaleDateString('en-US', { month: 'short' }),
                day: date.getDate().toString(),
                year: date.getFullYear().toString()
            };
        }
    }"
    x-init="init()"
>
    <!-- Search input (connected to Alpine) -->
    <input
        type="text"
        x-model="search"
        x-ref="searchInput"
        placeholder="Search events..."
        class="hidden"
        wire:ignore
    >

    <div class="mb-6">
        <p class="text-xl font-bold text-gray-700 mb-1">
            @if($filter === 'upcoming')
                Upcoming Event This Week
            @else
                All Events
            @endif
        </p>
        <p class="text-xs text-gray-600">
            @if($filter === 'upcoming')
                Events happening within the next 7 days. Tap Join to register.
            @else
                Browse all upcoming events and tap Join to register.
            @endif
        </p>
    </div>

    <!-- Events List -->
    <div 
        class="space-y-4"
    >
        @forelse($events->items() as $index => $event)
            @livewire('member.eventsV2.event-card', ['event' => $event, 'index' => $index], key('event-card-' . $event['id']))
        @empty
            <!-- Empty State -->
            <div class="card card-bordered border-dashed bg-base-200/50 p-8 text-center">
                <div class="card-body">
                    <svg class="w-12 h-12 text-base-content/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-base-content/60 font-semibold">No events found</p>
                    <p class="text-xs text-base-content/50 mt-1">Try adjusting your search or filters</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</div>

