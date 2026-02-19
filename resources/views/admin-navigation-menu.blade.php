    <nav
        x-data="{
            sidebarOpen: false,
            showNavTour: false,
            scrolledPast20: false,
            init() {
                if (typeof localStorage !== 'undefined' && localStorage.getItem('hopevillage_admin_nav_tour_seen') !== '1') {
                    this.showNavTour = true;
                }
                const checkScroll = () => {
                    const scrollable = document.documentElement.scrollHeight - window.innerHeight;
                    this.scrolledPast20 = scrollable > 0 && window.scrollY >= scrollable * 0.2;
                };
                checkScroll();
                window.addEventListener('scroll', checkScroll, { passive: true });
                return () => window.removeEventListener('scroll', checkScroll);
            },
            dismissNavTour() {
                if (typeof localStorage !== 'undefined') localStorage.setItem('hopevillage_admin_nav_tour_seen', '1');
                this.showNavTour = false;
                this.$nextTick(() => this.$refs.menuButton?.focus());
            }
        }"
        x-cloak
        @keydown.escape.window="if (showNavTour) dismissNavTour()"
        class="border-b border-gray-100 fixed top-0 left-0 right-0 z-50 transition-shadow shadow-sm duration-300 backdrop-blur-sm bg-gray-100/60"
        :class="scrolledPast20 ? 'shadow-lg bg-gray-100/60' : 'bg-gray-100'"
    >
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-22">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-12">
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown + Hamburger (last menu elements) -->
                <div class="flex items-center sm:ms-6 gap-2">
                    <div class="hidden sm:block">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div class="text-14px">{{ Auth::user()->name }}</div>
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

                                <x-dropdown-link href="{{ route('admin.settings.index') }}">
                                    {{ __('Settings') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="{{ route('admin.api-documentation.index') }}">
                                    {{ __('API Documentation') }}
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

                    <!-- Tour callout: points to hamburger (first-time admin) -->
                    <div class="relative flex items-center">
                        <div
                            x-show="showNavTour"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-full mr-2 top-[75px] -translate-y-1/2 w-64 max-w-[calc(100vw-8rem)] rounded-lg bg-white p-3 shadow-lg ring-1 ring-gray-200"
                            style="display: none;"
                            role="status"
                            aria-live="polite"
                        >
                            <p class="font-semibold text-gray-900 text-sm">{{ __('New Navigation Menu') }}</p>
                            <p class="mt-1 text-gray-600 text-sm">
                                {{ __('We have updated the navigation menu to make it easier to access the different sections of the platform.') }}
                            </p>
                            <button
                                type="button"
                                @click="dismissNavTour()"
                                class="mt-3 w-full rounded-md bg-orange-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                            >
                                {{ __('Got it') }}
                            </button>
                            <!-- Arrow pointing right at hamburger -->
                            <div class="absolute -right-2 top-4 h-0 w-0 -translate-y-1/2 border-y-8 border-l-8 border-y-transparent border-l-white" style="filter: drop-shadow(1px 0 0 rgb(229 231 235));"></div>
                        </div>

                        <!-- Hamburger: opens sidebar from the right -->
                        <button
                            x-ref="menuButton"
                            type="button"
                            @click="sidebarOpen = true; dismissNavTour()"
                            class="inline-flex ml-1 items-center justify-center p-1 rounded-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-300 transition duration-150 ease-in-out"
                            aria-label="{{ __('Open menu') }}"
                        >
                            <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <path d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                    {{-- Tour overlay: dark background, hidden when user presses Got it (teleported to body so it covers full viewport) --}}
                    <template x-teleport="body">
                        <div
                            x-show="showNavTour"
                            x-cloak
                            class="absolute inset-0 bg-black/50 z-40"
                            aria-hidden="true"
                            style="display: none;"
                        ></div>
                    </template>
                </div>
            </div>
        </div>

        <template x-teleport="body">
            <!-- Single wrapper so Alpine scope applies; overlay + sidebar inside -->
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak
                class="fixed inset-0 z-[100]"
                style="display: none;"
                aria-hidden="true"
            >
                <!-- Backdrop -->
                <div
                    @click="sidebarOpen = false"
                    class="absolute inset-0"
                    style="background-color: rgba(0, 0, 0, 0.5);"
                ></div>

                <!-- Sidebar panel (slide from right; always visible when wrapper is open) -->
                <div
                    class="fixed top-0 right-0 h-full w-80 max-w-[85vw] bg-white shadow-xl overflow-y-auto z-[101] transform transition-transform duration-300 ease-out"
                    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
                    role="dialog"
                    aria-label="{{ __('Admin navigation') }}"
                >
            <div class="flex flex-col h-full">
                <!-- Sidebar header -->
                <div class="flex items-center justify-between shrink-0 h-16 px-4 border-b border-gray-200 bg-white">
                    <span class="font-semibold text-gray-800">{{ __('Admin Portal') }}</span>
                    <button
                        type="button"
                        @click="sidebarOpen = false"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        aria-label="{{ __('Close menu') }}"
                    >
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar nav links -->
                <nav class="flex-1 py-4 px-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard') || request()->routeIs('admin.dashboard.v2')">
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
                        {{ __('Activities') }}
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
                        {{ __('Lucky Draw') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.news.index') }}" :active="request()->routeIs('admin.news*')">
                        {{ __('News') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.news-categories.index') }}" :active="request()->routeIs('admin.news-categories*')">
                        {{ __('News Categories') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('admin.announcements.index') }}" :active="request()->routeIs('admin.announcements*')">
                        {{ __('Announcements') }}
                    </x-responsive-nav-link>
                </nav>

                <!-- Sidebar footer (user & settings) -->
                <div class="shrink-0 pt-4 pb-6 px-4 border-t border-gray-200">
                    <div class="px-3 mb-3">
                        <div class="font-medium text-sm text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="space-y-1">
                        <x-responsive-nav-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.settings.index') }}">
                            {{ __('Settings') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.api-documentation.index') }}">
                            {{ __('API Documentation') }}
                        </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </template>
    </nav>