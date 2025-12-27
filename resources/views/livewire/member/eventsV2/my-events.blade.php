<div
    x-data="{ 
        isLoading: true,
        init() {
            // Start with opacity 0
            $el.style.opacity = '0';
            // Trigger fade-in after a short delay
            setTimeout(() => {
                this.isLoading = false;
                $el.style.transition = 'opacity 0.8s ease-out';
                $el.style.opacity = '1';
            }, 500);
        }
    }"
>
    <!-- Loading Animation -->
    <div 
        x-show="isLoading"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-base-200/10"
    >
        <div class="text-center">
            <span class="loading loading-bars loading-xl text-orange-500"></span>
            <p class="mt-4 text-gray-600 font-medium animate-pulse">Loading events...</p>
        </div>
    </div>

    <div x-show="!isLoading" x-cloak>
        <div class="mb-6">
            <p class="text-xl font-bold text-gray-700 mb-1">My Events</p>
            <p class="text-xs text-gray-600">Events you have registered for, marked as interested, added to favorites, or attended will appear here.</p>
        </div>

        @if (session()->has('message'))
            @php
                $type = session('message_type', 'success');
                $alertClass = $type === 'error' ? 'alert-error' : 'alert-success';
            @endphp
            <div 
                x-data="{ show: true }"
                x-cloak
                x-show="show"
                class="alert {{ $alertClass }} mb-4" 
                role="alert"
            >
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- Attended Events -->
        @if($attendedEvents->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Attended
                </h2>
                <div class="space-y-4">
                    @foreach($attendedEvents as $index => $event)
                        @livewire('member.eventsV2.event-card', ['event' => $event, 'index' => $index, 'isMyEvents' => true], key('attended-event-' . $event['id']))
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Joined Events -->
        @if($joinedEvents->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-orange-500 mb-4 flex items-center gap-2">
                    {{-- <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg> --}}
                    Joined Events
                </h2>
                <div class="space-y-4">
                    @php
                        $baseIndex = $attendedEvents->count();
                    @endphp
                    @foreach($joinedEvents as $index => $event)
                        @livewire('member.eventsV2.event-card', ['event' => $event, 'index' => $baseIndex + $index, 'isMyEvents' => true], key('joined-event-' . $event['id']))
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Interested Events -->
        @if($interestedEvents->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-orange-500 mb-4 flex items-center gap-2">
                    {{-- <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg> --}}
                    Interested
                </h2>
                <div class="space-y-4">
                    @php
                        $baseIndex = $attendedEvents->count() + $joinedEvents->count();
                    @endphp
                    @foreach($interestedEvents as $index => $event)
                        @livewire('member.eventsV2.event-card', ['event' => $event, 'index' => $baseIndex + $index, 'isMyEvents' => true], key('interested-event-' . $event['id']))
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Favorited Events -->
        @if($favoritedEvents->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-orange-500 mb-4 flex items-center gap-2">
                    {{-- <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg> --}}
                    Add to Favorites
                </h2>
                <div class="space-y-4">
                    @php
                        $baseIndex = $attendedEvents->count() + $joinedEvents->count() + $interestedEvents->count();
                    @endphp
                    @foreach($favoritedEvents as $index => $event)
                        @livewire('member.eventsV2.event-card', ['event' => $event, 'index' => $baseIndex + $index, 'isMyEvents' => true], key('favorited-event-' . $event['id']))
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Empty State -->
        @if($attendedEvents->isEmpty() && $joinedEvents->isEmpty() && $interestedEvents->isEmpty() && $favoritedEvents->isEmpty())
            <div class="card card-bordered border-dashed bg-base-200/50 p-8 text-center">
                <div class="card-body">
                    <svg class="w-12 h-12 text-base-content/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-base-content/60 font-semibold">No events found</p>
                    <p class="text-xs text-base-content/50 mt-1">You haven't registered for any events yet</p>
                </div>
            </div>
        @endif
    </div>
</div>

