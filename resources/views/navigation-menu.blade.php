@php
    $navBgClass = 'bg-white';

    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            $navBgClass = 'bg-gray-100';
        } elseif (auth()->user()->isMerchantUser()) {
            $navBgClass = 'bg-green-100';
        } else {
            // Member (default)
            $navBgClass = 'bg-yellow-100';
        }
    }
@endphp

@if(auth()->check() && auth()->user()->isAdmin())
    <nav x-data="{ open: false }" x-cloak class="{{ $navBgClass }} border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            {{-- <x-application-mark class="block h-9 w-auto" /> --}}
                            {{-- <span class="text-xl font-bold text-gray-800">Hope Village</span> --}}
                            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-12">
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        @if(auth()->user()->isAdmin())
                            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.locations.index') }}" :active="request()->routeIs('admin.locations*')">
                                {{ __('Locations') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.events.index') }}" :active="request()->routeIs('admin.events*')">
                                {{ __('Events') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.members.index') }}" :active="request()->routeIs('admin.members.index') || request()->routeIs('admin.members.profile')">
                                {{ __('Members') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.members.activities') }}" :active="request()->routeIs('admin.members.activities')">
                                {{ __('Member Activities') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.vouchers.index') }}" :active="request()->routeIs('admin.vouchers*')">
                                {{ __('Vouchers') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.merchants.index') }}" :active="request()->routeIs('admin.merchants*')">
                                {{ __('Merchants') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.point-system.index') }}" :active="request()->routeIs('admin.point-system*')">
                                {{ __('Point System') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('admin.raffle') }}" :active="request()->routeIs('admin.raffle')">
                                {{ __('Raffle') }}
                            </x-nav-link>
                            {{-- <x-nav-link href="{{ route('admin.raffle.v2') }}" :active="request()->routeIs('admin.raffle.v2')">
                                {{ __('Raffle V2') }} --}}
                            {{-- </x-nav-link> --}}
                        @elseif(auth()->user()->isMerchantUser())
                            <x-nav-link href="{{ route('merchant.dashboard') }}" :active="request()->routeIs('merchant.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('merchant.vouchers.index') }}" :active="request()->routeIs('merchant.vouchers*')">
                                {{ __('Vouchers') }}
                            </x-nav-link>
                        @endif
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.locations.index') }}" :active="request()->routeIs('admin.locations*')">
                        {{ __('Locations') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.events.index') }}" :active="request()->routeIs('admin.events*')">
                        {{ __('Events') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.members.index') }}" :active="request()->routeIs('admin.members.index') || request()->routeIs('admin.members.profile')">
                        {{ __('Members') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.members.activities') }}" :active="request()->routeIs('admin.members.activities')">
                        {{ __('Member Activities') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.vouchers.index') }}" :active="request()->routeIs('admin.vouchers*')">
                        {{ __('Vouchers') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.merchants.index') }}" :active="request()->routeIs('admin.merchants*')">
                        {{ __('Merchants') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.point-system.index') }}" :active="request()->routeIs('admin.point-system*')">
                        {{ __('Point System') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.raffle') }}" :active="request()->routeIs('admin.raffle')">
                        {{ __('Raffle') }}
                    </x-responsive-nav-link>
                    {{-- <x-responsive-nav-link href="{{ route('admin.raffle.v2') }}" :active="request()->routeIs('admin.raffle.v2')">
                        {{ __('Raffle V2') }} --}}
                    {{-- </x-responsive-nav-link> --}}
                @elseif(auth()->user()->isMerchantUser())
                    <x-responsive-nav-link href="{{ route('merchant.dashboard') }}" :active="request()->routeIs('merchant.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('merchant.vouchers.index') }}" :active="request()->routeIs('merchant.vouchers*')">
                        {{ __('Vouchers') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
@else
    <!-- Member Mobile Bottom Navigation -->
    <nav
        x-cloak
        x-data="{
            settingsOpen: false,
            qrOpen: false,
            qrStep: 'menu', // menu | show | scan
            qrCodeModalOpen: false, // New direct QR code modal
            qrLoading: false,
            qrError: null,
            qrImage: null,
            qrValue: null,
            qrCodeModalLoading: false, // Loading state for new modal
            qrCodeModalError: null, // Error state for new modal
            qrCodeModalImage: null, // QR image for new modal
            qrCodeModalValue: null, // QR value for new modal
            scanError: null,
            scanResult: null,
            stream: null,
            detector: null,
            scanTimer: null,
            isMerchant: @js(auth()->check() && auth()->user()->isMerchantUser()),
            handleQrButtonClick() {
                this.settingsOpen = false;
                if (this.isMerchant) {
                    window.dispatchEvent(new CustomEvent('openQrScanner'));
                } else {
                    this.openQrCodeModal();
                }
            },
            async loadMyQrCode() {
                this.qrLoading = true;
                this.qrError = null;
                this.qrImage = null;
                this.qrValue = null;
                try {
                    const res = await fetch('{{ route('qr-code.full') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    this.qrImage = data?.image ?? null;
                    this.qrValue = data?.qr_code ?? null;
                    if (!this.qrImage) {
                        this.qrError = 'QR code is not available.';
                    }
                } catch (e) {
                    this.qrError = 'Failed to load your QR code.';
                } finally {
                    this.qrLoading = false;
                }
            },
            async startScan() {
                this.scanError = null;
                this.scanResult = null;

                if (!navigator.mediaDevices?.getUserMedia) {
                    this.scanError = 'Camera is not supported on this device/browser.';
                    return;
                }

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false
                    });
                    this.$refs.qrVideo.srcObject = this.stream;
                    await this.$refs.qrVideo.play();
                } catch (e) {
                    this.scanError = 'Camera permission denied or camera not available.';
                    return;
                }

                if (!('BarcodeDetector' in window)) {
                    this.scanError = 'QR scan is not supported on this browser. Please use Chrome on Android, or update your browser.';
                    return;
                }

                try {
                    this.detector = new BarcodeDetector({ formats: ['qr_code'] });
                } catch (e) {
                    this.scanError = 'Failed to initialize QR scanner.';
                    return;
                }

                // Poll detection a few times per second
                this.scanTimer = setInterval(async () => {
                    if (!this.detector || !this.$refs.qrVideo) return;
                    try {
                        const codes = await this.detector.detect(this.$refs.qrVideo);
                        if (codes && codes.length) {
                            this.scanResult = codes[0].rawValue || 'Scanned';
                            this.stopScan();
                        }
                    } catch (e) {
                        // Ignore transient detection errors
                    }
                }, 300);
            },
            stopScan() {
                if (this.scanTimer) {
                    clearInterval(this.scanTimer);
                    this.scanTimer = null;
                }
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                    this.stream = null;
                }
            },
            closeQrModal() {
                this.stopScan();
                this.qrOpen = false;
                this.qrStep = 'menu';
                this.qrError = null;
                this.scanError = null;
                this.scanResult = null;
            },
            async openQrCodeModal() {
                this.qrCodeModalOpen = true;
                this.qrCodeModalLoading = true;
                this.qrCodeModalError = null;
                this.qrCodeModalImage = null;
                this.qrCodeModalValue = null;
                try {
                    const res = await fetch('{{ route('qr-code.full') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    this.qrCodeModalImage = data?.image ?? null;
                    this.qrCodeModalValue = data?.qr_code ?? null;
                    if (!this.qrCodeModalImage) {
                        this.qrCodeModalError = 'QR code is not available.';
                    }
                } catch (e) {
                    this.qrCodeModalError = 'Failed to load your QR code.';
                } finally {
                    this.qrCodeModalLoading = false;
                }
            },
            closeQrCodeModal() {
                this.qrCodeModalOpen = false;
                this.qrCodeModalLoading = false;
                this.qrCodeModalError = null;
                this.qrCodeModalImage = null;
                this.qrCodeModalValue = null;
            }
        }"
        class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-[#3a5870] border-t border-orange-200"
    >
        <!-- Settings submenu -->
        <div
            x-show="settingsOpen"
            x-transition.opacity
            x-cloak
            @click.away="settingsOpen = false"
            class="absolute right-0 bottom-full w-1/2 mb-1 pl-4 pr-0"
            style="display: none;"
        >
            <div class="bg-white border border-gray-200 rounded-t-xl shadow-lg overflow-hidden">
                <a href="{{ route('profile.show') }}" class="flex items-center gap-1 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex gap-1 items-center text-left px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                        <svg class="size-6" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.75 9.874C11.75 10.2882 12.0858 10.624 12.5 10.624C12.9142 10.624 13.25 10.2882 13.25 9.874H11.75ZM13.25 4C13.25 3.58579 12.9142 3.25 12.5 3.25C12.0858 3.25 11.75 3.58579 11.75 4H13.25ZM9.81082 6.66156C10.1878 6.48991 10.3542 6.04515 10.1826 5.66818C10.0109 5.29121 9.56615 5.12478 9.18918 5.29644L9.81082 6.66156ZM5.5 12.16L4.7499 12.1561L4.75005 12.1687L5.5 12.16ZM12.5 19L12.5086 18.25C12.5029 18.25 12.4971 18.25 12.4914 18.25L12.5 19ZM19.5 12.16L20.2501 12.1687L20.25 12.1561L19.5 12.16ZM15.8108 5.29644C15.4338 5.12478 14.9891 5.29121 14.8174 5.66818C14.6458 6.04515 14.8122 6.48991 15.1892 6.66156L15.8108 5.29644ZM13.25 9.874V4H11.75V9.874H13.25ZM9.18918 5.29644C6.49843 6.52171 4.7655 9.19951 4.75001 12.1561L6.24999 12.1639C6.26242 9.79237 7.65246 7.6444 9.81082 6.66156L9.18918 5.29644ZM4.75005 12.1687C4.79935 16.4046 8.27278 19.7986 12.5086 19.75L12.4914 18.25C9.08384 18.2892 6.28961 15.5588 6.24995 12.1513L4.75005 12.1687ZM12.4914 19.75C16.7272 19.7986 20.2007 16.4046 20.2499 12.1687L18.7501 12.1513C18.7104 15.5588 15.9162 18.2892 12.5086 18.25L12.4914 19.75ZM20.25 12.1561C20.2345 9.19951 18.5016 6.52171 15.8108 5.29644L15.1892 6.66156C17.3475 7.6444 18.7376 9.79237 18.75 12.1639L20.25 12.1561Z" fill="#000000"></path> </g>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="relative">
            <div class="grid {{ auth()->check() && auth()->user()->isMerchantUser() ? 'grid-cols-3' : 'grid-cols-4' }}">
                <a href="{{ auth()->check() && auth()->user()->isMerchantUser() ? route('merchant.dashboard') : route('member.dashboard') }}" class="flex flex-col items-center justify-center py-3 gap-1 hover:bg-orange-500 {{ (auth()->check() && auth()->user()->isMerchantUser() && request()->routeIs('merchant.dashboard')) || (auth()->check() && auth()->user()->isMember() && (request()->routeIs('member.dashboard') || request()->routeIs('member.dashboard'))) ? 'text-white bg-orange-500' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V19.5A2.25 2.25 0 0 0 6.75 21.75h3.75v-4.5A2.25 2.25 0 0 1 12.75 15h-1.5A2.25 2.25 0 0 1 13.5 17.25v4.5h3.75A2.25 2.25 0 0 0 19.5 19.5V9.75" />
                    </svg>
                    <span class="text-[11px] font-semibold">Home</span>
                </a>

                @if(auth()->check() && auth()->user()->isMember())
                <a href="{{ route('member.events') }}" class="flex flex-col items-center justify-center py-3 pr-1 gap-1 hover:bg-orange-500 {{ request()->routeIs('member.events') || request()->routeIs('member.events*') ? 'text-white bg-orange-500' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75Zm3-9h.008v.008H6v-.008Zm3 0h.008v.008H9v-.008Zm3 0h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="text-[11px] font-semibold">Events</span>
                </a>
                @endif

                <a href="{{ auth()->check() && auth()->user()->isMerchantUser() ? route('merchant.vouchers.index') : route('member.vouchers') }}" class="flex flex-col items-center justify-center py-3 gap-1 hover:bg-orange-500 {{ (auth()->check() && auth()->user()->isMerchantUser() && request()->routeIs('merchant.vouchers*')) || (auth()->check() && auth()->user()->isMember() && request()->routeIs('member.vouchers')) ? 'text-white bg-orange-500' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h6m-6 3h6m-6 3h6m-6 3h6M6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25A2.25 2.25 0 0 1 6.75 3Z" />
                    </svg>
                    <span class="text-[11px] font-semibold">Vouchers</span>
                </a>

                <button
                    type="button"
                    @click="settingsOpen = !settingsOpen"
                    :class="settingsOpen ? 'text-white' : '{{ request()->routeIs('profile.show') ? 'text-white bg-orange-500' : 'text-slate-200' }}'"
                    aria-haspopup="menu"
                    :aria-expanded="settingsOpen.toString()"
                    aria-label="Open settings menu"
                    class="flex flex-col items-center justify-center py-3 gap-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span class="text-[11px] font-semibold">Settings</span>
                </button>
            </div>

            <!-- Floating center QR button -->
            @if(auth()->check() && auth()->user()->isMember())
                <button
                    type="button"
                    @click="handleQrButtonClick()"
                    aria-haspopup="dialog"
                    aria-label="Open QR Code"
                    class="absolute left-1/2 -top-7 -translate-x-1/2 z-50 w-16 h-16 rounded-full bg-[#3A5770]/60 text-white shadow-lg ring-4 ring-orange-500 flex items-center justify-center hover:bg-[#3A5770] active:scale-95 transition"
                >
                    {{-- Temporarily commented out the menu approach --}}
                    {{-- @click="settingsOpen = false; qrOpen = true; qrStep = 'menu'" --}}
                    <svg class="size-10" fill="#ffffff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.1666667,6 C16.0746192,6 16,6.07461921 16,6.16666667 L16,7.83333333 C16,7.92538079 16.0746192,8 16.1666667,8 L17.8333333,8 C17.9253808,8 18,7.92538079 18,7.83333333 L18,6.16666667 C18,6.07461921 17.9253808,6 17.8333333,6 L16.1666667,6 Z M16,18 L16,17.5 C16,17.2238576 16.2238576,17 16.5,17 C16.7761424,17 17,17.2238576 17,17.5 L17,18 L18,18 L18,17.5 C18,17.2238576 18.2238576,17 18.5,17 C18.7761424,17 19,17.2238576 19,17.5 L19,18.5 C19,18.7761424 18.7761424,19 18.5,19 L14.5,19 C14.2238576,19 14,18.7761424 14,18.5 L14,17.5 C14,17.2238576 14.2238576,17 14.5,17 C14.7761424,17 15,17.2238576 15,17.5 L15,18 L16,18 L16,18 Z M13,11 L13.5,11 C13.7761424,11 14,11.2238576 14,11.5 C14,11.7761424 13.7761424,12 13.5,12 L11.5,12 C11.2238576,12 11,11.7761424 11,11.5 C11,11.2238576 11.2238576,11 11.5,11 L12,11 L12,10 L10.5,10 C10.2238576,10 10,9.77614237 10,9.5 C10,9.22385763 10.2238576,9 10.5,9 L13.5,9 C13.7761424,9 14,9.22385763 14,9.5 C14,9.77614237 13.7761424,10 13.5,10 L13,10 L13,11 Z M18,12 L17.5,12 C17.2238576,12 17,11.7761424 17,11.5 C17,11.2238576 17.2238576,11 17.5,11 L18,11 L18,10.5 C18,10.2238576 18.2238576,10 18.5,10 C18.7761424,10 19,10.2238576 19,10.5 L19,12.5 C19,12.7761424 18.7761424,13 18.5,13 C18.2238576,13 18,12.7761424 18,12.5 L18,12 Z M13,14 L12.5,14 C12.2238576,14 12,13.7761424 12,13.5 C12,13.2238576 12.2238576,13 12.5,13 L13.5,13 C13.7761424,13 14,13.2238576 14,13.5 L14,15.5 C14,15.7761424 13.7761424,16 13.5,16 L10.5,16 C10.2238576,16 10,15.7761424 10,15.5 C10,15.2238576 10.2238576,15 10.5,15 L13,15 L13,14 L13,14 Z M16.1666667,5 L17.8333333,5 C18.4776655,5 19,5.52233446 19,6.16666667 L19,7.83333333 C19,8.47766554 18.4776655,9 17.8333333,9 L16.1666667,9 C15.5223345,9 15,8.47766554 15,7.83333333 L15,6.16666667 C15,5.52233446 15.5223345,5 16.1666667,5 Z M6.16666667,5 L7.83333333,5 C8.47766554,5 9,5.52233446 9,6.16666667 L9,7.83333333 C9,8.47766554 8.47766554,9 7.83333333,9 L6.16666667,9 C5.52233446,9 5,8.47766554 5,7.83333333 L5,6.16666667 C5,5.52233446 5.52233446,5 6.16666667,5 Z M6.16666667,6 C6.07461921,6 6,6.07461921 6,6.16666667 L6,7.83333333 C6,7.92538079 6.07461921,8 6.16666667,8 L7.83333333,8 C7.92538079,8 8,7.92538079 8,7.83333333 L8,6.16666667 C8,6.07461921 7.92538079,6 7.83333333,6 L6.16666667,6 Z M6.16666667,15 L7.83333333,15 C8.47766554,15 9,15.5223345 9,16.1666667 L9,17.8333333 C9,18.4776655 8.47766554,19 7.83333333,19 L6.16666667,19 C5.52233446,19 5,18.4776655 5,17.8333333 L5,16.1666667 C5,15.5223345 5.52233446,15 6.16666667,15 Z M6.16666667,16 C6.07461921,16 6,16.0746192 6,16.1666667 L6,17.8333333 C6,17.9253808 6.07461921,18 6.16666667,18 L7.83333333,18 C7.92538079,18 8,17.9253808 8,17.8333333 L8,16.1666667 C8,16.0746192 7.92538079,16 7.83333333,16 L6.16666667,16 Z M13,6 L10.5,6 C10.2238576,6 10,5.77614237 10,5.5 C10,5.22385763 10.2238576,5 10.5,5 L13.5,5 C13.7761424,5 14,5.22385763 14,5.5 L14,7.5 C14,7.77614237 13.7761424,8 13.5,8 C13.2238576,8 13,7.77614237 13,7.5 L13,6 Z M10.5,8 C10.2238576,8 10,7.77614237 10,7.5 C10,7.22385763 10.2238576,7 10.5,7 L11.5,7 C11.7761424,7 12,7.22385763 12,7.5 C12,7.77614237 11.7761424,8 11.5,8 L10.5,8 Z M5.5,14 C5.22385763,14 5,13.7761424 5,13.5 C5,13.2238576 5.22385763,13 5.5,13 L7.5,13 C7.77614237,13 8,13.2238576 8,13.5 C8,13.7761424 7.77614237,14 7.5,14 L5.5,14 Z M9.5,14 C9.22385763,14 9,13.7761424 9,13.5 C9,13.2238576 9.22385763,13 9.5,13 L10.5,13 C10.7761424,13 11,13.2238576 11,13.5 C11,13.7761424 10.7761424,14 10.5,14 L9.5,14 Z M11,18 L11,18.5 C11,18.7761424 10.7761424,19 10.5,19 C10.2238576,19 10,18.7761424 10,18.5 L10,17.5 C10,17.2238576 10.2238576,17 10.5,17 L12.5,17 C12.7761424,17 13,17.2238576 13,17.5 C13,17.7761424 12.7761424,18 12.5,18 L11,18 Z M9,11 L9.5,11 C9.77614237,11 10,11.2238576 10,11.5 C10,11.7761424 9.77614237,12 9.5,12 L8.5,12 C8.22385763,12 8,11.7761424 8,11.5 L8,11 L7.5,11 C7.22385763,11 7,10.7761424 7,10.5 C7,10.2238576 7.22385763,10 7.5,10 L8.5,10 C8.77614237,10 9,10.2238576 9,10.5 L9,11 Z M5,10.5 C5,10.2238576 5.22385763,10 5.5,10 C5.77614237,10 6,10.2238576 6,10.5 L6,11.5 C6,11.7761424 5.77614237,12 5.5,12 C5.22385763,12 5,11.7761424 5,11.5 L5,10.5 Z M15,10.5 C15,10.2238576 15.2238576,10 15.5,10 C15.7761424,10 16,10.2238576 16,10.5 L16,12.5 C16,12.7761424 15.7761424,13 15.5,13 C15.2238576,13 15,12.7761424 15,12.5 L15,10.5 Z M17,15 L17,14.5 C17,14.2238576 17.2238576,14 17.5,14 L18.5,14 C18.7761424,14 19,14.2238576 19,14.5 C19,14.7761424 18.7761424,15 18.5,15 L18,15 L18,15.5 C18,15.7761424 17.7761424,16 17.5,16 L15.5,16 C15.2238576,16 15,15.7761424 15,15.5 L15,14.5 C15,14.2238576 15.2238576,14 15.5,14 C15.7761424,14 16,14.2238576 16,14.5 L16,15 L17,15 Z M3,6.5 C3,6.77614237 2.77614237,7 2.5,7 C2.22385763,7 2,6.77614237 2,6.5 L2,4.5 C2,3.11928813 3.11928813,2 4.5,2 L6.5,2 C6.77614237,2 7,2.22385763 7,2.5 C7,2.77614237 6.77614237,3 6.5,3 L4.5,3 C3.67157288,3 3,3.67157288 3,4.5 L3,6.5 Z M17.5,3 C17.2238576,3 17,2.77614237 17,2.5 C17,2.22385763 17.2238576,2 17.5,2 L19.5,2 C20.8807119,2 22,3.11928813 22,4.5 L22,6.5 C22,6.77614237 21.7761424,7 21.5,7 C21.2238576,7 21,6.77614237 21,6.5 L21,4.5 C21,3.67157288 20.3284271,3 19.5,3 L17.5,3 Z M6.5,21 C6.77614237,21 7,21.2238576 7,21.5 C7,21.7761424 6.77614237,22 6.5,22 L4.5,22 C3.11928813,22 2,20.8807119 2,19.5 L2,17.5 C2,17.2238576 2.22385763,17 2.5,17 C2.77614237,17 3,17.2238576 3,17.5 L3,19.5 C3,20.3284271 3.67157288,21 4.5,21 L6.5,21 Z M21,17.5 C21,17.2238576 21.2238576,17 21.5,17 C21.7761424,17 22,17.2238576 22,17.5 L22,19.5 C22,20.8807119 20.8807119,22 19.5,22 L17.5,22 C17.2238576,22 17,21.7761424 17,21.5 C17,21.2238576 17.2238576,21 17.5,21 L19.5,21 C20.3284271,21 21,20.3284271 21,19.5 L21,17.5 Z"></path> </g>
                    </svg>
                </button>
            @else
                <button
                    type="button"
                    @click="handleQrButtonClick()"
                    aria-haspopup="dialog"
                    aria-label="Open QR Code"
                    class="absolute -right-4 -top-20 -translate-x-1/2 z-50 w-16 h-16 rounded-full bg-orange-500/60 text-white shadow-lg ring-4 ring-orange-500 flex items-center justify-center hover:bg-[#3A5770] active:scale-95 transition"
                >
                    {{-- Temporarily commented out the menu approach --}}
                    {{-- @click="settingsOpen = false; qrOpen = true; qrStep = 'menu'" --}}
                    <svg class="size-10" fill="#ffffff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.1666667,6 C16.0746192,6 16,6.07461921 16,6.16666667 L16,7.83333333 C16,7.92538079 16.0746192,8 16.1666667,8 L17.8333333,8 C17.9253808,8 18,7.92538079 18,7.83333333 L18,6.16666667 C18,6.07461921 17.9253808,6 17.8333333,6 L16.1666667,6 Z M16,18 L16,17.5 C16,17.2238576 16.2238576,17 16.5,17 C16.7761424,17 17,17.2238576 17,17.5 L17,18 L18,18 L18,17.5 C18,17.2238576 18.2238576,17 18.5,17 C18.7761424,17 19,17.2238576 19,17.5 L19,18.5 C19,18.7761424 18.7761424,19 18.5,19 L14.5,19 C14.2238576,19 14,18.7761424 14,18.5 L14,17.5 C14,17.2238576 14.2238576,17 14.5,17 C14.7761424,17 15,17.2238576 15,17.5 L15,18 L16,18 L16,18 Z M13,11 L13.5,11 C13.7761424,11 14,11.2238576 14,11.5 C14,11.7761424 13.7761424,12 13.5,12 L11.5,12 C11.2238576,12 11,11.7761424 11,11.5 C11,11.2238576 11.2238576,11 11.5,11 L12,11 L12,10 L10.5,10 C10.2238576,10 10,9.77614237 10,9.5 C10,9.22385763 10.2238576,9 10.5,9 L13.5,9 C13.7761424,9 14,9.22385763 14,9.5 C14,9.77614237 13.7761424,10 13.5,10 L13,10 L13,11 Z M18,12 L17.5,12 C17.2238576,12 17,11.7761424 17,11.5 C17,11.2238576 17.2238576,11 17.5,11 L18,11 L18,10.5 C18,10.2238576 18.2238576,10 18.5,10 C18.7761424,10 19,10.2238576 19,10.5 L19,12.5 C19,12.7761424 18.7761424,13 18.5,13 C18.2238576,13 18,12.7761424 18,12.5 L18,12 Z M13,14 L12.5,14 C12.2238576,14 12,13.7761424 12,13.5 C12,13.2238576 12.2238576,13 12.5,13 L13.5,13 C13.7761424,13 14,13.2238576 14,13.5 L14,15.5 C14,15.7761424 13.7761424,16 13.5,16 L10.5,16 C10.2238576,16 10,15.7761424 10,15.5 C10,15.2238576 10.2238576,15 10.5,15 L13,15 L13,14 L13,14 Z M16.1666667,5 L17.8333333,5 C18.4776655,5 19,5.52233446 19,6.16666667 L19,7.83333333 C19,8.47766554 18.4776655,9 17.8333333,9 L16.1666667,9 C15.5223345,9 15,8.47766554 15,7.83333333 L15,6.16666667 C15,5.52233446 15.5223345,5 16.1666667,5 Z M6.16666667,5 L7.83333333,5 C8.47766554,5 9,5.52233446 9,6.16666667 L9,7.83333333 C9,8.47766554 8.47766554,9 7.83333333,9 L6.16666667,9 C5.52233446,9 5,8.47766554 5,7.83333333 L5,6.16666667 C5,5.52233446 5.52233446,5 6.16666667,5 Z M6.16666667,6 C6.07461921,6 6,6.07461921 6,6.16666667 L6,7.83333333 C6,7.92538079 6.07461921,8 6.16666667,8 L7.83333333,8 C7.92538079,8 8,7.92538079 8,7.83333333 L8,6.16666667 C8,6.07461921 7.92538079,6 7.83333333,6 L6.16666667,6 Z M6.16666667,15 L7.83333333,15 C8.47766554,15 9,15.5223345 9,16.1666667 L9,17.8333333 C9,18.4776655 8.47766554,19 7.83333333,19 L6.16666667,19 C5.52233446,19 5,18.4776655 5,17.8333333 L5,16.1666667 C5,15.5223345 5.52233446,15 6.16666667,15 Z M6.16666667,16 C6.07461921,16 6,16.0746192 6,16.1666667 L6,17.8333333 C6,17.9253808 6.07461921,18 6.16666667,18 L7.83333333,18 C7.92538079,18 8,17.9253808 8,17.8333333 L8,16.1666667 C8,16.0746192 7.92538079,16 7.83333333,16 L6.16666667,16 Z M13,6 L10.5,6 C10.2238576,6 10,5.77614237 10,5.5 C10,5.22385763 10.2238576,5 10.5,5 L13.5,5 C13.7761424,5 14,5.22385763 14,5.5 L14,7.5 C14,7.77614237 13.7761424,8 13.5,8 C13.2238576,8 13,7.77614237 13,7.5 L13,6 Z M10.5,8 C10.2238576,8 10,7.77614237 10,7.5 C10,7.22385763 10.2238576,7 10.5,7 L11.5,7 C11.7761424,7 12,7.22385763 12,7.5 C12,7.77614237 11.7761424,8 11.5,8 L10.5,8 Z M5.5,14 C5.22385763,14 5,13.7761424 5,13.5 C5,13.2238576 5.22385763,13 5.5,13 L7.5,13 C7.77614237,13 8,13.2238576 8,13.5 C8,13.7761424 7.77614237,14 7.5,14 L5.5,14 Z M9.5,14 C9.22385763,14 9,13.7761424 9,13.5 C9,13.2238576 9.22385763,13 9.5,13 L10.5,13 C10.7761424,13 11,13.2238576 11,13.5 C11,13.7761424 10.7761424,14 10.5,14 L9.5,14 Z M11,18 L11,18.5 C11,18.7761424 10.7761424,19 10.5,19 C10.2238576,19 10,18.7761424 10,18.5 L10,17.5 C10,17.2238576 10.2238576,17 10.5,17 L12.5,17 C12.7761424,17 13,17.2238576 13,17.5 C13,17.7761424 12.7761424,18 12.5,18 L11,18 Z M9,11 L9.5,11 C9.77614237,11 10,11.2238576 10,11.5 C10,11.7761424 9.77614237,12 9.5,12 L8.5,12 C8.22385763,12 8,11.7761424 8,11.5 L8,11 L7.5,11 C7.22385763,11 7,10.7761424 7,10.5 C7,10.2238576 7.22385763,10 7.5,10 L8.5,10 C8.77614237,10 9,10.2238576 9,10.5 L9,11 Z M5,10.5 C5,10.2238576 5.22385763,10 5.5,10 C5.77614237,10 6,10.2238576 6,10.5 L6,11.5 C6,11.7761424 5.77614237,12 5.5,12 C5.22385763,12 5,11.7761424 5,11.5 L5,10.5 Z M15,10.5 C15,10.2238576 15.2238576,10 15.5,10 C15.7761424,10 16,10.2238576 16,10.5 L16,12.5 C16,12.7761424 15.7761424,13 15.5,13 C15.2238576,13 15,12.7761424 15,12.5 L15,10.5 Z M17,15 L17,14.5 C17,14.2238576 17.2238576,14 17.5,14 L18.5,14 C18.7761424,14 19,14.2238576 19,14.5 C19,14.7761424 18.7761424,15 18.5,15 L18,15 L18,15.5 C18,15.7761424 17.7761424,16 17.5,16 L15.5,16 C15.2238576,16 15,15.7761424 15,15.5 L15,14.5 C15,14.2238576 15.2238576,14 15.5,14 C15.7761424,14 16,14.2238576 16,14.5 L16,15 L17,15 Z M3,6.5 C3,6.77614237 2.77614237,7 2.5,7 C2.22385763,7 2,6.77614237 2,6.5 L2,4.5 C2,3.11928813 3.11928813,2 4.5,2 L6.5,2 C6.77614237,2 7,2.22385763 7,2.5 C7,2.77614237 6.77614237,3 6.5,3 L4.5,3 C3.67157288,3 3,3.67157288 3,4.5 L3,6.5 Z M17.5,3 C17.2238576,3 17,2.77614237 17,2.5 C17,2.22385763 17.2238576,2 17.5,2 L19.5,2 C20.8807119,2 22,3.11928813 22,4.5 L22,6.5 C22,6.77614237 21.7761424,7 21.5,7 C21.2238576,7 21,6.77614237 21,6.5 L21,4.5 C21,3.67157288 20.3284271,3 19.5,3 L17.5,3 Z M6.5,21 C6.77614237,21 7,21.2238576 7,21.5 C7,21.7761424 6.77614237,22 6.5,22 L4.5,22 C3.11928813,22 2,20.8807119 2,19.5 L2,17.5 C2,17.2238576 2.22385763,17 2.5,17 C2.77614237,17 3,17.2238576 3,17.5 L3,19.5 C3,20.3284271 3.67157288,21 4.5,21 L6.5,21 Z M21,17.5 C21,17.2238576 21.2238576,17 21.5,17 C21.7761424,17 22,17.2238576 22,17.5 L22,19.5 C22,20.8807119 20.8807119,22 19.5,22 L17.5,22 C17.2238576,22 17,21.7761424 17,21.5 C17,21.2238576 17.2238576,21 17.5,21 L19.5,21 C20.3284271,21 21,20.3284271 21,19.5 L21,17.5 Z"></path> </g>
                    </svg>
                </button>

                
            @endif
        </div>

        <!-- QR Menu Modal (temporarily commented out) -->
        {{-- <div
            x-show="qrOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[60] bg-black/60 flex items-end justify-center p-3"
            @keydown.escape.window="closeQrModal()"
            @click="closeQrModal()"
        >
            ... QR menu modal content ...
        </div> --}}

        <!-- New Direct QR Code Modal -->
        <div
            x-show="qrCodeModalOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[60] bg-black/60 flex items-center justify-center p-4"
            @keydown.escape.window="closeQrCodeModal()"
            @click="closeQrCodeModal()"
            style="display: none;"
        >
            <div class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl" @click.stop>
                <div class="flex items-center justify-end mb-4">
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-colors" @click="closeQrCodeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Loading State -->
                <template x-if="qrCodeModalLoading">
                    <div class="py-12 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <p class="mt-4 text-sm text-gray-500">Loading your QR code…</p>
                    </div>
                </template>

                <!-- Error State -->
                <template x-if="qrCodeModalError && !qrCodeModalLoading">
                    <div class="py-8 text-center">
                        <div class="text-red-600 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-red-600 font-semibold" x-text="qrCodeModalError"></p>
                    </div>
                </template>

                <!-- QR Code Display -->
                <template x-if="qrCodeModalImage && !qrCodeModalLoading">
                    <div class="flex flex-col items-center gap-4">
                        <h3 class="text-lg font-bold text-gray-800 text-center" x-text="qrCodeModalValue ?? ''"></h3>
                        <p class="text-xs text-gray-500 text-center px-4">
                            Show this QR code to be scanned at locations or events
                        </p>
                        <div class="bg-white p-4 rounded-xl border-2 border-gray-200 shadow-sm">
                            <img :src="qrCodeModalImage" alt="My QR code" class="w-64 h-64 object-contain">
                        </div>

                        <button class="bg-blue-500 hover:bg-blue-600 border border-blue-700 transition-all duration-300 hover:scale-105 text-white px-4 py-3 rounded-md w-1/2 mx-auto" @click="$dispatch('openQrScanner')">Scan QR a Code</button>
                    </div>
                </template>
            </div>
        </div>
    </nav>
@endif

