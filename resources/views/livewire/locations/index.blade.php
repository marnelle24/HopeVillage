<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Locations Management') }}
            </h2>
            <a href="{{ route('admin.locations.create') }}" class="md:block hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                Add New Location
            </a>
            <a href="{{ route('admin.locations.create') }}" class="md:hidden block bg-indigo-600 hover:bg-indigo-500 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </a>
        </div>
    </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session()->has('message'))
                    <div 
                        x-data="{ 
                            show: @entangle('showMessage').live,
                            timeoutId: null
                        }"
                        x-init="
                            $watch('show', value => {
                                if (value && !timeoutId) {
                                    timeoutId = setTimeout(() => {
                                        show = false;
                                        timeoutId = null;
                                    }, 3000);
                                } else if (!value && timeoutId) {
                                    clearTimeout(timeoutId);
                                    timeoutId = null;
                                }
                            });
                            if (show) {
                                timeoutId = setTimeout(() => {
                                    show = false;
                                    timeoutId = null;
                                }, 3000);
                            }
                        "
                        x-show="show"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-out duration-500"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" 
                        role="alert"
                    >
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Search and Filter -->
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search locations..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>
                        <div>
                            <select 
                                wire:model.live="statusFilter" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:px-0 px-4">
                    @forelse($locations as $location)
                        @php
                            $thumbnailHeight = 'h-[250px]';
                            $status = $location->is_active ? 'Active' : 'Inactive';
                            $statusClass = $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            
                            // Generate map thumbnail URL if no thumbnail but address exists
                            $mapThumbnailUrl = null;
                            if (!$location->thumbnail_url) {
                                $apiKey = config('services.google_maps.api_key');
                                if ($apiKey) {
                                    // Prefer coordinates if available, otherwise use address
                                    if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                                        $lat = $location->latitude;
                                        $lng = $location->longitude;
                                        $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=400x250&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                                    } elseif ($location->address) {
                                        $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                        $mapThumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=400x250&markers=color:red|{$address}&key={$apiKey}";
                                    }
                                }
                            }
                        @endphp
                        <div class="bg-white overflow-hidden shadow-md flex flex-col rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                            <div class="w-full {{ $thumbnailHeight }} border border-gray-300 rounded-t-lg">
                                @if($location->thumbnail_url)
                                        <img src="{{ $location->thumbnail_url }}" alt="{{ $location->name }}" class="w-full {{ $thumbnailHeight }} border-b border-gray-300 object-cover rounded-t-lg">
                                @elseif($mapThumbnailUrl)
                                    <img src="{{ $mapThumbnailUrl }}" alt="{{ $location->name }} - Map Location" class="w-full {{ $thumbnailHeight }} object-cover rounded-t-lg">
                                @else
                                    <span class="text-gray-400 text-[10px] h-[250px]">IMG</span>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col h-full group-hover:bg-orange-50 transition-all duration-300">
                                <div class="flex-1">
                                    <h3 class="md:text-lg text-xl font-bold text-gray-900 mb-2">{{ $location->name }}</h3>
                                    <table class="w-full">
                                        <tr>
                                            <td class="md:text-sm text-xs text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">{{ $location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code  }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">{{ $location->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm text-gray-600 flex items-center py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                                </svg>
                                            </td>
                                            <td class="md:text-sm text-md text-gray-500 pl-1">{{ $location->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="mt-2 flex justify-between items-baseline">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $status }}</span>

                                    <div class="flex items-center gap-2">
                                        <a 
                                            href="{{ route('admin.locations.edit', $location->id) }}" 
                                            title="Edit Location"
                                            class="bg-blue-700/60 hover:bg-sky-600 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                        <button 
                                            wire:click="delete({{ $location->id }})" 
                                            title="Delete Location"
                                            wire:confirm="Are you sure you want to delete this location?"
                                            class="bg-red-600/60 hover:bg-red-700 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                            No locations found.
                            <p class="mt-4">
                                <a href="{{ route('admin.locations.create') }}" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all duration-300 text-sm border border-gray-300 rounded-lg p-2">Add New Location</a>
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
</div>

