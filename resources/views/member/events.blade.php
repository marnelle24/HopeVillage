<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Events') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ tab: 'upcoming' }">
            <!-- Tabs -->
            <div class="mb-6 flex items-center justify-center">
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-slate-400">
                    <button
                        type="button"
                        @click="tab = 'upcoming'"
                        :class="tab === 'upcoming' ? 'bg-orange-500 shadow text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                    >
                        Upcoming Events
                    </button>
                    <button
                        type="button"
                        @click="tab = 'mine'"
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


