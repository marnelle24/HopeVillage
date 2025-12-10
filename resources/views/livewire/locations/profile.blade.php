<div>
    <x-slot name="header">
        <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ $location->name }} - Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.locations.edit', $location->location_code) }}" class="bg-slate-600 md:text-base text-xs hover:bg-slate-700 text-white font-normal py-2 px-4 rounded-lg">
                    Edit Location
                </a>
                <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="bg-indigo-600 md:text-base text-xs hover:bg-indigo-700 text-white font-normal py-2 px-4 rounded-lg">
                    Manage Events
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.locations.index') }}" class="text-gray-600 hover:text-gray-900 md:mx-0 mx-4">
                ← Back to Locations
            </a>
            
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6 md:mx-0 mx-4">
                <!-- Left Column - Location Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Location Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Location Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Name</label>
                                <p class="text-gray-900">{{ $location->name }}</p>
                            </div>
                            
                            @if($location->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="text-gray-900">{{ $location->description }}</p>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p>
                                        <span class="px-3 py-1 inline-flex text-md leading-5 font-semibold rounded-full {{ $location->is_active ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-red-100 text-red-800 border border-red-500' }}">
                                            {{ $location->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Contact Information</h3>
                        
                        <div class="space-y-4">
                            @if($location->address)
                            <div class="flex items-start gap-1">
                                <svg class="size-5" version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#000000">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path fill="#394240" d="M32,0C18.745,0,8,10.745,8,24c0,5.678,2.502,10.671,5.271,15l17.097,24.156C30.743,63.686,31.352,64,32,64 s1.257-0.314,1.632-0.844L50.729,39C53.375,35.438,56,29.678,56,24C56,10.745,45.255,0,32,0z M48.087,39h-0.01L32,61L15.923,39 h-0.01C13.469,35.469,10,29.799,10,24c0-12.15,9.85-22,22-22s22,9.85,22,22C54,29.799,50.281,35.781,48.087,39z"></path> <path fill="#394240" d="M32,14c-5.523,0-10,4.478-10,10s4.477,10,10,10s10-4.478,10-10S37.523,14,32,14z M32,32 c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,32,32,32z"></path> <path fill="#394240" d="M32,10c-7.732,0-14,6.268-14,14s6.268,14,14,14s14-6.268,14-14S39.732,10,32,10z M32,36 c-6.627,0-12-5.373-12-12s5.373-12,12-12s12,5.373,12,12S38.627,36,32,36z"></path> </g> <g> <path fill="#F76D57" d="M32,12c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S38.627,12,32,12z M32,34 c-5.522,0-10-4.477-10-10s4.478-10,10-10s10,4.477,10,10S37.522,34,32,34z"></path> <path fill="#F76D57" d="M32,2c-12.15,0-22,9.85-22,22c0,5.799,3.469,11.469,5.913,15h0.01L32,61l16.077-22h0.01 C50.281,35.781,54,29.799,54,24C54,11.85,44.15,2,32,2z M32,38c-7.732,0-14-6.268-14-14s6.268-14,14-14s14,6.268,14,14 S39.732,38,32,38z"></path> </g> <path opacity="0.2" fill="#231F20" d="M32,12c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S38.627,12,32,12z M32,34 c-5.522,0-10-4.477-10-10s4.478-10,10-10s10,4.477,10,10S37.522,34,32,34z"></path> </g> </g>
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">
                                        Address
                                    </label>
                                    <p class="text-gray-900">
                                        {{ $location->address }}
                                        @if($location->city || $location->province || $location->postal_code)
                                            <br>
                                            {{ trim(implode(', ', array_filter([$location->city, $location->province, $location->postal_code]))) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($location->phone)
                            <div class="flex items-start gap-1">
                                <svg class="size-5" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 500 500" xml:space="preserve" fill="#000000">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#FFFFFF;stroke:#FFFFFF;stroke-width:10;stroke-miterlimit:10;} .st1{fill:#343434;} .st2{fill:#8ECDFC;} .st3{fill:#E9E9E9;} .st4{fill:#A8F1E5;} </style> <g id="border"> <path class="st0" d="M352.268,41.873H147.661c-15.877,0-28.665,12.788-28.665,28.665v358.924c0,15.877,12.788,28.665,28.665,28.665 h204.607c15.877,0,28.737-12.788,28.737-28.665V70.538C381.005,54.66,368.145,41.873,352.268,41.873z"></path> </g> <g id="object" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg"> <g> <path class="st1" d="M147.66,41.873h204.607c15.877,0,28.737,12.788,28.737,28.665v358.924c0,15.877-12.86,28.665-28.737,28.665 H147.66c-15.877,0-28.665-12.788-28.665-28.665V70.538C118.995,54.66,131.783,41.873,147.66,41.873L147.66,41.873z"></path> <path class="st2" d="M154.629,79.23h190.67c12.572,0,22.702,10.13,22.702,22.702v284.568c0,12.644-10.13,22.774-22.702,22.774 h-190.67c-12.572,0-22.702-10.13-22.702-22.774V101.933C131.927,89.36,142.057,79.23,154.629,79.23L154.629,79.23z"></path> <path class="st3" d="M342.569,64.719c0,3.52-2.946,6.322-6.466,6.322c-3.592,0-6.538-2.802-6.538-6.322 c0-3.449,2.946-6.251,6.538-6.251C339.623,58.468,342.569,61.27,342.569,64.719L342.569,64.719z"></path> <path class="st3" d="M234.877,56.528h26.294c3.305,0,5.963,2.658,5.963,5.963c0,3.305-2.658,5.963-5.963,5.963h-26.294 c-3.305,0-5.963-2.658-5.963-5.963C228.914,59.187,231.572,56.528,234.877,56.528L234.877,56.528z"></path> <path class="st3" d="M181.067,59.833c0,2.011-1.509,3.735-3.448,3.735c-1.868,0-3.448-1.724-3.448-3.735 c0-2.084,1.581-3.736,3.448-3.736C179.559,56.097,181.067,57.75,181.067,59.833L181.067,59.833z"></path> <path class="st4" d="M156.282,79.23c-12.788,0-23.205,9.052-24.283,20.763l236.002,272.714V101.933 c0-12.572-10.848-22.702-24.355-22.702H156.282z"></path> <path class="st3" d="M262.249,430.755c0,6.466-5.388,11.639-12.07,11.639c-6.609,0-11.998-5.173-11.998-11.639 c0-6.394,5.388-11.638,11.998-11.638C256.861,419.117,262.249,424.362,262.249,430.755L262.249,430.755z"></path> </g> </g> </g>
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">
                                        Phone
                                    </label>
                                    <p class="text-gray-900">{{ $location->phone }}</p>
                                </div>
                            </div>
                            @endif

                            @if($location->email)
                            <div class="flex items-start gap-1">
                                <svg class="size-5" fill="#000000" viewBox="0 0 24 24" id="email" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path id="secondary" d="M20.61,5.23l-8,6.28a1,1,0,0,1-1.24,0l-8-6.28A1,1,0,0,0,3,6V18a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V6A1,1,0,0,0,20.61,5.23Z" style="fill: #2ca9bc; stroke-width: 2;"></path><path id="primary" d="M20,19H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H20a1,1,0,0,1,1,1V18A1,1,0,0,1,20,19ZM20,5H4a1,1,0,0,0-.62.22l8,6.29a1,1,0,0,0,1.24,0l8-6.29A1,1,0,0,0,20,5Z" style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path></g>
                                </svg>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">
                                        Email
                                    </label>
                                    <p class="text-gray-900">{{ $location->email }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Recent Events -->
                <div class="space-y-6">
                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('admin.locations.events.create', $location->location_code) }}" class="flex items-center justify-center gap-2 w-full bg-indigo-500 hover:bg-indigo-600 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 8V16M16 12H8M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                Create New Event
                            </a>
                            <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="flex items-center justify-center gap-2 w-full bg-gray-600 hover:bg-gray-700 hover:-translate-y-0.5 text-white text-center font-semibold py-4 px-4 rounded-lg transition-all duration-200">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 9H21M7 3V5M17 3V5M6 12H8M11 12H13M16 12H18M6 15H8M11 15H13M16 15H18M6 18H8M11 18H13M16 18H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                                View All Events
                            </a>
                        </div>
                    </div>

                    <!-- Recent Events Card -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Events</h3>
                            <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                View All
                            </a>
                        </div>
                        
                        @if($location->events->count() > 0)
                            <div class="space-y-3">
                                @foreach($location->events as $event)
                                    <div class="border-l-4 border-indigo-500 pl-3 py-2 group hover:bg-gray-100 hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                                        <a href="{{ route('admin.locations.events.edit', [$location->location_code, $event->id]) }}" class="group-hover:text-indigo-600 text-md font-semibold text-indigo-400 hover:text-indigo-600 transition-colors duration-300">
                                            {{ $event->title }}
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $event->start_date->format('M d, Y') }}
                                                @if($event->start_date->format('Y-m-d') === $event->end_date->format('Y-m-d'))
                                                    {{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                                @else
                                                    - {{ $event->end_date->format('M d, Y g:i A') }}
                                                @endif
                                            </p>
                                            @php
                                                $statusClass = match($event->status) {
                                                    'published' => 'bg-green-100 text-green-800 group-hover:bg-green-200 transition-colors duration-300',
                                                    'cancelled' => 'bg-red-100 text-red-800 group-hover:bg-red-200 trasition-colors duration-300',
                                                    'completed' => 'bg-gray-100 text-gray-800 group-hover:bg-gray-200 trasition-colors duration-300',
                                                    default => 'bg-yellow-100 text-yellow-800 group-hover:bg-yellow-200 trasition-colors duration-300',
                                                };
                                            @endphp
                                            
                                            <div class="mt-2">
                                                <span class="inline-flex text-xs leading-5 font-semibold rounded-full mt-1 px-2.5 py-1 {{ $statusClass }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                                
                                                @if($event->end_date > now())
                                                <span class="inline-flex text-xs leading-5 font-semibold rounded-full mt-1 px-2.5 py-1 bg-yellow-100 text-yellow-800">
                                                    Still Accepting Registrations
                                                </span>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 flex items-center justify-center gap-2 py-4">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 9H21M7 3V5M17 3V5M6 12H8M11 12H13M16 12H18M6 15H8M11 15H13M16 15H18M6 18H8M11 18H13M16 18H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#cfcfcf" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                                <span class="text-gray-500 text-sm">No events yet</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
