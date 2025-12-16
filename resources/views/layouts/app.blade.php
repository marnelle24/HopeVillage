<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
        <title>Hope Village | Guest Workers & Migrants Online Support Community</title>
        <meta name="description" content="{{ 'Hope Village is the online community for Migrants & Guest Workers. It is a platform for the community to connect with each other and to share their stories and experiences.' }}">
        <favicon href="{{ asset('favicon.ico') }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon-precomposed" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon-precomposed-180x180" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon-precomposed-120x120" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon-precomposed-76x76" href="{{ asset('favicon.ico') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-[#FAF7F4]"">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="{{ auth()->check() && auth()->user()->isMember() ? 'pb-24 sm:pb-0' : '' }}">
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        @stack('scripts')
    </body>
</html>
