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
                        <x-application-mark class="block h-9 w-auto" />
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
                        <a href="{{ route('member.dashboard') }}#my-events" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                            {{ __('My Events') }}
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
                <x-responsive-nav-link href="{{ route('member.dashboard') }}#my-events">
                    {{ __('My Events') }}
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

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif
</nav>
@else
    <!-- Member Mobile Bottom Navigation -->
    <nav x-data="{ settingsOpen: false }" class="sm:hidden fixed inset-x-0 bottom-0 z-50 bg-white border-t border-gray-200">
        <!-- Settings submenu -->
        <div
            x-show="settingsOpen"
            x-transition.opacity
            @click.away="settingsOpen = false"
            class="absolute inset-x-0 bottom-full mb-2 px-4"
            style="display: none;"
        >
            <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                <a href="{{ route('profile.show') }}" class="block px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-4">
            <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center justify-center py-3 gap-1 {{ request()->routeIs('member.dashboard') ? 'text-indigo-600' : 'text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V19.5A2.25 2.25 0 0 0 6.75 21.75h3.75v-4.5A2.25 2.25 0 0 1 12.75 15h-1.5A2.25 2.25 0 0 1 13.5 17.25v4.5h3.75A2.25 2.25 0 0 0 19.5 19.5V9.75" />
                </svg>
                <span class="text-[11px] font-semibold">Home</span>
            </a>

            <a href="{{ route('member.dashboard') }}#my-vouchers" class="flex flex-col items-center justify-center py-3 gap-1 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75h6m-6 3h6m-6 3h6m-6 3h6M6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25A2.25 2.25 0 0 1 6.75 3Z" />
                </svg>
                <span class="text-[11px] font-semibold">My Vouchers</span>
            </a>

            <a href="{{ route('member.dashboard') }}#my-events" class="flex flex-col items-center justify-center py-3 gap-1 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75Zm3-9h.008v.008H6v-.008Zm3 0h.008v.008H9v-.008Zm3 0h.008v.008H12v-.008Z" />
                </svg>
                <span class="text-[11px] font-semibold">My Events</span>
            </a>

            <button
                type="button"
                @click="settingsOpen = !settingsOpen"
                :class="settingsOpen ? 'text-indigo-600' : '{{ request()->routeIs('profile.show') ? 'text-indigo-600' : 'text-gray-600' }}'"
                aria-haspopup="menu"
                :aria-expanded="settingsOpen.toString()"
                aria-label="Open settings menu"
                class="flex flex-col items-center justify-center py-3 gap-1"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span class="text-[11px] font-semibold">Settings</span>
            </button>
        </div>
    </nav>
@endif
