<div
    x-data="{ 
        loaded: false,
        imageLoaded: false,
        favoritesLoading: false,
        interestedLoading: false,
        joinLoading: false,
        minLoadTime: 1000,
        loadingTimers: {},
        startLoading(type) {
            this[type + 'Loading'] = true;
            const startTime = Date.now();
            this.loadingTimers[type] = startTime;
        },
        stopLoading(type) {
            if (!this.loadingTimers[type]) return;
            const elapsed = Date.now() - this.loadingTimers[type];
            const remaining = this.minLoadTime - elapsed;
            if (remaining > 0) {
                setTimeout(() => {
                    this[type + 'Loading'] = false;
                    delete this.loadingTimers[type];
                }, remaining);
            } else {
                this[type + 'Loading'] = false;
                delete this.loadingTimers[type];
            }
        }
    }"
    @event-updated.window="
        if (favoritesLoading) stopLoading('favorites');
        if (interestedLoading) stopLoading('interested');
        if (joinLoading) stopLoading('join');
    "
    x-init="
        loaded = true;
        $watch('loaded', (value) => {
            if (value) {
                $el.style.opacity = '0';
                $el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    $el.style.transition = 'all 0.6s ease-out';
                    $el.style.opacity = '1';
                    $el.style.transform = 'translateY(0)';
                }, 10);
            }
        });
    "
    class="card card-bordered bg-base-100 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer"
    @click="window.location.href = '/member/event/{{ $event['event_code'] }}'"
>
    <div class="card-body flex-row gap-4 p-4">
        <!-- Event Image -->
        <div class="flex-shrink-0">
            <div class="w-20 h-20 rounded-lg bg-base-200 overflow-hidden relative">
                @if($event['thumbnail_url'] ?? null)
                    <img
                        src="{{ $event['thumbnail_url'] }}"
                        alt="{{ $event['title'] }}"
                        class="w-full h-full object-cover"
                        @load="imageLoaded = true"
                        x-show="imageLoaded"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                    >
                @else
                    @php
                        $dateInfo = $this->formatDateForThumbnail($event['start_date'] ?? null);
                    @endphp
                    <div class="w-full h-full flex flex-col items-center justify-center border-4 border-orange-300 bg-orange-50/50 rounded-lg p-2">
                        <div class="text-md font-semibold text-orange-400 uppercase">{{ $dateInfo['month'] }}</div>
                        <div class="text-3xl font-bold text-orange-300">{{ $dateInfo['day'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Event Details -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <!-- Event Title -->
                <a href="/member/event/{{ $event['event_code'] }}" class="text-gray-900 text-xl font-bold line-clamp-2 hover:text-orange-600 transition-colors flex-1">{{ $event['title'] }}</a>
            </div>
            {{-- add the description here with truncate and add ... at the end if the description is longer than 100 characters --}}
            <p class="text-gray-600 text-xs line-clamp-2 italic">{{ str()->limit($event['description'] ?? '', 100) }}</p>
            
            @if($isMyEvents)
                {{-- Show status badge only in my-events --}}
                @if(isset($event['status']))
                    @php
                        $statusConfig = [
                            'favorited' => ['label' => 'Favorited', 'class' => 'border border-orange-500 text-orange-500'],
                            'interested' => ['label' => 'Interested', 'class' => 'border border-orange-500 text-orange-500'],
                            'registered' => ['label' => 'Joined', 'class' => 'border border-orange-500 text-orange-500'],
                            'attended' => ['label' => 'Attended', 'class' => 'border border-orange-500 text-orange-500'],
                        ];
                        $status = $statusConfig[$event['status']] ?? null;
                    @endphp
                    @if($status)
                        <div class="mt-3">
                            <span class="badge {{ $status['class'] }} badge-sm whitespace-nowrap">{{ $status['label'] }}</span>
                        </div>
                    @endif
                @endif
            @else
                {{-- Show action buttons only in browse component --}}
                @php
                    $status = $event['registration_status'] ?? null;
                    $favoritesLabel = $status === 'favorited' ? 'Favorited' : 'Favorites';
                    $interestedLabel = $status === 'interested' ? 'Interested' : 'Interest';
                    $joinLabel = $status === 'registered' ? 'Joined' : 'Join';
                    
                    $favoritesClass = $status === 'favorited' 
                        ? 'text-orange-300 hover:text-orange-300' 
                        : 'text-orange-500 hover:text-orange-500';
                    $interestedClass = $status === 'interested' 
                        ? 'text-orange-300 hover:text-orange-300' 
                        : 'text-orange-500 hover:text-orange-500';
                    $joinClass = $status === 'registered' 
                        ? 'text-orange-300 hover:text-orange-300' 
                        : 'text-orange-500 hover:text-orange-500';
                @endphp
                <!-- Action Links -->
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    <button
                        type="button"
                        @click.stop="startLoading('favorites')"
                        wire:click="addToFavorites"
                        :disabled="favoritesLoading"
                        :class="favoritesLoading ? 'opacity-50 cursor-not-allowed' : ''"
                        class="{{ $favoritesClass }} rounded-full border border-orange-500 px-2 py-1 font-medium cursor-pointer transition-colors disabled:opacity-50 flex items-center gap-1 text-xs"
                    >
                        <svg 
                            x-show="!favoritesLoading"
                            x-cloak
                            class="w-4 h-4" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <svg 
                            x-show="favoritesLoading"
                            x-cloak
                            class="animate-spin w-4 h-4" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!favoritesLoading" x-cloak>{{ $favoritesLabel }}</span>
                        <span x-show="favoritesLoading" x-cloak>Adding...</span>
                    </button>
                    <button
                        type="button"
                        @click.stop="startLoading('interested')"
                        wire:click="markInterested"
                        :disabled="interestedLoading"
                        :class="interestedLoading ? 'opacity-50 cursor-not-allowed' : ''"
                        class="{{ $interestedClass }} rounded-full border border-orange-500 px-2 py-1 font-medium cursor-pointer transition-colors disabled:opacity-50 flex items-center gap-1 text-xs"
                    >
                        <svg 
                            x-show="!interestedLoading"
                            x-cloak
                            class="w-4 h-4" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <svg 
                            x-show="interestedLoading"
                            x-cloak
                            class="animate-spin w-4 h-4" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!interestedLoading" x-cloak>{{ $interestedLabel }}</span>
                        <span x-show="interestedLoading" x-cloak>Marking...</span>
                    </button>
                    <button
                        type="button"
                        @click.stop="startLoading('join')"
                        wire:click="join"
                        :disabled="joinLoading"
                        :class="joinLoading ? 'opacity-50 cursor-not-allowed' : ''"
                        class="{{ $joinClass }} flex items-center rounded-full border border-orange-500 px-2 py-1 font-medium cursor-pointer transition-colors disabled:opacity-50 text-xs"
                    >
                        <svg 
                            x-show="!joinLoading"
                            x-cloak
                            class="w-4 h-4" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <svg 
                            x-show="joinLoading"
                            x-cloak
                            class="animate-spin w-4 h-4" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!joinLoading" x-cloak>{{ $joinLabel }}</span>
                        <span x-show="joinLoading" x-cloak>Joining...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

