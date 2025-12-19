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
    </head>
    <body class="font-sans antialiased bg-[#FAF7F4]">
        <x-banner />

        <!-- Toast notifications (Livewire event: notify) -->
        <div
            x-data="{ open: false, message: '', type: 'success', t: null }"
            x-on:hv-toast.window="
                type = $event.detail?.type ?? 'success';
                message = $event.detail?.message ?? '';
                open = true;
                clearTimeout(t);
                t = setTimeout(() => open = false, 2500);
            "
            x-show="open"
            x-transition.opacity
            x-cloak
            class="fixed bottom-[6rem] right-0 z-[120] max-w-[90vw] w-[360px]"
            role="status"
            aria-live="polite"
        >
            <div
                class="rounded-l-xl border shadow-lg px-4 py-3 opacity-90 transition-opacity duration-300"
                :class="type === 'success'
                    ? 'bg-emerald-100 border-emerald-200 text-emerald-800'
                    : (type === 'error'
                        ? 'bg-red-50 border-red-200 text-red-800'
                        : 'bg-slate-50 border-slate-200 text-slate-800')"
            >
                <p class="text-sm font-semibold" x-text="message"></p>
            </div>
        </div>

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
        <script>
            document.addEventListener('livewire:init', () => {
                if (!window.Livewire) return;
                Livewire.on('notify', (...args) => {
                    const detail = args?.[0] ?? {};
                    window.dispatchEvent(new CustomEvent('hv-toast', { detail }));
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
