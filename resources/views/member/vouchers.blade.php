<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-16">
            <div>
                <span class="font-semibold text-gray-900 text-xs">My Points</span>
                <button type="button" title="My Points" class="flex items-center gap-1 text-xs text-gray-500 border border-gray-300 rounded-lg px-2 py-1">
                    <svg class="size-4" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css">  .st0{fill:#000000;}  </style> <g> <path class="st0" d="M336.563,225.156c67.797,0,122.938,55.156,122.938,122.938s-55.141,122.938-122.938,122.938 c-67.766,0-122.922-55.156-122.922-122.938S268.797,225.156,336.563,225.156 M336.563,184.188 c-90.5,0-163.891,73.375-163.891,163.906S246.063,512,336.563,512c90.516,0,163.906-73.375,163.906-163.906 S427.078,184.188,336.563,184.188z"></path> <path class="st0" d="M341.922,293.406h-34.891c-5.578,0-10.078,4.5-10.078,10.063v102.875c0,5.563,4.5,10.094,10.078,10.094h5.063 c5.563,0,10.078-4.531,10.078-10.094v-28.953h19.75c26.422,0,47.922-18.828,47.922-42 C389.844,312.25,368.344,293.406,341.922,293.406z M341.922,355.281h-19.75V315.5h19.75c12.516,0,22.688,8.938,22.688,19.891 S354.438,355.281,341.922,355.281z"></path> <path class="st0" d="M144.391,0C71,0,11.531,28.969,11.531,64.719v34.563C11.531,135.031,71,164,144.391,164 c73.375,0,132.859-28.969,132.859-64.719V64.719C277.25,28.969,217.766,0,144.391,0z M144.391,26.875 c64.703,0,105.969,24.844,105.969,37.844s-41.266,37.844-105.969,37.844S38.422,77.719,38.422,64.719S79.688,26.875,144.391,26.875 z"></path> <path class="st0" d="M144.375,216c24.578,0,47.594-3.281,67.359-8.938c-19.016,16.719-34.578,37.375-45.563,60.563 c-7.063,0.563-14.344,0.906-21.797,0.906c-73.359,0-132.844-29.016-132.844-64.75v-40.625c0-0.656,0.484-1.25,1.141-1.313 c0.328-0.094,0.656,0.063,0.906,0.406c-0.156,0,0,0.469,0.891,2.688C27.344,194.125,80.609,216,144.375,216z"></path> <path class="st0" d="M148.063,348.094c0,7.969,0.484,15.75,1.547,23.438c-1.719,0.094-3.438,0.094-5.234,0.094 c-73.359,0-132.844-28.938-132.844-64.656v-40.75c0-0.563,0.484-1.125,1.141-1.219c0.531-0.063,0.891,0.25,1.125,0.625 c-0.297-0.406-0.641-0.625,0.672,2.5c12.875,29.156,66.141,51.063,129.906,51.063c1.969,0,4.016,0,5.969-0.188 C148.797,328.516,148.063,338.188,148.063,348.094z"></path> <path class="st0" d="M193.203,470.281c-15.078,2.969-31.547,4.5-48.828,4.5c-73.359,0-132.844-28.906-132.844-64.719v-40.656 c0-0.656,0.484-1.156,1.141-1.219c0.5-0.094,0.984,0.156,1.141,0.656c-0.313-0.406-0.734-0.813,0.656,2.375 c12.875,29.25,66.141,51.125,129.906,51.125c6.313,0,12.609-0.25,18.672-0.656C170.594,439.469,180.844,455.875,193.203,470.281z"></path> </g> </g>
                    </svg>                
                    <span class="font-semibold text-gray-900 text-sm">{{ auth()->user()->total_points ? number_format(auth()->user()->total_points, 2) : '0 point' }}</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 md:px-0 px-4">
        @php
            $status = request()->query('status');
            $initialTab = $status === 'my-vouchers' ? 'mine' : 'active';
        @endphp

        <div
            class="max-w-7xl mx-auto sm:px-6 lg:px-8"
            x-data="{
                tab: '{{ $initialTab }}',
                startX: null,
                syncUrl() {
                    const params = new URLSearchParams(window.location.search);
                    params.set('status', this.tab === 'mine' ? 'my-vouchers' : 'activeVouchers');
                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.replaceState({}, '', newUrl);
                },
                setTab(next) {
                    this.tab = next;
                    this.syncUrl();
                },
                onTouchStart(e) { this.startX = e.touches?.[0]?.clientX ?? null },
                onTouchEnd(e) {
                    if (this.startX === null) return;
                    const endX = e.changedTouches?.[0]?.clientX ?? null;
                    if (endX === null) return;
                    const dx = endX - this.startX;
                    if (dx > 60) this.setTab('mine');      // swipe right -> claimed/redeemed
                    if (dx < -60) this.setTab('active');   // swipe left -> active/claimable
                    this.startX = null;
                },
            }"
            x-init="(() => {
                const status = new URLSearchParams(window.location.search).get('status');
                if (status === 'my-vouchers') tab = 'mine';
                if (status === 'activeVouchers') tab = 'active';
                syncUrl();
            })()"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
        >
            <!-- Tabs (same style as Events) -->
            <div class="mb-6 flex items-center justify-center">
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-slate-400">
                    <button
                        type="button"
                        @click="setTab('active')"
                        :class="tab === 'active' ? 'bg-orange-500 shadow text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                    >
                        Active & Claimable
                    </button>
                    <button
                        type="button"
                        @click="setTab('mine')"
                        :class="tab === 'mine' ? 'bg-orange-500 shadow text-white' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                    >
                        Claimed & Redeemed
                    </button>
                </div>
            </div>

            <!-- Active / Claimable -->
            <div x-show="tab === 'active'" x-cloak>
                @livewire('member.vouchers.browse', key('member-vouchers-browse-page'))
            </div>

            <!-- Claimed / Redeemed -->
            <div x-show="tab === 'mine'" x-cloak>
                @livewire('member.vouchers.my-vouchers', key('member-vouchers-my-vouchers-page'))
            </div>
        </div>
    </div>
</x-app-layout>


