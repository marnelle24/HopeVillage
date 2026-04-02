<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Hope Village | Service Unavailable</title>
        <meta name="description" content="{{ 'A community hub for migrants community in Singapore - brought to you by Hope Initiative Allaince in partnership with Advancer IFM and other supporting partners.' }}">
        <link rel="icon" type="image/png" href="{{ asset('hv-logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('hv-logo.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-[#FAF7F4]">
        <div class="font-sans text-gray-900 bg-orange-100 antialiased px-4">
            <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl w-full">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 sm:p-12 text-center">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-orange-100 mb-4">
                                    <img src="{{ asset('hv-logo.png') }}" alt="Hope Village Logo" class="w-20 h-20">
                                </div>
                            </div>

                            <h1 class="text-4xl font-bold text-gray-600 mb-4">Maintenance Mode</h1>

                            <h2 class="text-3xl font-semibold text-gray-500 mb-4">Service Temporarily Unavailable</h2>
                            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                                We are performing maintenance right now.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                                <button onclick="window.location.reload()"
                                        class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M19 8a8 8 0 00-13.657-3.343L4 10m0 0l1.343-1.343M20 16a8 8 0 01-13.657 3.343L4 14m0 0l1.343 1.343"></path>
                                    </svg>
                                    Refresh
                                </button>
                            </div>

                            <p class="text-sm text-gray-500 mt-6">
                                If this continues, please contact support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

