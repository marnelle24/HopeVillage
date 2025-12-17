<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('Members') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                        <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                        @if($member->whatsapp_number)
                                            <div class="text-xs text-gray-500">{{ $member->whatsapp_number }}</div>
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
                                        <a
                                            href="{{ route('admin.members.profile', $member->fin) }}"
                                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-2 rounded-lg"
                                        >
                                            View
                                        </a>
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


