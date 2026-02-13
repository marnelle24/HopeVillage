<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <!-- Toast notifications (DaisyUI) - for admin/member pages using this layout -->
        <div
            class="toast {{ auth()->check() && auth()->user()->isMember() ? 'toast-bottom' : 'toast-top' }} toast-end z-[99999999]"
            x-data="{
                toasts: [],
                addToast(type, message) {
                    if (!message || message === '') return;
                    const id = Date.now();
                    this.toasts.push({ id, type, message });
                    setTimeout(() => this.removeToast(id), 3000);
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
                        <svg x-show="toast.type === 'success'" x-cloak xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <svg x-show="toast.type === 'error'" x-cloak xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <svg x-show="toast.type === 'info'" x-cloak xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <svg x-show="toast.type === 'warning'" x-cloak xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span x-text="toast.message" class="font-medium text-sm wrap-break-word text-white"></span>
                    </div>
                    <button type="button" @click="removeToast(toast.id)" class="btn btn-sm btn-ghost btn-circle shrink-0 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="min-h-screen bg-gray-100 {{ auth()->check() && auth()->user()->isAdmin() ? 'pt-16' : '' }}">
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
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        <!-- QR Scanner Component - Available throughout the system -->
        <livewire:qr-scanner />

        @livewireScripts
        @stack('scripts')
    </body>
</html>

