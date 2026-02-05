@php
    $allowToChangeUserType = [
        'HJ82CQCH6', // karl
        'TN5TAY6IL', // marnelle
        'BTSKKURCJ', // Jaslyn
    ];
    $canAddActivity = auth()->user()->isAdmin() && in_array(auth()->user()->qr_code, $allowToChangeUserType);
@endphp
<div>
    @if($canAddActivity)
        <livewire:members.add-activity-modal :member="$member" :key="'add-activity-'.$member->id" />
    @endif

    <x-slot name="header">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex md:flex-row flex-col md:gap-0 gap-4 justify-between items-center">
                <div class="flex items-center gap-4">
                    <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                        {{ $member->name }} - Member Profile
                    </h2>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.members.index') }}" class="bg-orange-500 hover:scale-105 transition-all duration-300 cursor-pointer md:text-base text-xs hover:bg-orange-600 text-white font-normal py-2 px-4 rounded-full">
                        <span class="flex items-center gap-1">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Back to Members
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message') || session()->has('error'))
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
                    class="mb-4 {{ session()->has('error') ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700' }} border px-4 py-3 rounded relative md:mx-0 mx-4" 
                    role="alert"
                >
                    <span class="block sm:inline">{{ session('message') ?? session('error') }}</span>
                </div>
            @endif

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
                                <label class="text-sm font-medium text-gray-500">Membership Code</label>
                                <p class="text-gray-900 font-mono">{{ $member->qr_code ?? '-' }}</p>
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
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date Registered</label>
                                <p class="text-gray-900">{{ $member->created_at?->format('d M Y g:i A') ?? '-' }}</p>
                            </div>
                            @if($canAddActivity)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">User Type</label>
                                    <div class="mt-1 flex gap-2">
                                        <select
                                            wire:model="selectedUserType"
                                            class="flex-1 px-3 py-2 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                        >
                                            <option value="member">Member</option>
                                            <option value="admin">Administrator</option>
                                            <option value="merchant_user">Merchant User</option>
                                        </select>
                                        <button
                                            wire:click="updateUserType"
                                            wire:confirm="Are you sure you want to change this user's type? This action will be logged."
                                            class="bg-orange-600 hover:bg-orange-700 px-3 py-2 rounded-md"
                                            title="Update User Type"
                                        >
                                            <svg class="size-5" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#ffffff" stroke="#ffffff">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>disk</title> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="work-case" fill="#ffffff" transform="translate(85.333333, 85.333333)"> <path d="M243.498667,1.42108547e-14 L341.333333,97.8346667 L341.333333,341.333333 L1.42108547e-14,341.333333 L1.42108547e-14,1.42108547e-14 L243.498667,1.42108547e-14 Z M213.333333,234.666667 L128,234.666667 L128,298.688 L213.333333,298.688 L213.333333,234.666667 Z M85.3333333,42.6666667 L42.6666667,42.6666667 L42.6666667,298.666667 L85.3333333,298.666667 L85.3333333,192 L256,192 L256,298.666667 L298.666667,298.666667 L298.666667,115.498667 L256,72.8533333 L256,149.333333 L85.3333333,149.333333 L85.3333333,42.6666667 Z M213.333333,42.6666667 L128,42.6666667 L128,106.688 L213.333333,106.688 L213.333333,42.6666667 Z" id="Mask"> </path> </g> </g> </g>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-500">Only administrators can change user type</p>
                                </div>
                            @else
                                <div>
                                    <label class="text-sm font-medium text-gray-500">User Type</label>
                                    <p class="text-gray-900 capitalize font-bold font-mono">{{ $member->user_type }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Activities / Logs -->
                <div class="lg:col-span-2 space-y-6">

                    <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
                        <div class="rounded-xl border border-gray-300 bg-green-50 p-4 shadow-sm">
                            <p class="text-xs text-gray-600">Total Points</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $member->total_points }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-300 bg-yellow-50 p-4 shadow-sm">
                            <p class="text-xs text-gray-600">Used Vouchers</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $member->vouchers()->wherePivot('status', 'redeemed')->count() }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-300 bg-orange-50 p-4 shadow-sm">
                            <p class="text-xs text-gray-600">Ranking (Coming soon)</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{-- {{ $member->vouchers->count() }} --}}
                                -
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4 border-b pb-2">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Member Activities</h3>

                            @if($canAddActivity)
                                <button
                                    type="button"
                                    wire:click="$dispatch('openAddActivityModal')"
                                    class="text-sm hover:underline hover:scale-105 transition-all duration-300 text-orange-500 hover:text-orange-600 cursor-pointer"
                                >
                                    Manually Add Activity
                                </button>
                            @endif
                        </div>
                        <div class="space-y-3 overflow-y-auto max-h-[350px]">
                            @forelse($member->memberActivities as $activity)
                                <div class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-bold text-gray-800">
                                                    {{ $activity->activityType?->description ?? $activity->activityType?->name ?? 'Some Activity' }}
                                                </p>
                                                @if($canAddActivity)
                                                    <livewire:members.set-activity-void-button 
                                                        :member-activity="$activity" 
                                                        wire:click="refreshMember"
                                                        :key="'void-'.$activity->id" 
                                                    />
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">
                                                {{ $activity->activity_time?->format('M d, Y g:i A') ?? '-' }}
                                                @if($activity->location)
                                                    • {{ $activity->location->name }}
                                                @endif
                                            </p>
                                            @if($activity->description)
                                                <p class="text-sm text-gray-700 mt-1">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                        @if($activity->pointLog !== null)
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">Points</p>
                                                <p class="text-lg font-bold text-gray-800">
                                                    {{ $activity->pointLog->points }}
                                                </p>
                                            </div>
                                        @endif
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

                        <div class="space-y-3 overflow-y-auto max-h-[350px]">
                            @forelse($member->pointLogs as $log)
                                <div class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $log->activityType?->description ?? 'Points' }}
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


