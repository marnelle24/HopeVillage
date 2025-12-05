<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                            <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->name }}!</p>
                        </div>
                        <div class="px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                            <span class="font-semibold">Admin User</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    <!-- Stats Card 1 -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Total Locations</h3>
                        <p class="text-3xl font-bold text-blue-600">0</p>
                        <p class="text-sm text-blue-700 mt-2">Manage facility locations</p>
                    </div>

                    <!-- Stats Card 2 -->
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-green-900 mb-2">Total Members</h3>
                        <p class="text-3xl font-bold text-green-600">0</p>
                        <p class="text-sm text-green-700 mt-2">View all members</p>
                    </div>

                    <!-- Stats Card 3 -->
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Active Bookings</h3>
                        <p class="text-3xl font-bold text-purple-600">0</p>
                        <p class="text-sm text-purple-700 mt-2">Monitor bookings</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Manage Locations
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Manage Amenities
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Create Events
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            View Analytics
                        </button>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800">
                        <strong>Note:</strong> This is a test UI dashboard for Admin users. Full functionality will be implemented later.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

