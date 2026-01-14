<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
        <title>Hope Village | Guest Workers & Migrants Online Support Community</title>

        <meta name="description" content="{{ 'Hope Village is the online community for Migrants & Guest Workers. It is a platform for the community to connect with each other and to share their stories and experiences.' }}">
        <link rel="icon" type="image/png" href="{{ asset('hv-logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('hv-logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('hv-logo.png') }}">
        <link rel="apple-touch-icon-precomposed" href="{{ asset('hv-logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('hv-logo.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('hv-logo.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('hv-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
    </head>
    <body class="bg-orange-100">
        <div class="font-sans text-gray-900 bg-orange-100 antialiased px-4">
            {{ $slot }}
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
