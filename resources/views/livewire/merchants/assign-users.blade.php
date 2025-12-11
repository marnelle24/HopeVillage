<div>
    <x-dialog-modal wire:model="showModal" maxWidth="2xl">
        <x-slot name="title">
            Assign Users to {{ $merchant->name }}
        </x-slot>

        <x-slot name="content">
            @if (session()->has('users-assigned'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('users-assigned') }}</span>
                </div>
            @endif

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
                        class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition"
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
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
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
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Close
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
