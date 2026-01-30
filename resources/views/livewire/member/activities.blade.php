<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20">
    <!-- Filters Section -->
    <div class="px-4 mt-6">
        <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
            <div class="grid grid-cols-3 gap-2 items-center">
                <!-- Search -->
                <label class="col-span-2 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-5 w-5 opacity-70">
                        <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        class="w-full pl-10 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400" 
                        placeholder="Search activities..." 
                    />
                </label>

                <!-- Date Filter -->
                <div class="col-span-1">
                    <select
                        wire:model.live="dateFilter"
                        class="border border-gray-300 focus:ring-2 focus:ring-orange-500 hover:ring-orange-500 active:ring-orange-500 rounded-full text-sm w-full"
                    >
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">Last 7 Days</option>
                        <option value="month">Last 30 Days</option>
                    </select>
                </div>
            </div>

            <!-- Results Count -->
            <p class="text-xs text-base-content/60 mt-2 italic text-left">
                {{ $activities->total() }} {{ $activities->total() === 1 ? 'activity' : 'activities' }} found
            </p>
        </div>

            <!-- Activities List -->
            <div class="space-y-3">
                @forelse($activities as $activity)
                    <div class="{{(isset($activity->metadata['status']) && $activity->metadata['status'] === 'void') ? 'bg-gray-200' : 'bg-white'}} card shadow-md hover:shadow-xl border border-base-300 rounded-2xl overflow-hidden transition-all duration-300">
                        <div class="card-body p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <!-- Activity Type and Points -->
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="badge badge-primary badge-xs">
                                            {{ $activity->activityType->name ?? 'N/A' }}
                                        </span>
                                        @if($activity->pointLog && $activity->pointLog->points > 0)
                                            <span class="badge badge-success badge-xs gap-1">
                                                +{{ $activity->pointLog->points }} {{ $activity->pointLog->points === 1 ? 'Point' : 'Points' }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Description -->
                                    @if($activity->description)
                                        <p class="text-sm text-base-content/70 mb-2 line-clamp-2">
                                            {{ $activity->description }}
                                        </p>
                                    @endif

                                    <!-- Date and Time -->
                                    <div class="flex items-center gap-1 text-xs text-base-content/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $activity->activity_time->format('M d, Y') }}</span>
                                        <span class="">•</span>
                                        <span>{{ $activity->activity_time->format('g:i A') }}</span>
                                    </div>
                                </div>
                                @if(isset($activity->metadata['status']) && $activity->metadata['status'] === 'void')
                                    <span class="badge badge-ghost badge-sm text-gray-400 shrink-0">Void</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card bg-base-100 shadow-md border border-base-300 rounded-2xl">
                        <div class="card-body items-center justify-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-base-300 mb-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-base-content mb-2">No activities found</h3>
                            <p class="text-sm text-base-content/60 text-center">
                                @if($search || $dateFilter !== 'all')
                                    Try adjusting your filters to see more results.
                                @else
                                    You haven't completed any activities yet. Start exploring to earn points!
                                @endif
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="mt-6">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</div>