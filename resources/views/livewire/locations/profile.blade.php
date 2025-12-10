<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.locations.index') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Locations
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $location->name }} - Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.locations.edit', $location->location_code) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Edit Location
                </a>
                <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Manage Events
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                            <div>
                                <label class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    Address
                                </label>
                                <p class="text-gray-900 mt-1">
                                    {{ $location->address }}
                                    @if($location->city || $location->province || $location->postal_code)
                                        <br>
                                        {{ trim(implode(', ', array_filter([$location->city, $location->province, $location->postal_code]))) }}
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($location->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 18.75h3" />
                                    </svg>
                                    Phone
                                </label>
                                <p class="text-gray-900 mt-1">{{ $location->phone }}</p>
                            </div>
                            @endif

                            @if($location->email)
                            <div>
                                <label class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                                    </svg>
                                    Email
                                </label>
                                <p class="text-gray-900 mt-1">{{ $location->email }}</p>
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
                            <a href="{{ route('admin.locations.events.create', $location->location_code) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-2 px-4 rounded-lg transition">
                                Create New Event
                            </a>
                            <a href="{{ route('admin.locations.events.index', $location->location_code) }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center font-semibold py-2 px-4 rounded-lg transition">
                                View All Events
                            </a>
                            <a href="{{ route('admin.locations.edit', $location->location_code) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-2 px-4 rounded-lg transition">
                                Edit Location
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
                                    <div class="border-l-4 border-indigo-500 pl-3 py-2">
                                        <a href="{{ route('admin.locations.events.edit', [$location->location_code, $event->id]) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                            {{ $event->title }}
                                        </a>
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
                                                'published' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-yellow-100 text-yellow-800',
                                            };
                                        @endphp
                                        <span class="inline-flex text-xs leading-5 font-semibold rounded-full mt-1 px-2 py-0.5 {{ $statusClass }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No events yet</p>
                            <a href="{{ route('admin.locations.events.create', $location->location_code) }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition mt-2">
                                Create First Event
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
