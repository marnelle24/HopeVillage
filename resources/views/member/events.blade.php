<x-app-layout>
    <x-slot name="header">
        <x-member-points-header />
    </x-slot>

    <div class="py-10 md:px-0 px-4">
        @php
            $type = request()->query('type');
            $initialTab = $type === 'my-events' ? 'mine' : 'upcoming';
        @endphp

        <div
            class="max-w-7xl mx-auto sm:px-6 lg:px-8"
            x-cloak
            x-data="{
                tab: '{{ $initialTab }}',
                syncUrl() {
                    const params = new URLSearchParams(window.location.search);
                    params.set('type', this.tab === 'mine' ? 'my-events' : 'upcoming');
                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.replaceState({}, '', newUrl);
                },
                setTab(next) {
                    this.tab = next;
                    this.syncUrl();
                },
                init() {
                    const type = new URLSearchParams(window.location.search).get('type');
                    if (type === 'my-events') this.tab = 'mine';
                    if (type === 'upcoming') this.tab = 'upcoming';
                    this.syncUrl();
                },
            }"
            x-init="init()"
        >
            <!-- Tabs -->
            <div class="mb-6 flex items-center justify-center">
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-slate-400">
                    <button
                        type="button"
                        @click="setTab('upcoming')"
                        :class="tab === 'upcoming' ? 'bg-orange-500 shadow text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                    >
                        Upcoming Events
                    </button>
                    <button
                        type="button"
                        @click="setTab('mine')"
                        :class="tab === 'mine' ? 'bg-orange-500 shadow text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                    >
                        My Events
                    </button>
                </div>
            </div>

            <!-- Upcoming -->
            <div x-show="tab === 'upcoming'" x-cloak>
                @livewire('member.events.browse', key('member-events-browse-page'))
            </div>

            <!-- My Events -->
            <div x-show="tab === 'mine'" x-cloak>
                @livewire('member.events.my-events', key('member-events-my-events-page'))
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('scroll-to-top', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</x-app-layout>


