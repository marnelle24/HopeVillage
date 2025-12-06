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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

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
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Active Amenities</h3>
                        <p class="text-3xl font-bold text-gray-600">{{ \App\Models\Amenity::count() }}</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">Monitor amenities</p>
                    </div>

                    <!-- Stats Card 4 -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-300">
                        <h3 class="lg:text-lg text-sm font-semibold text-gray-900 mb-2">Active Bookings</h3>
                        <p class="text-3xl font-bold text-gray-600">4</p>
                        <p class="lg:text-sm text-xs text-gray-700 mt-2">Monitor bookings</p>
                    </div>
                </div>

                <div class="mt-12">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div 
                            class="group cursor-pointer bg-blue-50 hover:bg-blue-100 hover:shadow-lg hover:border-blue-600/80 hover:-translate-y-0.5 transition-all duration-200 rounded-lg shadow border border-blue-600/60 flex flex-col min-h-44 justify-center items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 stroke-blue-700/50 group-hover:stroke-blue-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                            </svg>
                            <p class="lg:text-lg text-sm font-semibold text-blue-700/50 group-hover:text-blue-700 mb-2">Scan QR Code</p>
                        </div>
                        <a
                            href="{{ route('admin.locations.index') }}"
                            class="group cursor-pointer bg-blue-50 hover:bg-blue-100 hover:shadow-lg hover:border-blue-600/80 hover:-translate-y-0.5 transition-all duration-200 rounded-lg shadow border border-blue-600/60 flex flex-col min-h-44 justify-center items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 stroke-blue-700/50 group-hover:stroke-blue-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <p class="lg:text-lg text-sm font-semibold text-blue-700/50 group-hover:text-blue-700 mb-2">Manage Locations</p>
                        </a>
                        <div 
                            class="group cursor-pointer bg-blue-50 hover:bg-blue-100 hover:shadow-lg hover:border-blue-600/80 hover:-translate-y-0.5 transition-all duration-200 rounded-lg shadow border border-blue-600/60 flex flex-col min-h-44 justify-center items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 stroke-blue-700/50 group-hover:stroke-blue-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                            </svg>
                            <p class="lg:text-lg text-sm font-semibold text-blue-700/50 group-hover:text-blue-700 mb-2">Manage Amenities</p>
                        </div>
                        <div 
                            class="group cursor-pointer bg-blue-50 hover:bg-blue-100 hover:shadow-lg hover:border-blue-600/80 hover:-translate-y-0.5 transition-all duration-200 rounded-lg shadow border border-blue-600/60 flex flex-col min-h-44 justify-center items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 stroke-blue-700/50 group-hover:stroke-blue-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <p class="lg:text-lg text-sm font-semibold text-blue-700/50 group-hover:text-blue-700 mb-2">View Analytics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

