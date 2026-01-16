<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
                <div class="flex items-center gap-4">
                    <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                        {{ $event->title }} - Profile
                    </h2>
                </div>
                <a href="{{ route('admin.events.index') }}" class="text-orange-600 md:text-base text-md hover:text-orange-700 font-normal py-2 px-4 rounded-full">
                    ← Back to Events
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">            
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
                                        {{ $event->start_date->format('d M Y g:i A') }}
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
                                        {{ $event->end_date->format('d M Y g:i A') }}
                                    </p>
                                </div>
                            </div>


                            {{-- @if($event->venue)
                            <div class="flex items-start gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Venue</label>
                                    <p class="text-gray-900">{{ $event->venue }}</p>
                                </div>
                            </div>
                            @endif --}}
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

                </div>

                <!-- Right Column - Quick Actions & Registrations -->
                <div class="space-y-6">
                    <!-- Event QR Code Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Event QR Code</h3>
                        <div 
                            x-data="{
                                qrCodeImage: '{{ $qrCodeImage }}',
                                eventCode: '{{ $event->event_code }}',
                                async downloadQR() {
                                    try {
                                        const response = await fetch(this.qrCodeImage);
                                        const blob = await response.blob();
                                        const url = window.URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = `event-qr-${this.eventCode}.png`;
                                        document.body.appendChild(a);
                                        a.click();
                                        window.URL.revokeObjectURL(url);
                                        document.body.removeChild(a);
                                    } catch (error) {
                                        console.error('Download failed:', error);
                                        alert('Failed to download QR code. Please try again.');
                                    }
                                },
                                async shareQR() {
                                    try {
                                        if (navigator.share) {
                                            const response = await fetch(this.qrCodeImage);
                                            const blob = await response.blob();
                                            const file = new File([blob], `event-qr-${this.eventCode}.png`, { type: 'image/png' });
                                            await navigator.share({
                                                title: 'Event QR Code: {{ $event->title }}',
                                                text: `Event Code: ${this.eventCode}`,
                                                files: [file]
                                            });
                                        } else if (navigator.clipboard) {
                                            await navigator.clipboard.writeText(this.eventCode);
                                            alert('Event code copied to clipboard!');
                                        } else {
                                            // Fallback: copy event code to clipboard manually
                                            const textArea = document.createElement('textarea');
                                            textArea.value = this.eventCode;
                                            document.body.appendChild(textArea);
                                            textArea.select();
                                            document.execCommand('copy');
                                            document.body.removeChild(textArea);
                                            alert('Event code copied to clipboard!');
                                        }
                                    } catch (error) {
                                        console.error('Share failed:', error);
                                        // Fallback to copy event code
                                        try {
                                            await navigator.clipboard.writeText(this.eventCode);
                                            alert('Event code copied to clipboard!');
                                        } catch (e) {
                                            alert('Sharing not available. Event Code: ' + this.eventCode);
                                        }
                                    }
                                }
                            }"
                        >
                            <div class="flex items-center justify-center mb-4">
                                <img :src="qrCodeImage" alt="Event QR Code" class="w-full max-w-md h-64 object-contain rounded-lg border border-gray-300" id="event-qr-image">
                            </div>
                            <div class="flex gap-3 justify-center">
                                <button 
                                    @click="downloadQR()"
                                    class="flex items-center text-xs gap-1 px-3 py-1 bg-transparent hover:bg-gray-200 cursor-pointer text-gray-500 border border-gray-500 font-medium rounded-lg transition-colors duration-200"
                                >
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Download
                                </button>
                                <button 
                                    @click="shareQR()"
                                    class="flex items-center text-xs gap-1 px-3 py-1 bg-green-600 hover:bg-green-700 cursor-pointer text-white border border-green-600 font-medium rounded-lg transition-colors duration-200"
                                >
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                    </svg>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            {{-- <a href="{{ route('admin.locations.events.edit', [$event->location->location_code, $event->id]) }}" class="flex items-center justify-center gap-2 w-full bg-indigo-500 hover:bg-indigo-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-white group-hover:stroke-blue-700">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                </svg>
                                Scan QR Code
                            </a> --}}
                            <a href="{{ route('admin.locations.events.edit', [$event->location->location_code, $event->id]) }}" class="flex items-center justify-center gap-2 w-full bg-yellow-600 hover:bg-yellow-700 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                Update Event
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
                                        $isEventFinished = $event->end_date->isPast();
                                        $displayStatus = $isEventFinished ? 'Finished' : ($event->status ?: 'Finished');
                                        $statusClass = $isEventFinished 
                                            ? 'bg-gray-100 text-gray-800 border border-gray-500'
                                            : match($event->status) {
                                                'published' => 'bg-green-100 text-green-800 border border-green-500',
                                                'cancelled' => 'bg-red-100 text-red-800 border border-red-500',
                                                'completed' => 'bg-gray-100 text-gray-800 border border-gray-500',
                                                default => 'bg-yellow-100 text-yellow-800 border border-yellow-500',
                                            };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-md leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($displayStatus) }}
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
            
            <!-- Member Registrants Card -->
            @livewire('events.participants', ['event_code' => $event->event_code])
        </div>
    </div>
</div>
