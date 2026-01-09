<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="bumblebee">
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
        {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
        {{-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Zalando+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-[#FAF7F4]">
        <x-banner />

        <!-- Toast notifications (DaisyUI) -->
        <div 
            class="toast {{ auth()->check() && auth()->user()->isMember() ? 'toast-bottom' : 'toast-top' }} toast-end z-120"
            x-data="{ 
                toasts: [],
                addToast(type, message) {
                    if (!message || message === '') {
                        return;
                    }
                    const id = Date.now();
                    this.toasts.push({ id, type, message });
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 3000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }
            }"
            x-on:hv-toast.window="addToast($event.detail?.type ?? 'success', $event.detail?.message ?? '')"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div 
                    class="p-2 rounded-lg shadow-lg mb-2 min-w-[300px] max-w-[90vw] flex items-center justify-between"
                    :class="{
                        'bg-green-500 text-white border-green-500': toast.type === 'success',
                        'bg-red-500 text-white border-red-500': toast.type === 'error',
                        'bg-blue-500 text-white border-blue-500': toast.type === 'info',
                        'bg-yellow-500 text-white border-yellow-500': toast.type === 'warning'
                    }"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform translate-x-full"
                >
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <svg 
                            x-show="toast.type === 'success'"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" 
                            class="stroke-current shrink-0 h-5 w-5" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg 
                            x-show="toast.type === 'error'"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" 
                            class="stroke-current shrink-0 h-5 w-5" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg 
                            x-show="toast.type === 'info'"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" 
                            class="stroke-current shrink-0 h-5 w-5" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg 
                            x-show="toast.type === 'warning'"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" 
                            class="stroke-current shrink-0 h-5 w-5" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span x-text="toast.message" class="font-medium text-sm wrap-break-word text-white"></span>
                    </div>
                    <button 
                        @click="removeToast(toast.id)"
                        class="btn btn-sm btn-ghost btn-circle shrink-0 text-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
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

        <!-- QR Scanner Component - Available throughout the system -->
        <livewire:qr-scanner />

        @livewireScripts
        <script>
            document.addEventListener('livewire:init', () => {
                if (!window.Livewire) return;
                Livewire.on('notify', (data) => {
                    // In Livewire v3, when using named parameters like dispatch('notify', type: 'success', message: '...')
                    // the data comes as an object with the parameter names as keys
                    const detail = data || {};
                    window.dispatchEvent(new CustomEvent('hv-toast', { 
                        detail: {
                            type: detail.type || 'success',
                            message: detail.message || ''
                        }
                    }));
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
