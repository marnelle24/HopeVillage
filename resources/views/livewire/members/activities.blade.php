<div>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Member Activities') }}
                </h2>
                <a href="{{ route('admin.members.index') }}" class="cursor-pointer bg-orange-500 hover:bg-orange-600 text-white font-normal py-2 px-4 rounded-lg text-sm">
                    <span class="flex items-center gap-1">
                        Back to Members
                    </span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- add the activity chart component --}}
            <div class="mb-12">
                <livewire:admin.entry-scans-per-location />
            </div>

            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-4 md:mx-0 mx-4 mb-6">
                <div class="grid lg:grid-cols-7 grid-cols-1 gap-2">
                    <!-- Search -->
                    <div class="col-span-1 lg:col-span-3">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by member name, email, FIN, or description..."
                            class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                    </div>
                    <div class="relative cols-span-1 lg:col-span-4 grid lg:grid-cols-3 grid-cols-1 gap-4">
                        <!-- Activity Type Filter -->
                        <div class="lg:col-span-1">
                            <select
                                wire:model.live="activityTypeFilter"
                                class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                                <option value="">All Activity Types</option>
                                @foreach($activityTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Date Filter -->
                        <div class="col-span-1 flex flex-col gap-1">
                            <select
                                wire:model.live="dateFilter"
                                class="w-full px-4 py-2 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                                <option value="all">All</option>
                                <option value="today">Today</option>
                                <option value="week">Last 7 Days</option>
                                <option value="month">Last 30 Days</option>
                                <option value="custom">Custom Date</option>
                            </select>
                            @if($dateFilter === 'custom')
                                <div class="lg:absolute lg:right-0 lg:top-12 flex lg:flex-row flex-col gap-2 lg:space-y-0 space-y-2 lg:mt-0 mt-4 items-center lg:px-4 px-0">
                                    <div class="relative w-full flex items-center gap-2">
                                        <span class="text-gray-400 text-sm shrink-0">from:</span>
                                        <input
                                            id="customDateStart"
                                            x-ref="dateFromInput"
                                            type="date"
                                            wire:model.live="customStartDate"
                                            class="w-full px-2 py-1 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        >
                                        <label
                                            for="customDateStart"
                                            class="absolute inset-y-0 right-0 flex w-12 cursor-pointer items-center justify-center text-gray-400 hover:text-gray-600"
                                            @click.prevent="if ($refs.dateFromInput?.showPicker) $refs.dateFromInput.showPicker()"
                                        >
                                        </label>
                                    </div>
                                    <div class="relative w-full flex items-center gap-2">
                                        <span class="text-gray-400 text-sm shrink-0">to:</span>
                                        <input
                                            id="customDateEnd"
                                            x-ref="dateToInput"
                                            type="date"
                                            wire:model.live="customEndDate"
                                            class="w-full px-2 py-1 text-gray-800 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        >
                                        <label
                                            for="customDateEnd"
                                            @click.prevent="if ($refs.dateToInput?.showPicker) $refs.dateToInput.showPicker()"
                                            class="absolute inset-y-0 right-0 flex w-12 cursor-pointer items-center justify-center text-gray-400 hover:text-gray-600"
                                        >
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-1">
                            <button
                                type="button"
                                wire:click="exportCsv"
                                class="w-full flex gap-2 items-center justify-center py-3 text-xs font-medium text-white bg-orange-500 rounded-full hover:bg-orange-600 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export CSV
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="flex flex-wrap items-center justify-between gap-4 mt-4">
                    <p class="text-sm text-gray-500">
                        Result: {{ $activities->total() }} {{ $activities->total() <= 1 ? 'activity found' : 'activities found' }}
                    </p>
                </div>
            </div>

            @php
                $allowToChangeUserType = [
                    'HJ82CQCH6', // karl
                    'TN5TAY6IL', // marnelle
                    'BTSKKURCJ', // Jaslyn
                ];
            @endphp
            <!-- Activities Table -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg md:mx-0 mx-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">&nbsp;</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity Type</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th> --}}
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($activity->user)
                                            <div class="font-semibold text-gray-900">{{ $activity->user->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ $activity->user->email ?? '-' }}</div>
                                            @if($activity->user->qr_code)
                                                <div class="text-xs text-gray-500 font-mono">{{ $activity->user->qr_code }}</div>
                                            @endif
                                        @else
                                            <div class="font-semibold text-gray-500 italic">Deleted User</div>
                                            <div class="text-xs text-gray-400">User ID: {{ $activity->user_id }}</div>
                                        @endif
                                    </td>
                                    @if(auth()->user()->isAdmin() && in_array(auth()->user()->qr_code, $allowToChangeUserType))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <livewire:members.set-activity-void-button 
                                                :member-activity="$activity" 
                                                wire:click="refreshMember"
                                                :key="'void-'.$activity->id" 
                                            />
                                        </td>
                                    @endif
                                    <td class="px-6 space-x-1 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $activity->activityType->name ?? 'N/A' }}
                                        </span>
                                        {{-- add the point value --}}
                                        @if($activity->pointLog?->points > 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ '+' . $activity->pointLog?->points }} {{ $activity->pointLog?->points > 1 ? 'Points' : 'Point' }}
                                            </span>
                                        @endif
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ Str::words($activity->location->name, 4, '...') }}</div>
                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">{{ $activity->activity_time->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $activity->activity_time->format('g:i A') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No activities found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($activities->hasPages())
                <div class="mt-8 md:mx-0 mx-4">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

