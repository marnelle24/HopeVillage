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
                <h2 class="text-sm font-semibold text-orange-500 mb-4 flex items-center gap-2">
                    <svg 
                        x-cloak
                        class="w-5 h-5 text-orange-600" 
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>

                    Registered Events
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
                <h2 class="text-md font-semibold text-orange-500 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                    </svg>
                    Liked Events
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
                    Favorited Events
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

