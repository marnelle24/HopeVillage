<div>
    <x-dialog-modal wire:model="showModal" maxWidth="2xl">
        <x-slot name="title">
            Manage Users for {{ $merchant->name }}
        </x-slot>

        <x-slot name="content">
            @if (session()->has('user-created'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('user-created') }}</span>
                </div>
            @endif

            @if (session()->has('user-removed'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('user-removed') }}</span>
                </div>
            @endif

            @if (session()->has('users-assigned'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('users-assigned') }}</span>
                </div>
            @endif

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        wire:click="switchTab('create')"
                        type="button"
                        class="@if($activeTab === 'create') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Create New User
                    </button>
                    <button
                        wire:click="switchTab('assign')"
                        type="button"
                        class="@if($activeTab === 'assign') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Assign Existing User
                    </button>
                    <button
                        wire:click="switchTab('list')"
                        type="button"
                        class="@if($activeTab === 'list') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Assigned Users ({{ $merchant->users->count() }})
                    </button>
                </nav>
            </div>

            <!-- Create New User Tab -->
            @if($activeTab === 'create')
                @if($showAddForm)
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                id="name"
                                wire:model.blur="name" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                                placeholder="Full Name"
                            >
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input 
                                type="email" 
                                id="email"
                                wire:model.blur="email" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                                placeholder="email@example.com"
                            >
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input 
                                type="password" 
                                id="password"
                                wire:model.blur="password" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror"
                                placeholder="Minimum 8 characters"
                            >
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <input 
                                type="password" 
                                id="password_confirmation"
                                wire:model.blur="password_confirmation" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Confirm password"
                            >
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button 
                                wire:click="cancelAddUser"
                                type="button"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>
                            <button 
                                wire:click="createUser"
                                type="button"
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                            >
                                Create User
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Create New Merchant User</h3>
                        <p class="mt-1 text-sm text-gray-500">Create a new user account and assign them to this merchant.</p>
                        <div class="mt-6">
                            <button 
                                wire:click="showAddUserForm"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Create New User
                            </button>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Assign Existing User Tab -->
            @if($activeTab === 'assign')
                <div>
                    <div class="mb-4">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search users by name or email..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    @if(count($selectedUserIds) > 0)
                        <div class="mb-4 bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                            <p class="text-sm font-medium text-indigo-900">
                                {{ count($selectedUserIds) }} user(s) selected
                            </p>
                            <button 
                                wire:click="assignSelectedUsers"
                                class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm"
                            >
                                Assign Selected Users
                            </button>
                        </div>
                    @endif

                    @if($availableUsers->count() > 0)
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($availableUsers as $user)
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleUser({{ $user->id }})"
                                        @checked(in_array($user->id, $selectedUserIds))
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    >
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        @if($user->merchants->count() > 0)
                                            <p class="text-xs text-gray-400 mt-1">
                                                Already assigned to {{ $user->merchants->count() }} other merchant(s)
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $availableUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No users available</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($this->search)
                                    No merchant users found matching your search.
                                @else
                                    All merchant users are already assigned to this merchant.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Assigned Users List Tab -->
            @if($activeTab === 'list')
                @if($merchant->users->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($merchant->users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if(Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                @php
                                                    $pivot = $user->merchants()->where('merchants.id', $merchant->id)->first()?->pivot;
                                                    $isDefault = $pivot && $pivot->is_default;
                                                @endphp
                                                @if($isDefault)
                                                    <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full font-semibold">Default</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                            @if($user->merchants->count() > 1)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($user->merchants as $userMerchant)
                                                        <span class="text-xs px-2 py-0.5 {{ $userMerchant->id == $merchant->id ? 'bg-indigo-100 text-indigo-800 font-semibold' : 'bg-gray-200 text-gray-600' }} rounded">
                                                            {{ $userMerchant->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    wire:click="removeUser({{ $user->id }})"
                                    wire:confirm="Are you sure you want to remove this user from this merchant?"
                                    class="ml-4 text-red-600 hover:text-red-800 transition-colors"
                                    title="Remove User from Merchant"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">No users have been assigned to this merchant yet.</p>
                    </div>
                @endif
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Close
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
