<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
        <div class="flex items-center gap-2">
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 px-4 lg:px-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="lg:text-2xl text-md font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                    </div>
                    <div class="px-4 py-2 bg-green-100 text-green-800 border lg:scale-100 scale-75 border-green-600 rounded-lg">
                        <span class="font-semibold">Administrator</span>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4 text-md">
                New dashboard v2 is now available.
                <a href="{{ route('admin.dashboard.v2') }}" class="text-blue-500 underline cursor-pointer">Check it out here</a>.
            </p>
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 lg:mx-0 mx-4">

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mt-6">
                    <!-- Stats Card 1 -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-300">
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Total Locations</h3>
                        <p class="text-3xl font-bold text-gray-600">{{ \App\Models\Location::count() }}</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">Manage facility locations</p>
                    </div>

                    <!-- Stats Card 2 -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-300">
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Total Members</h3>
                        <p class="text-3xl font-bold text-gray-600">{{ \App\Models\User::where('user_type', 'member')->count() }}</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">View all members</p>
                    </div>

                    <!-- Stats Card 3 -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-300">
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Active Events</h3>
                        <p class="text-3xl font-bold text-gray-600">{{ \App\Models\Event::where('status', 'published')->count() }}</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">Monitor events</p>
                    </div>

                    <!-- Stats Card 4 -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-300">
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Active Vouchers</h3>
                        <p class="text-3xl font-bold text-gray-600">{{ \App\Models\Voucher::where('is_active', true)->count() }}</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">Monitor vouchers</p>
                    </div>
                </div>
            </div>
            <br />
            <br />
            <h3 class="md:text-2xl text-xl font-bold text-orange-500 mb-4 lg:mx-0 mx-4">Overall Performance & Analytics</h3>
            <!-- Charts Section -->
            <div class="lg:mx-0 mx-4">
                <livewire:admin.dashboard-charts />
            </div>
        </div>
    </div>
</x-app-layout>

