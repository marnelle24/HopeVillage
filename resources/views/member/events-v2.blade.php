<x-app-layout>
    <x-slot name="header">
        <x-member-points-header />
    </x-slot>

    <div class="min-h-screen bg-base-200">
        <div
            class="max-w-7xl mx-auto"
            x-cloak
            x-data="{
                tab: 'upcoming',
                filter: 'all', // 'all' or 'upcoming'
                isLoading: true,
                syncUrl() {
                    const params = new URLSearchParams(window.location.search);
                    if (this.tab === 'upcoming') {
                        params.set('filter', this.filter);
                    } else {
                        params.delete('filter');
                    }
                    const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                    window.history.replaceState({}, '', newUrl);
                },
                setTab(next) {
                    this.tab = next;
                    this.syncUrl();
                    // Reset filter when switching tabs
                    if (next === 'upcoming') {
                        this.filter = 'all';
                    }
                    // Show loading animation when switching to My Events tab
                    if (next === 'mine') {
                        this.isLoading = true;
                        setTimeout(() => {
                            this.isLoading = false;
                        }, 500);
                    }
                },
                setFilter(f) {
                    this.filter = f;
                    this.syncUrl();
                    // Show loading animation
                    this.isLoading = true;
                    // Hide content immediately
                    // Reload to remount Livewire component with new filter after a short delay
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?filter=' + f;
                    }, 300);
                },
                init() {
                    const filterParam = new URLSearchParams(window.location.search).get('filter');
                    if (filterParam) this.filter = filterParam;
                    // If filter exists, we're on upcoming tab, otherwise default to upcoming
                    this.tab = 'upcoming';
                    this.syncUrl();
                    // Show loading animation with delay before displaying content
                    this.isLoading = true;
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                },
            }"
            x-init="init()"
        >
            
            <!-- Content Section -->
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div 
                    x-data="{ 
                        activeCategory: tab === 'mine' ? 'my-events' : 'upcoming',
                        init() {
                            this.$watch('tab', (value) => {
                                this.activeCategory = value === 'mine' ? 'my-events' : 'upcoming';
                            });
                        },
                        isActive(category) {
                            if (category === 'my-events') {
                                return this.activeCategory === 'my-events' && tab === 'mine';
                            }
                            return this.activeCategory === category && tab === 'upcoming';
                        }
                    }"
                    class="flex items-center gap-1 overflow-x-auto pb-2 scrollbar-hide mb-8"
                >
                    <button
                        type="button"
                        @click="activeCategory = 'all'; tab = 'upcoming'; setTab('upcoming'); setFilter('all')"
                        :class="tab === 'upcoming' && filter === 'all' ? 'bg-orange-500 shadow-md text-white' : 'border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white'"
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 flex items-center gap-2"
                    >
                        <span>All Events</span>
                    </button>
                    <button
                        type="button"
                        @click="activeCategory = 'upcoming'; tab = 'upcoming'; setTab('upcoming'); setFilter('upcoming')"
                        :class="tab === 'upcoming' && filter === 'upcoming' ? 'bg-orange-500 shadow-md text-white' : 'border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white'"
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Upcoming</span>
                    </button>
                    <button
                        type="button"
                        @click="activeCategory = 'my-events'; tab = 'mine'; setTab('mine')"
                        :class="isActive('my-events') ? 'bg-orange-500 shadow-md text-white' : 'border border-orange-500 text-orange-500 hover:bg-orange-500'"
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        <span>My Events</span>
                    </button>
                </div>
                
                <!-- Loading Animation -->
                <div 
                    x-show="isLoading"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-out duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-base-200/10"
                >
                    <div class="text-center">
                        <span class="loading loading-bars loading-xl text-orange-500"></span>
                        <p class="mt-4 text-gray-600 font-medium animate-pulse">Loading events...</p>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div x-show="tab === 'upcoming' && !isLoading" x-cloak>
                    <div wire:ignore.self>
                        @php
                            $currentFilter = request()->query('filter', 'all');
                        @endphp
                        @livewire(\App\Livewire\Member\EventsV2\Browse::class, ['filter' => $currentFilter], key('member-events-v2-browse-page-' . $currentFilter))
                    </div>
                </div>

                <!-- My Events -->
                <div x-show="tab === 'mine' && !isLoading" x-cloak>
                    @livewire(\App\Livewire\Member\EventsV2\MyEvents::class, key('member-events-v2-my-events-page'))
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

