<div>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Point System Management') }}
            </h2>
            <div class="flex items-center gap-4">
                <!-- Point System Toggle Component -->
                <livewire:point-system.toggle />
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 @if(!$pointSystemEnabled) opacity-90 pointer-events-none relative @endif" wire:key="point-system-content-{{ $pointSystemEnabled }}">
            @if(!$pointSystemEnabled)
                <div class="absolute inset-0 z-50 flex items-center justify-center bg-gray-100/70 rounded-lg">
                    <div class="bg-white border-2 border-gray-300 rounded-lg px-6 py-4 shadow-lg">
                        <p class="text-lg font-semibold text-gray-700 text-center">
                            Point System is Disabled
                        </p>
                        <p class="text-sm text-gray-500 text-center mt-2">
                            Enable the point system to manage configurations
                        </p>
                    </div>
                </div>
            @endif
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search configurations..." 
                            @if(!$pointSystemEnabled) disabled @endif
                            class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @if(!$pointSystemEnabled) cursor-not-allowed bg-gray-100 @endif"
                        >
                    </div>
                    <div>
                        <select 
                            wire:model.live="statusFilter" 
                            @if(!$pointSystemEnabled) disabled @endif
                            class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @if(!$pointSystemEnabled) cursor-not-allowed bg-gray-100 @endif"
                        >
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <select 
                            wire:model.live="activityTypeFilter" 
                            @if(!$pointSystemEnabled) disabled @endif
                            class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @if(!$pointSystemEnabled) cursor-not-allowed bg-gray-100 @endif"
                        >
                            <option value="">All Activity Types</option>
                            @foreach($activityTypes as $activityType)
                                <option value="{{ $activityType->id }}">{{ $activityType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <a href="{{ route('admin.point-system.create') }}" class="flex items-center justify-center gap-1 bg-orange-400 hover:bg-orange-500 text-white py-3 px-4 rounded-full text-center text-sm transition-colors">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </span>
                            New Config
                        </a>
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:px-0 px-4">
                @forelse($configs as $config)
                    <div class="w-full bg-white hover:bg-gray-50 overflow-hidden border-2 border-gray-300 flex md:flex-row flex-col md:justify-between justify-start items-center rounded-lg group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="flex-1 flex items-start h-full">
                            <div class="w-28 rounded-l-lg bg-orange-300/70 flex flex-col items-center justify-center mr-4 text-2xl font-bold h-full">
                                <span class="text-2xl font-bold text-white drop-shadow">
                                    {{ $config->points }}
                                </span>
                                <p class="text-xs text-white">points</p>
                            </div>
                            <div class="flex-1 p-4">
                                <div class="flex md:flex-row flex-col md:items-center items-start gap-2">
                                    @php 
                                        $activityTypeName = explode('_', $config->activityType->name);
                                    @endphp
                                    <h3 class="md:text-lg text-xl font-bold text-gray-500">
                                        @foreach($activityTypeName as $name)
                                            {{ ucfirst($name) . '' }}
                                        @endforeach
                                    </h3>
                                    <div class="flex">
                                        @php
                                            $status = $config->is_active ? 'Active' : 'Inactive';
                                            $statusClass = $config->is_active ? 'bg-green-100 text-green-800 border border-green-400' : 'bg-red-100 text-red-800 border border-red-400';
                                        @endphp
                                        <span class="flex items-center gap-1 {{ $statusClass }} rounded-lg px-2 py-0.5 text-xs">
                                            <span class="text-xs">{{ $status }}</span>
                                        </span>
                                    </div>
                                </div>
                                @if($config->description)
                                    <p class="text-sm text-gray-400">{{ $config->description }}</p>
                                @endif
                                <div class="flex md:flex-row flex-col md:items-center items-start gap-2 mt-2">
                                    @if($config->location)
                                        <div class="flex flex-col items-start justify-start mt-2">
                                            <p class="text-xs text-gray-400">Only applicable to:</p>
                                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                </svg>
                                                {{ $config->location->name }}
                                            </p>
                                        </div>
                                    @endif
                                    @if($config->amenity)
                                        <div class="flex items-center gap-1 mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 stroke-gray-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ $config->amenity->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 flex items-start justify-end gap-2">
                                <button 
                                    wire:click="edit({{ $config->id }})"
                                    @if(!$pointSystemEnabled) disabled @endif
                                    title="Edit Configuration"
                                    class="rounded-full p-3 flex items-center justify-center bg-blue-500 text-white transition-all duration-300 @if($pointSystemEnabled) hover:scale-110 hover:bg-sky-600 @else cursor-not-allowed opacity-50 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button 
                                    wire:confirm="Are you sure you want to delete this configuration?" 
                                    title="Delete Configuration"
                                    wire:click="delete({{ $config->id }})"
                                    @if(!$pointSystemEnabled) disabled @endif
                                    class="rounded-full p-3 flex items-center justify-center bg-red-400 text-white transition-all duration-300 @if($pointSystemEnabled) hover:scale-110 hover:bg-red-500 @else cursor-not-allowed opacity-50 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-300 text-lg py-12 border-dashed border-2 border-gray-200 rounded-lg p-4 bg-white">
                        No point system configurations found.
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="mt-6 md:px-0 px-4">
                {{ $configs->links() }}
            </div>
        </div>
    </div>
</div>

