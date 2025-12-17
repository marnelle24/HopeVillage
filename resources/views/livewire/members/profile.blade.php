<div>
    <x-slot name="header">
        <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ $member->name }} - Member Profile
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.members.index') }}" class="bg-slate-600 md:text-base text-xs hover:bg-slate-700 text-white font-normal py-2 px-4 rounded-lg">
                    Back to Members
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:mx-0 mx-4">
                <!-- Left: Member info -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Member Information</h3>

                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Name</label>
                                <p class="text-gray-900">{{ $member->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-gray-900">{{ $member->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">FIN</label>
                                <p class="text-gray-900 font-mono">{{ $member->fin ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">WhatsApp</label>
                                <p class="text-gray-900">{{ $member->whatsapp_number ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Age</label>
                                    <p class="text-gray-900">{{ $member->age ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Gender</label>
                                    <p class="text-gray-900">{{ $member->gender ?? '-' }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Verified</label>
                                <p>
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $member->is_verified ? 'bg-green-100 text-green-800 border border-green-500' : 'bg-yellow-100 text-yellow-800 border border-yellow-500' }}">
                                        {{ $member->is_verified ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Stats</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <p class="text-xs text-gray-600">Total Points</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $member->total_points }}</p>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <p class="text-xs text-gray-600">Claimed Vouchers</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $member->vouchers->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Activities / Logs -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Recent Member Activities</h3>

                        <div class="space-y-3">
                            @forelse($member->memberActivities as $activity)
                                <div class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $activity->activityType?->name ?? 'Activity' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $activity->activity_time?->format('M d, Y g:i A') ?? '-' }}
                                                @if($activity->location)
                                                    • {{ $activity->location->name }}
                                                @endif
                                            </p>
                                            @if($activity->description)
                                                <p class="text-sm text-gray-700 mt-2">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Points</p>
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $activity->pointLog?->points ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-500 font-semibold">
                                    No activities found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Recent Point Logs</h3>

                        <div class="space-y-3">
                            @forelse($member->pointLogs as $log)
                                <div class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $log->activityType?->name ?? 'Points' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $log->awarded_at?->format('M d, Y g:i A') ?? '-' }}
                                                @if($log->location)
                                                    • {{ $log->location->name }}
                                                @endif
                                            </p>
                                            @if($log->description)
                                                <p class="text-sm text-gray-700 mt-2">{{ $log->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Points</p>
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $log->points }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-500 font-semibold">
                                    No point logs found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


