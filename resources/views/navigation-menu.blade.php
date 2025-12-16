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

@if(auth()->check() && !auth()->user()->isMember())
    <nav x-data="{ open: false }" class="{{ $navBgClass }} border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            {{-- <x-application-mark class="block h-9 w-auto" /> --}}
                            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-16">
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    @if(auth()->user()->isAdmin())
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('admin.locations.index') }}" :active="request()->routeIs('admin.locations.*')">
                                {{ __('Locations') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('admin.events.index') }}" :active="request()->routeIs('admin.events.*')">
                                {{ __('Events') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('admin.raffle') }}" :active="request()->routeIs('admin.raffle')">
                                {{ __('Raffle Draw') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('admin.amenities.index') }}" :active="request()->routeIs('admin.amenities.*')">
                                {{ __('Amenities') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('admin.merchants.index') }}" :active="request()->routeIs('admin.merchants.*')">
                                {{ __('Merchants') }}
                            </x-nav-link>
                        </div>
                    @elseif(auth()->user()->isMerchantUser())
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('merchant.vouchers.index') }}" :active="request()->routeIs('merchant.vouchers.*')">
                                {{ __('My Vouchers') }}
                            </x-nav-link>
                        </div>
                    @else
                        <!-- Member -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link href="{{ route('member.dashboard') }}" :active="request()->routeIs('member.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('member.dashboard') }}#my-vouchers" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                                {{ __('My Vouchers') }}
                            </a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('member.events') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                                {{ __('Events') }}
                            </a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('member.dashboard') }}#my-activities" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                                {{ __('My Recent Activities') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <!-- Merchant Switcher for Merchant Users -->
                    @if(auth()->check() && auth()->user()->isMerchantUser() && auth()->user()->merchants->count() > 1)
                        @livewire('merchant.merchant-switcher-dropdown', key('merchant-switcher-nav'))
                    @endif

                    <!-- Teams Dropdown -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ Auth::user()->currentTeam->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="w-60">
                                        <!-- Team Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Manage Team') }}
                                        </div>

                                        <!-- Team Settings -->
                                        <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                            {{ __('Team Settings') }}
                                        </x-dropdown-link>

                                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                            <x-dropdown-link href="{{ route('teams.create') }}">
                                                {{ __('Create New Team') }}
                                            </x-dropdown-link>
                                        @endcan

                                        <!-- Team Switcher -->
                                        @if (Auth::user()->allTeams()->count() > 1)
                                            <div class="border-t border-gray-200"></div>

                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Switch Teams') }}
                                            </div>

                                            @foreach (Auth::user()->allTeams() as $team)
                                                <x-switchable-team :team="$team" />
                                            @endforeach
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-gray-600 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                        {{ __('API Tokens') }}
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-200"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}"
                                            @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                @if(!auth()->user()->isMember())
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        @if(!auth()->user()->isMember())
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.locations.index') }}" :active="request()->routeIs('admin.locations.*')">
                        {{ __('Locations') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.events.index') }}" :active="request()->routeIs('admin.events.*')">
                        {{ __('Events') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.raffle') }}" :active="request()->routeIs('admin.raffle')">
                        {{ __('Raffle Draw') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.amenities.index') }}" :active="request()->routeIs('admin.amenities.*')">
                        {{ __('Amenities') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.merchants.index') }}" :active="request()->routeIs('admin.merchants.*')">
                        {{ __('Merchants') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->isMerchantUser())
                    <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('merchant.vouchers.index') }}" :active="request()->routeIs('merchant.vouchers.*')">
                        {{ __('My Vouchers') }}
                    </x-responsive-nav-link>
                @else
                    <!-- Member -->
                    <x-responsive-nav-link href="{{ route('member.dashboard') }}" :active="request()->routeIs('member.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('member.dashboard') }}#my-vouchers">
                        {{ __('My Vouchers') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('member.events') }}" :active="request()->routeIs('member.events')">
                        {{ __('Events') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('member.dashboard') }}#my-activities">
                        {{ __('My Recent Activities') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                            {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}"
                                    @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </nav>
@else
    <!-- Member Mobile Bottom Navigation -->
    <nav
        x-data="{
            settingsOpen: false,
            qrOpen: false,
            qrStep: 'menu', // menu | show | scan
            qrLoading: false,
            qrError: null,
            qrImage: null,
            qrValue: null,
            scanError: null,
            scanResult: null,
            stream: null,
            detector: null,
            scanTimer: null,
            async loadMyQrCode() {
                this.qrLoading = true;
                this.qrError = null;
                this.qrImage = null;
                this.qrValue = null;
                try {
                    const res = await fetch('{{ route("member.qr-code.full") }}', {
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
            }
        }"
        class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-slate-600 border-t border-gray-200"
    >
        <!-- Settings submenu -->
        <div
            x-show="settingsOpen"
            x-transition.opacity
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
            <div class="grid grid-cols-4">
                <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center justify-center py-3 gap-1 hover:bg-slate-700 {{ request()->routeIs('member.dashboard') ? 'text-white bg-slate-700' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V19.5A2.25 2.25 0 0 0 6.75 21.75h3.75v-4.5A2.25 2.25 0 0 1 12.75 15h-1.5A2.25 2.25 0 0 1 13.5 17.25v4.5h3.75A2.25 2.25 0 0 0 19.5 19.5V9.75" />
                    </svg>
                    <span class="text-[11px] font-semibold">Home</span>
                </a>

                <a href="{{ route('member.events') }}" class="flex flex-col items-center justify-center py-3 pr-4 gap-1 hover:bg-slate-700 {{ request()->routeIs('member.events') ? 'text-white bg-slate-700' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75Zm3-9h.008v.008H6v-.008Zm3 0h.008v.008H9v-.008Zm3 0h.008v.008H12v-.008Z" />
                    </svg>
                    <span class="text-[11px] font-semibold">Events</span>
                </a>


                <a href="{{ route('member.dashboard') }}#my-vouchers" class="flex flex-col items-center justify-center py-3 pl-5 gap-1 hover:bg-slate-700 {{ request()->routeIs('member.my-vouchers') ? 'text-white bg-slate-700' : 'text-slate-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h6m-6 3h6m-6 3h6m-6 3h6M6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25A2.25 2.25 0 0 1 6.75 3Z" />
                    </svg>
                    <span class="text-[11px] font-semibold">Vouchers</span>
                </a>

                <button
                    type="button"
                    @click="settingsOpen = !settingsOpen"
                    :class="settingsOpen ? 'text-white' : '{{ request()->routeIs('profile.show') ? 'text-white bg-slate-700' : 'text-slate-200' }}'"
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
            <button
                type="button"
                @click="settingsOpen = false; qrOpen = true; qrStep = 'menu'"
                aria-haspopup="dialog"
                :aria-expanded="qrOpen.toString()"
                aria-label="Open QR menu"
                class="absolute left-1/2 -top-7 -translate-x-1/2 z-50 w-16 h-16 rounded-full bg-slate-400/80 text-white shadow-lg ring-4 ring-slate-600 flex items-center justify-center hover:bg-orange-500 active:scale-95 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 2h2m-2 4h6v-6h-6m0 0h2m-2 2h2" />
                </svg>
            </button>
        </div>

        <!-- QR Menu Modal -->
        <div
            x-show="qrOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[60] bg-black/60 flex items-end justify-center p-3"
            @keydown.escape.window="closeQrModal()"
            @click="closeQrModal()"
        >
            <div class="w-full max-w-md bg-white rounded-t-2xl p-4" @click.stop>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-gray-800">QR Code</p>
                    <button type="button" class="text-gray-500 hover:text-gray-700" @click="closeQrModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Step: menu -->
                <div x-show="qrStep === 'menu'">
                    <div class="grid grid-cols-1 gap-2">
                        <button
                            type="button"
                            class="w-full text-left px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50"
                            @click="qrStep = 'show'; loadMyQrCode()"
                        >
                            <p class="text-sm font-semibold text-gray-800">Show my QR Code</p>
                            <p class="text-xs text-gray-500">Display your member QR code to be scanned.</p>
                        </button>

                        <button
                            type="button"
                            class="w-full text-left px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50"
                            @click="qrStep = 'scan'; startScan()"
                        >
                            <p class="text-sm font-semibold text-gray-800">Scan a QR Code</p>
                            <p class="text-xs text-gray-500">Use your camera to scan a QR code.</p>
                        </button>
                    </div>
                </div>

                <!-- Step: show -->
                <div x-show="qrStep === 'show'" x-cloak>
                    <button type="button" class="text-xs font-semibold text-indigo-600 mb-3" @click="qrStep = 'menu'">
                        ← Back
                    </button>

                    <template x-if="qrLoading">
                        <div class="py-8 text-center text-sm text-gray-500">Loading your QR code…</div>
                    </template>

                    <template x-if="qrError">
                        <div class="py-4 text-sm text-red-600" x-text="qrError"></div>
                    </template>

                    <template x-if="qrImage">
                        <div class="flex flex-col items-center gap-3">
                            <div class="bg-white p-3 rounded-xl border border-gray-200">
                                <img :src="qrImage" alt="My QR code" class="w-56 h-56">
                            </div>
                            <p class="text-[11px] text-gray-500 font-mono" x-text="qrValue ?? ''"></p>
                            <a href="{{ route('member.dashboard') }}" class="text-xs text-gray-500 underline">Open dashboard QR view</a>
                        </div>
                    </template>
                </div>

                <!-- Step: scan -->
                <div x-show="qrStep === 'scan'" x-cloak>
                    <div class="flex items-center justify-between mb-3">
                        <button type="button" class="text-xs font-semibold text-indigo-600" @click="stopScan(); qrStep = 'menu'">
                            ← Back
                        </button>
                        <button type="button" class="text-xs font-semibold text-gray-600" @click="stopScan(); startScan()">
                            Restart
                        </button>
                    </div>

                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-black">
                        <video x-ref="qrVideo" class="w-full h-56 object-cover" playsinline></video>
                    </div>

                    <template x-if="scanError">
                        <div class="mt-3 text-sm text-red-600" x-text="scanError"></div>
                    </template>

                    <template x-if="scanResult">
                        <div class="mt-3 p-3 rounded-xl border border-green-200 bg-green-50">
                            <p class="text-xs font-semibold text-green-800">Scanned</p>
                            <p class="text-sm text-gray-800 break-all mt-1" x-text="scanResult"></p>
                            <div class="mt-2 flex gap-2">
                                <button
                                    type="button"
                                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-gray-900 text-white"
                                    @click="navigator.clipboard?.writeText(scanResult || '')"
                                >
                                    Copy
                                </button>
                                <button
                                    type="button"
                                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-gray-300 text-gray-700"
                                    @click="closeQrModal()"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </template>

                    <p class="mt-3 text-[11px] text-gray-500">
                        Tip: if the camera doesn’t open, check browser permissions and make sure you’re on HTTPS.
                    </p>
                </div>
            </div>
        </div>
    </nav>
@endif
