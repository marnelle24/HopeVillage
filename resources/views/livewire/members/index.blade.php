<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Members') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.members.activities') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-normal py-2 px-4 rounded-lg text-sm">
                    View Activities
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search members (name, email, FIN, WhatsApp)..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    <div>
                        <select
                            wire:model.live="verifiedFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="all">All</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">Unverified</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg md:mx-0 mx-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">FIN</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Verified</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Points</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activities</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($members as $member)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                        <div class="text-xs text-gray-500 flex gap-1">
                                            <svg class="size-3" fill="#000000" viewBox="0 0 24 24" id="email" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path id="secondary" d="M20.61,5.23l-8,6.28a1,1,0,0,1-1.24,0l-8-6.28A1,1,0,0,0,3,6V18a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V6A1,1,0,0,0,20.61,5.23Z" style="fill: #2ca9bc; stroke-width: 2;"></path><path id="primary" d="M20,19H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H20a1,1,0,0,1,1,1V18A1,1,0,0,1,20,19ZM20,5H4a1,1,0,0,0-.62.22l8,6.29a1,1,0,0,0,1.24,0l8-6.29A1,1,0,0,0,20,5Z" style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path></g>
                                            </svg>
                                            {{ $member->email ?? 'N/A' }}
                                        </div>
                                        @if($member->whatsapp_number)
                                            <div class="text-xs text-gray-500 flex gap-1">
                                                <svg class="size-3" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 500 500" xml:space="preserve" fill="#000000">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#FFFFFF;stroke:#FFFFFF;stroke-width:10;stroke-miterlimit:10;} .st1{fill:#343434;} .st2{fill:#8ECDFC;} .st3{fill:#E9E9E9;} .st4{fill:#A8F1E5;} </style> <g id="border"> <path class="st0" d="M352.268,41.873H147.661c-15.877,0-28.665,12.788-28.665,28.665v358.924c0,15.877,12.788,28.665,28.665,28.665 h204.607c15.877,0,28.737-12.788,28.737-28.665V70.538C381.005,54.66,368.145,41.873,352.268,41.873z"></path> </g> <g id="object" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg"> <g> <path class="st1" d="M147.66,41.873h204.607c15.877,0,28.737,12.788,28.737,28.665v358.924c0,15.877-12.86,28.665-28.737,28.665 H147.66c-15.877,0-28.665-12.788-28.665-28.665V70.538C118.995,54.66,131.783,41.873,147.66,41.873L147.66,41.873z"></path> <path class="st2" d="M154.629,79.23h190.67c12.572,0,22.702,10.13,22.702,22.702v284.568c0,12.644-10.13,22.774-22.702,22.774 h-190.67c-12.572,0-22.702-10.13-22.702-22.774V101.933C131.927,89.36,142.057,79.23,154.629,79.23L154.629,79.23z"></path> <path class="st3" d="M342.569,64.719c0,3.52-2.946,6.322-6.466,6.322c-3.592,0-6.538-2.802-6.538-6.322 c0-3.449,2.946-6.251,6.538-6.251C339.623,58.468,342.569,61.27,342.569,64.719L342.569,64.719z"></path> <path class="st3" d="M234.877,56.528h26.294c3.305,0,5.963,2.658,5.963,5.963c0,3.305-2.658,5.963-5.963,5.963h-26.294 c-3.305,0-5.963-2.658-5.963-5.963C228.914,59.187,231.572,56.528,234.877,56.528L234.877,56.528z"></path> <path class="st3" d="M181.067,59.833c0,2.011-1.509,3.735-3.448,3.735c-1.868,0-3.448-1.724-3.448-3.735 c0-2.084,1.581-3.736,3.448-3.736C179.559,56.097,181.067,57.75,181.067,59.833L181.067,59.833z"></path> <path class="st4" d="M156.282,79.23c-12.788,0-23.205,9.052-24.283,20.763l236.002,272.714V101.933 c0-12.572-10.848-22.702-24.355-22.702H156.282z"></path> <path class="st3" d="M262.249,430.755c0,6.466-5.388,11.639-12.07,11.639c-6.609,0-11.998-5.173-11.998-11.639 c0-6.394,5.388-11.638,11.998-11.638C256.861,419.117,262.249,424.362,262.249,430.755L262.249,430.755z"></path> </g> </g> </g>
                                                </svg>
                                                {{ $member->whatsapp_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-mono">{{ $member->fin ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-lg border {{ $member->is_verified ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200' }}">
                                            {{ $member->is_verified ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $member->total_points }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <div class="text-xs text-gray-600">Member Activities: <span class="font-semibold">{{ $member->member_activities_count }}</span></div>
                                        <div class="text-xs text-gray-600">Event Registrations: <span class="font-semibold">{{ $member->event_registrations_count }}</span></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($member->fin)
                                                <a
                                                    href="{{ route('admin.members.profile', $member->fin) }}"
                                                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-2 rounded-lg"
                                                >
                                                    View
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-2 bg-gray-400 text-white text-xs font-semibold px-3 py-2 rounded-lg cursor-not-allowed">
                                                    View
                                                </span>
                                            @endif
                                            @if(auth()->user()->isAdmin())
                                                <button
                                                    wire:click="delete({{ $member->id }})"
                                                    wire:confirm="Are you sure you want to delete this member? This action cannot be undone."
                                                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
                                                    title="Delete Member"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No members found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="p-4">
                        {{ $members->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


