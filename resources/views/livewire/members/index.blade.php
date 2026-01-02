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

            <!-- Password Reset Card -->
            @if($showPasswordReset && $selectedUser)
                <div class="max-w-2xl mx-auto">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 text-center">
                        You are about to reset the password of
                        <span class="text-orange-600 font-bold text-xl italic underline">{{ $selectedUser->name }}</span>
                    </h2>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-xl md:mx-0 mx-4">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Reset Password</h3>
                                <button
                                    wire:click="cancelPasswordReset"
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Cancel"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- User Information -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-8 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Name</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $selectedUser->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $selectedUser->email ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">FIN</label>
                                        <p class="text-sm font-medium text-gray-900 font-mono">{{ $selectedUser->fin ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">WhatsApp</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $selectedUser->whatsapp_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Reset Form -->
                            <form wire:submit="resetPassword">
                                <div class="space-y-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            New Password
                                        </label>
                                        <div class="relative" x-data="{ showPassword: false }">
                                            <input
                                                type="password"
                                                id="password"
                                                wire:model="password"
                                                x-bind:type="showPassword ? 'text' : 'password'"
                                                class="w-full px-4 py-4 pr-10 text-gray-700 border border-orange-300 rounded-full focus:ring-1 transition-all duration-300 focus:ring-orange-500 focus:border-orange-500"
                                                placeholder="Enter new password"
                                                required
                                            >
                                            <button
                                                type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                                tabindex="-1"
                                            >
                                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                            Confirm Password
                                        </label>
                                        <div class="relative" x-data="{ showPasswordConfirm: false }">
                                            <input
                                                type="password"
                                                id="password_confirmation"
                                                wire:model="password_confirmation"
                                                x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                                                class="w-full px-4 py-4 pr-10 text-gray-700 border border-orange-300 rounded-full focus:ring-1 transition-all duration-300 focus:ring-orange-500 focus:border-orange-500"
                                                placeholder="Confirm new password"
                                                required
                                            >
                                            <button
                                                type="button"
                                                @click="showPasswordConfirm = !showPasswordConfirm"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                                tabindex="-1"
                                            >
                                                <svg x-show="!showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <svg x-show="showPasswordConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="text-xs text-gray-500 mt-2 bg-orange-50 border border-orange-200 rounded-lg p-2">
                                        <p class="text-orange-500">Password must be at least 8 characters and contain:</p>
                                        <ul class="list-disc list-inside mt-1 space-y-1 text-orange-500">
                                            <li>At least one uppercase letter</li>
                                            <li>At least one lowercase letter</li>
                                            <li>At least one number</li>
                                            <li>At least one special character</li>
                                            <li>Example: MyP@ssw0rd</li>
                                        </ul>
                                    </div>

                                    <div class="flex justify-end gap-3 pt-4">
                                        <button
                                            type="button"
                                            wire:click="cancelPasswordReset"
                                            class="px-4 py-3 border border-gray-300 cursor-pointer rounded-full text-center text-md font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-8 py-3 bg-orange-600 cursor-pointer hover:bg-orange-700 text-center text-white rounded-full text-lg font-medium transition-colors duration-300"
                                        >
                                            Reset Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search and Filter -->
            @if(!$showPasswordReset)
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 md:mx-0 mx-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search members (name, email, FIN, WhatsApp)..."
                            class="w-full px-4 py-2 border text-gray-700 border-gray-500 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                    </div>
                    <div>
                        <select
                            wire:model.live="verifiedFilter"
                            class="w-full px-4 py-2 border text-gray-700 border-gray-500 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                            <option value="all">All</option>
                            <option value="verified">Verified</option>
                            <option value="unverified">Unverified</option>
                        </select>
                    </div>
                </div>
            </div>
            @endif

            @if(!$showPasswordReset)
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
                                        <div class="font-semibold text-gray-900 text-lg">{{ $member->name }}</div>
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
                                        <div class="flex items-center justify-end gap-1">
                                            @if($member->fin)
                                                <a
                                                    title="View Member Profile"
                                                    href="{{ route('admin.members.profile', $member->fin) }}"
                                                    class="flex gap-1 items-center hover:scale-105 cursor-pointer duration-300 text-xs text-indigo-500 hover:text-indigo-600 transition-all"
                                                >
                                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                    View Profile
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-2 bg-gray-400 text-white text-xs font-semibold px-3 py-2 rounded-lg cursor-not-allowed">
                                                    View
                                                </span>
                                            @endif
                                            @if(auth()->user()->isAdmin())
                                                <span class="text-gray-500 text-xs px-1">|</span>
                                                {{-- add the delete button --}}
                                                <button
                                                    wire:click="delete({{ $member->id }})"
                                                    wire:confirm="Are you sure you want to delete this member? This action cannot be undone."
                                                    class="flex gap-1 items-center text-red-500 hover:text-red-600 transition-all hover:scale-105 cursor-pointer duration-300 text-xs"
                                                    title="Delete Member"
                                                >
                                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            @endif
                                            @if(auth()->user()->isAdmin())
                                                <span class="text-gray-500 text-xs px-1">|</span>
                                                <button
                                                    wire:click="triggerPasswordReset({{ $member->id }})"
                                                    class="flex gap-1 items-center text-indigo-500 hover:text-indigo-600 transition-all hover:scale-105 cursor-pointer duration-300 text-xs"
                                                    title="Reset Password"
                                                >
                                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>

                                                    Password
                                                </button>
                                                {{-- <button
                                                    wire:click="delete({{ $member->id }})"
                                                    wire:confirm="Are you sure you want to delete this member? This action cannot be undone."
                                                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
                                                    title="Delete Member"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button> --}}
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
            </div>
            @if($members->hasPages())
                <div class="p-4">
                    {{ $members->links() }}
                </div>
            @endif
            @endif
        </div>
    </div>
</div>


