<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Member Activities') }}
            </h2>
            <a href="{{ route('admin.members.index') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-normal py-2 px-4 rounded-lg text-sm">
                Back to Members
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <div class="grid lg:grid-cols-4 grid-cols-1 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by member name, email, FIN, or description..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <div class="lg:col-span-2 flex lg:flex-row flex-col gap-4">
                        <!-- Location Filter -->
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
    
                        <!-- Activity Type Filter -->
                        <div>
                            <select
                                wire:model.live="activityTypeFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Activity Types</option>
                                @foreach($activityTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Date Filter -->
                        <div>
                            <select
                                wire:model.live="dateFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">Last 7 Days</option>
                                <option value="month">Last 30 Days</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Activities Table -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg md:mx-0 mx-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $activity->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $activity->user->email }}</div>
                                        @if($activity->user->fin)
                                            <div class="text-xs text-gray-500 font-mono">{{ $activity->user->fin }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $activity->activityType->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $activity->location->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $activity->activity_time->format('M d, Y') }}</div>
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

                @if($activities->hasPages())
                    <div class="p-4">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

