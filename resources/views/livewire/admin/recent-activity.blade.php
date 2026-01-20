<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Recent Activity
        </h2>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3 p-3 rounded-lg bg-base-200">
                    <div class="flex-shrink-0">
                        <div class="avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-10">
                                <span class="text-xs">{{ substr($activity->user->name ?? 'U', 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">{{ $activity->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-base-content/70">{{ $activity->activityType->name ?? 'Activity' }}</p>
                        <p class="text-xs text-base-content/60 mt-1">
                            {{ $activity->location->name ?? 'N/A' }} • {{ $activity->activity_time->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-base-content/70 py-4">No recent activity</p>
            @endforelse
        </div>
    </div>
</div>
