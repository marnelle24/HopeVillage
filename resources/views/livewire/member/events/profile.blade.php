<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->title }}
            </h2>

            <div>
                <span class="font-semibold text-gray-900 text-xs">My Points</span>
                <button type="button" title="My Points" class="flex items-center gap-1 text-xs text-gray-500 border border-gray-300 rounded-lg px-2 py-1">
                    <svg class="size-4" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css">  .st0{fill:#000000;}  </style> <g> <path class="st0" d="M336.563,225.156c67.797,0,122.938,55.156,122.938,122.938s-55.141,122.938-122.938,122.938 c-67.766,0-122.922-55.156-122.922-122.938S268.797,225.156,336.563,225.156 M336.563,184.188 c-90.5,0-163.891,73.375-163.891,163.906S246.063,512,336.563,512c90.516,0,163.906-73.375,163.906-163.906 S427.078,184.188,336.563,184.188z"></path> <path class="st0" d="M341.922,293.406h-34.891c-5.578,0-10.078,4.5-10.078,10.063v102.875c0,5.563,4.5,10.094,10.078,10.094h5.063 c5.563,0,10.078-4.531,10.078-10.094v-28.953h19.75c26.422,0,47.922-18.828,47.922-42 C389.844,312.25,368.344,293.406,341.922,293.406z M341.922,355.281h-19.75V315.5h19.75c12.516,0,22.688,8.938,22.688,19.891 S354.438,355.281,341.922,355.281z"></path> <path class="st0" d="M144.391,0C71,0,11.531,28.969,11.531,64.719v34.563C11.531,135.031,71,164,144.391,164 c73.375,0,132.859-28.969,132.859-64.719V64.719C277.25,28.969,217.766,0,144.391,0z M144.391,26.875 c64.703,0,105.969,24.844,105.969,37.844s-41.266,37.844-105.969,37.844S38.422,77.719,38.422,64.719S79.688,26.875,144.391,26.875 z"></path> <path class="st0" d="M144.375,216c24.578,0,47.594-3.281,67.359-8.938c-19.016,16.719-34.578,37.375-45.563,60.563 c-7.063,0.563-14.344,0.906-21.797,0.906c-73.359,0-132.844-29.016-132.844-64.75v-40.625c0-0.656,0.484-1.25,1.141-1.313 c0.328-0.094,0.656,0.063,0.906,0.406c-0.156,0,0,0.469,0.891,2.688C27.344,194.125,80.609,216,144.375,216z"></path> <path class="st0" d="M148.063,348.094c0,7.969,0.484,15.75,1.547,23.438c-1.719,0.094-3.438,0.094-5.234,0.094 c-73.359,0-132.844-28.938-132.844-64.656v-40.75c0-0.563,0.484-1.125,1.141-1.219c0.531-0.063,0.891,0.25,1.125,0.625 c-0.297-0.406-0.641-0.625,0.672,2.5c12.875,29.156,66.141,51.063,129.906,51.063c1.969,0,4.016,0,5.969-0.188 C148.797,328.516,148.063,338.188,148.063,348.094z"></path> <path class="st0" d="M193.203,470.281c-15.078,2.969-31.547,4.5-48.828,4.5c-73.359,0-132.844-28.906-132.844-64.719v-40.656 c0-0.656,0.484-1.156,1.141-1.219c0.5-0.094,0.984,0.156,1.141,0.656c-0.313-0.406-0.734-0.813,0.656,2.375 c12.875,29.25,66.141,51.125,129.906,51.125c6.313,0,12.609-0.25,18.672-0.656C170.594,439.469,180.844,455.875,193.203,470.281z"></path> </g> </g>
                    </svg>                
                    <span class="font-semibold text-gray-900 text-sm">{{ auth()->user()->total_points ? number_format(auth()->user()->total_points, 2) : '0 point' }}</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 md:px-0 px-4">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('member.events') }}" class="text-sm text-gray-600 hover:text-orange-500">
                ← Back to Events
            </a>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                @php
                    $type = session('message_type', 'success');
                    $classes = $type === 'error'
                        ? 'bg-red-100 border-red-400 text-red-700'
                        : 'bg-green-100 border-green-400 text-green-700';
                @endphp
                <div class="mb-4 border px-4 py-3 rounded {{ $classes }}" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            @php
                $location = $event->location;

                $heroImageUrl = $event->thumbnail_url ?? null;
                if (!$heroImageUrl) {
                    $apiKey = config('services.google_maps.api_key');
                    if ($apiKey && $location) {
                        if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                            $lat = $location->latitude;
                            $lng = $location->longitude;
                            $heroImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=1200x450&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                        } elseif (!empty($location->address)) {
                            $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                            $heroImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=1200x450&markers=color:red|{$address}&key={$apiKey}";
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

                $isFull = $event->max_participants && $event->max_participants > 0
                    ? ($event->registrations_count >= $event->max_participants)
                    : false;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    @if(!empty($heroImageUrl ?? null))
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <img
                                src="{{ $heroImageUrl ?? '' }}"
                                alt="{{ $event->title }}"
                                class="w-full h-72 object-cover"
                                loading="lazy"
                            >
                        </div>
                    @endif

                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Event Information</h3>
                        <p class="text-sm text-gray-600">{{ $schedule }}</p>

                        @if($event->description)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-1">Description</p>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                            </div>
                        @endif

                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            <div>
                                <span class="font-medium text-gray-600">Location:</span>
                                <span>{{ $location?->name ?? 'Unknown location' }}</span>
                            </div>
                            @if($event->venue)
                                <div>
                                    <span class="font-medium text-gray-600">Venue:</span>
                                    <span>{{ $event->venue }}</span>
                                </div>
                            @endif
                            @if($location?->address)
                                <div>
                                    <span class="font-medium text-gray-600">Address:</span>
                                    <span>
                                        {{ $location->address }}
                                        @if($location->city || $location->province || $location->postal_code)
                                            , {{ trim(implode(', ', array_filter([$location->city, $location->province, $location->postal_code]))) }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Registration</h3>

                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Participants</span>
                            <span class="font-semibold text-gray-800">
                                {{ $event->registrations_count }}
                                @if($event->max_participants && $event->max_participants > 0)
                                    / {{ $event->max_participants }}
                                @else
                                    / ∞
                                @endif
                            </span>
                        </div>

                        <div class="mt-4">
                            @if($event->is_registered)
                                <button type="button" disabled class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-500 border border-gray-200">
                                    Joined
                                </button>
                            @elseif($isFull)
                                <button type="button" disabled class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-500 border border-gray-200">
                                    Full
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="join"
                                    wire:loading.attr="disabled"
                                    class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="join">Join</span>
                                    <span wire:loading wire:target="join">Joining...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('scroll-to-top', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</div>


