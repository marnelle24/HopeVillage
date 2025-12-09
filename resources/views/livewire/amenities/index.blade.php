<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Amenities Management') }}
            </h2>
            <a href="{{ route('admin.amenities.create') }}" class="md:block hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                Add New Amenity
            </a>
            <a href="{{ route('admin.amenities.create') }}" class="md:hidden block bg-indigo-600 hover:bg-indigo-500 hover:scale-105 text-white p-2 rounded-full transition-all duration-200">
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
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search amenities..." 
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
                    <div>
                        <select 
                            wire:model.live="locationFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select 
                            wire:model.live="typeFilter" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Types</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Pickleball">Pickleball</option>
                            <option value="Badminton">Badminton</option>
                            <option value="Function Hall">Function Hall</option>
                            <option value="Swimming Pool">Swimming Pool</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:px-0 px-4">
                @forelse($amenities as $amenity)

                    @switch($amenity->type)
                        @case('Basketball')
                            @php
                                $amenityTypeClass = 'bg-green-50 text-green-800';
                                $imgBgColor = 'bg-green-200';
                                $imgTextColor = 'text-green-800';
                            @endphp
                            @break
                        @case('Pickleball')
                            @php
                                $amenityTypeClass = 'bg-yellow-50 text-yellow-800';
                                $imgBgColor = 'bg-yellow-200';
                                $imgTextColor = 'text-yellow-800';
                            @endphp
                            @break
                        @case('Badminton')
                            @php
                                $amenityTypeClass = 'bg-blue-50 text-blue-800';
                                $imgBgColor = 'bg-blue-200';
                                $imgTextColor = 'text-blue-800';
                            @endphp
                            @break
                        @case('Function Hall')
                            @php
                                $amenityTypeClass = 'bg-red-50 text-red-800';
                                $imgBgColor = 'bg-red-200';
                                $imgTextColor = 'text-red-800';
                            @endphp
                            @break
                        @case('Swimming Pool')
                            @php
                                $amenityTypeClass = 'bg-purple-50 text-purple-800';
                                $imgBgColor = 'bg-purple-200';
                                $imgTextColor = 'text-purple-800';
                            @endphp
                            @break
                        @default
                            @php
                                $amenityTypeClass = 'bg-slate-50 text-slate-800';
                                $imgBgColor = 'bg-slate-200';
                                $imgTextColor = 'text-slate-800';
                            @endphp
                            @break
                    @endswitch

                    <div class="w-full {{ $amenityTypeClass }} overflow-hidden border-2 border-gray-300 flex md:flex-row flex-col md:justify-between justify-start items-center rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="flex-1 flex items-start h-full">
                            @if($amenity->thumbnail_url)
                                <img src="{{ $amenity->thumbnail_url }}" alt="{{ $amenity->name }}" class="w-24 h-full object-cover rounded-lg">
                            @else
                                @php
                                    $typeLabel = $amenity->type;
                                    if (str_starts_with($typeLabel, 'others-')) {
                                        $typeLabel = substr($typeLabel, 7); // Remove "others-" prefix
                                    }
                                    $typeLabel = strtoupper(substr($typeLabel, 0, 2));
                                @endphp
                                <span class="{{ $imgTextColor }} opacity-50 drop-shadow-lg text-3xl font-bold w-32 h-full {{ $imgBgColor }} rounded-l-lg flex items-center justify-center">
                                    {{ $typeLabel }}
                                </span>
                            @endif
                            <div class="flex-1 flex md:flex-row flex-col justify-between">
                                <div class="px-4 py-4">
                                    <div class="flex md:flex-row flex-col md:items-center items-start gap-2">
                                        <h3 class="md:text-lg text-xl font-bold text-gray-500">{{ $amenity->name }}</h3>
                                        <span class="flex items-center gap-1 text-gray-600 bg-transparent border border-gray-500 rounded-lg py-1 px-2 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.469.469 1.229.469 1.698 0l4.318-4.318c.469-.469.469-1.229 0-1.698l-9.581-9.581A2.25 2.25 0 0 0 9.568 3Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                            </svg>
                                            <span class="text-xs capitalize">{{ $amenity->type }}</span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">{{ $amenity->description }}</p>
                                    <div class="flex items-center gap-1 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $amenity->location->name }}</span>
                                    </div>
                                    <div class="flex md:flex-row flex-col md:items-center items-start gap-1 mt-2">
                                        <div class="flex">
                                            <span class="flex items-center gap-1 text-gray-600 mt-2 bg-gray-200 rounded-lg py-1 px-3 text-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                </svg>
                                                <span class="text-sm">{{ $amenity->capacity }}</span>
                                            </span>
                                        </div>
                                        <div class="flex">
                                            @php
                                                $bookable = $amenity->is_bookable ? 'Allow Booking' : 'Not Allow Booking';
                                                $bookableClass = $amenity->is_bookable ? 'bg-yellow-200 text-yellow-800 border border-yellow-300' : 'bg-yellow-50 text-yellow-600 border border-yellow-300';
                                            @endphp
                                            <span class="flex items-center gap-1 {{ $bookableClass }} mt-2 rounded-lg py-1 px-2.5 text-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                                </svg>
                                                <span class="text-sm">{{ $bookable }}</span>
                                            </span>
                                        </div>
                                        <div class="flex">
                                            @php
                                                $status = $amenity->is_active ? 'Active' : 'Inactive';
                                                $statusClass = $amenity->is_active ? 'bg-green-100 text-green-800 border border-green-300/50' : 'bg-red-100 text-red-800 border border-red-300';
                                            @endphp
                                            <span class="flex items-center gap-1 {{ $statusClass }} mt-2 rounded-lg px-2.5 py-1 text-xs">
                                                <span class="text-sm">{{ $status }}</span>
                                            </span>
                                        </div>
                                        @if($amenity->hourly_rate)
                                            <div class="flex">
                                                <span class="flex items-center bg-zinc-200 text-zinc-800 gap-1 mt-2 rounded-lg py-1 px-3 text-xs">
                                                    <span class="text-sm">${{ number_format($amenity->hourly_rate, 2) . '/hr' }}</span>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
    
                                </div>
                                <div class="p-4 flex items-start justify-end gap-2">
                                    <button 
                                        title="Edit Amenity"
                                        wire:click="edit({{ $amenity->id }})"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-blue-500 hover:bg-sky-600 text-white transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button 
                                        wire:confirm="Are you sure you want to delete this amenity?" 
                                        title="Delete Amenity"
                                        wire:click="delete({{ $amenity->id }})"
                                        class="rounded-full p-3 flex items-center hover:scale-110 justify-center bg-red-400 hover:bg-red-500 text-white transition-all duration-300">
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
                        No amenities found.
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="mt-6 md:px-0 px-4">
                {{ $amenities->links() }}
            </div>
        </div>
    </div>
</div>
