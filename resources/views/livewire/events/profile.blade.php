<div>
    <x-slot name="header">
        <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ $event->title }} - Profile
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.events.index') }}" class="text-gray-600 hover:text-gray-900 md:mx-0 mx-4">
                ← Back to Events
            </a>
            
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6 md:mx-0 mx-4">
                <!-- Left Column - Event Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Thumbnail -->
                    @if($event->thumbnail_url)
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                        <img src="{{ $event->thumbnail_url }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                    </div>
                    @endif

                    <!-- Event Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Event Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Title</label>
                                <p class="text-gray-900">{{ $event->title }}</p>
                            </div>
                            
                            @if($event->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="text-gray-900">{{ $event->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Date & Time Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Date & Time</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Start Date & Time</label>
                                    <p class="text-gray-900">
                                        {{ $event->start_date->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">End Date & Time</label>
                                    <p class="text-gray-900">
                                        {{ $event->end_date->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            @if($event->venue)
                            <div class="flex items-start gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Venue</label>
                                    <p class="text-gray-900">{{ $event->venue }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Location Information</h3>
                        
                        @php
                            $location = $event->location;
                            $mapThumbnailUrl = null;
                            if ($location) {
                                $apiKey = config('services.google_maps.api_key');
                                if ($apiKey) {
                                    if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                                        $lat = $location->latitude;
                                        $lng = $location->longitude;
                                        $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=800x300&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                                    } elseif (!empty($location->address)) {
                                        $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                        $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=800x300&markers=color:red|{$address}&key={$apiKey}";
                                    }
                                }
                            }
                        @endphp

                        <div class="space-y-4">
                            @if($location && $location->address)
                            <div class="flex flex-col items-start gap-1">
                                <div class="flex items-center gap-1">
                                    <svg class="size-5" version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path fill="#394240" d="M32,0C18.745,0,8,10.745,8,24c0,5.678,2.502,10.671,5.271,15l17.097,24.156C30.743,63.686,31.352,64,32,64 s1.257-0.314,1.632-0.844L50.729,39C53.375,35.438,56,29.678,56,24C56,10.745,45.255,0,32,0z M48.087,39h-0.01L32,61L15.923,39 h-0.01C13.469,35.469,10,29.799,10,24c0-12.15,9.85-22,22-22s22,9.85,22,22C54,29.799,50.281,35.781,48.087,39z"></path> <path fill="#394240" d="M32,14c-5.523,0-10,4.478-10,10s4.477,10,10,10s10-4.478,10-10S37.523,14,32,14z M32,32 c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,32,32,32z"></path> <path fill="#394240" d="M32,10c-7.732,0-14,6.268-14,14s6.268,14,14,14s14-6.268,14-14S39.732,10,32,10z M32,36 c-6.627,0-12-5.373-12-12s5.373-12,12-12s12,5.373,12,12S38.627,36,32,36z"></path> </g> <g> <path fill="#F76D57" d="M32,12c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S38.627,12,32,12z M32,34 c-5.522,0-10-4.477-10-10s4.478-10,10-10s10,4.477,10,10S37.522,34,32,34z"></path> <path fill="#F76D57" d="M32,2c-12.15,0-22,9.85-22,22c0,5.799,3.469,11.469,5.913,15h0.01L32,61l16.077-22h0.01 C50.281,35.781,54,29.799,54,24C54,11.85,44.15,2,32,2z M32,38c-7.732,0-14-6.268-14-14s6.268-14,14-14s14,6.268,14,14 S39.732,38,32,38z"></path> </g> <path opacity="0.2" fill="#231F20" d="M32,12c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S38.627,12,32,12z M32,34 c-5.522,0-10-4.477-10-10s4.478-10,10-10s10,4.477,10,10S37.522,34,32,34z"></path> </g> </g>
                                    </svg>
                                    <p class="text-gray-900 font-bold text-lg">
                                        <a href="{{ route('admin.locations.profile', $location->location_code) }}" class="text-indigo-500 hover:text-indigo-600">
                                            {{ $location->name }}
                                        </a>
                                    </p>
                                </div>
                                <p class="text-gray-900 text-sm pl-6">
                                    {{ $location->address }}
                                    @if($location->city || $location->province || $location->postal_code)
                                        <br>
                                        {{ trim(implode(', ', array_filter([$location->city, $location->province, $location->postal_code]))) }}
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($mapThumbnailUrl)
                                <div class="pt-2">
                                    <img
                                        src="{{ $mapThumbnailUrl }}"
                                        alt="{{ $location?->name ?? 'Location' }} map"
                                        class="w-full h-56 object-cover rounded-lg border border-gray-200"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Member Registrants Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-semibold text-gray-800">Member Registrants</h3>
                            <span class="text-sm text-gray-500">
                                {{ $event->registrations->count() }} 
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                            </span>
                        </div>
                        
                        @if($event->registrations->count() > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($event->registrations as $registration)
                                    @php
                                        $statusClass = match($registration->status) {
                                            'attended' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'no_show' => 'bg-gray-100 text-gray-800',
                                            default => 'bg-blue-100 text-blue-800',
                                        };
                                    @endphp
                                    <div class="border-l-4 border-indigo-500 pl-3 py-2 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $registration->user->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Registered: {{ $registration->registered_at ? $registration->registered_at->format('M d, Y g:i A') : $registration->created_at->format('M d, Y g:i A') }}
                                                </p>
                                                @if($registration->attended_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    Attended: {{ $registration->attended_at->format('M d, Y g:i A') }}
                                                </p>
                                                @endif
                                            </div>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $registration->status)) }}
                                            </span>
                                        </div>
                                        @if($registration->notes)
                                        <p class="text-xs text-gray-600 mt-2 italic">
                                            {{ $registration->notes }}
                                        </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 flex items-center justify-center gap-2 py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <span>No registrations yet</span>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Registrations -->
                <div class="space-y-6">
                    @php
                        $eventQrImage = app(\App\Services\QrCodeService::class)->generateQrCodeImage($event->event_code, 260);
                    @endphp

                    <!-- Event QR Code Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <div class="flex flex-col items-center">
                            <img
                                id="event-qr-image"
                                src="{{ $eventQrImage }}"
                                alt="Event QR Code"
                                class="w-64 h-64 object-contain border border-gray-200 rounded-lg bg-white"
                            >
                        </div>
                        <div class="flex items-center justify-center mt-4">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick="shareEventQrCode()"
                                    class="hover:bg-gray-200 border border-gray-400 bg-transparent text-xs hover:-translate-y-0.5 duration-300 text-gray-600 font-semibold py-2 px-4 rounded-lg transition-all"
                                >
                                    Share QR Code
                                </button>
                                <a
                                    id="event-qr-download"
                                    href="{{ $eventQrImage }}"
                                    download="event-{{ $event->event_code }}.png"
                                    class="hover:bg-gray-200 border border-gray-400 bg-transparent text-xs hover:-translate-y-0.5 duration-300 text-gray-600 font-semibold py-2 px-4 rounded-lg transition-all"
                                >
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('admin.locations.events.edit', [$event->location->location_code, $event->id]) }}" class="flex items-center justify-center gap-2 w-full bg-indigo-500 hover:bg-indigo-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-white group-hover:stroke-blue-700">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                </svg>
                                Scan QR Code
                            </a>
                            <a href="{{ route('admin.locations.events.edit', [$event->location->location_code, $event->id]) }}" class="flex items-center justify-center gap-2 w-full bg-yellow-600 hover:bg-yellow-700 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                Edit Event
                            </a>
                            <a href="{{ route('admin.locations.profile', $event->location->location_code) }}" class="flex items-center justify-center gap-2 w-full bg-gray-600 hover:bg-gray-700 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                View Location
                            </a>
                            <a href="{{ route('admin.locations.events.index', $event->location->location_code) }}" class="flex items-center justify-center gap-2 w-full bg-orange-500 hover:bg-orange-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 9H21M7 3V5M17 3V5M6 12H8M11 12H13M16 12H18M6 15H8M11 15H13M16 15H18M6 18H8M11 18H13M16 18H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                                All Events
                            </a>
                        </div>
                    </div>

                    <script>
                        async function shareEventQrCode() {
                            const img = document.getElementById('event-qr-image');
                            const downloadLink = document.getElementById('event-qr-download');
                            if (!img || !downloadLink) return;

                            const dataUrl = img.src;
                            const filename = downloadLink.getAttribute('download') || 'event-qr.png';
                            const title = 'Event QR Code';
                            const text = 'Scan this QR code to get the event code.';

                            try {
                                const response = await fetch(dataUrl);
                                const blob = await response.blob();
                                const file = new File([blob], filename, { type: blob.type || 'image/png' });

                                if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                                    await navigator.share({ title, text, files: [file] });
                                } else {
                                    // Fallback: trigger download
                                    downloadLink.click();
                                }
                            } catch (e) {
                                // Fallback: trigger download
                                downloadLink.click();
                            }
                        }
                    </script>

                    <!-- Event Statistics Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Event Statistics</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Registrations</label>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $event->registrations->count() }}
                                    @if($event->max_participants)
                                        / {{ $event->max_participants }}
                                    @endif
                                </p>
                            </div>
                            @if($event->max_participants)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Available Spots</label>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ max(0, $event->max_participants - $event->registrations->count()) }}
                                </p>
                            </div>
                            @endif
                            <div>
                                <label class="text-sm font-medium text-gray-500">Created By</label>
                                <p class="text-gray-900">{{ $event->creator->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Created At</label>
                                <p class="text-gray-900">{{ $event->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                <p>
                                    @php
                                        $statusClass = match($event->status) {
                                            'published' => 'bg-green-100 text-green-800 border border-green-500',
                                            'cancelled' => 'bg-red-100 text-red-800 border border-red-500',
                                            'completed' => 'bg-gray-100 text-gray-800 border border-gray-500',
                                            default => 'bg-yellow-100 text-yellow-800 border border-yellow-500',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-md leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Event Code</label>
                                <p class="text-gray-900 font-mono">{{ $event->event_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
