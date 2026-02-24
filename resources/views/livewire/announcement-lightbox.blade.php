<div>
    @if($announcements->isNotEmpty())
        <div
            x-data="{
                open: false,
                currentIndex: 0,
                total: {{ $announcements->count() }},
                storageKey: 'announcementLightboxLastClosedAt',
                intervalMs: 5 * 60 * 1000,
                init() {
                    const lastClosed = parseInt(localStorage.getItem(this.storageKey) || '0', 10);
                    if (!lastClosed || (Date.now() - lastClosed >= this.intervalMs)) {
                        this.open = true;
                    }
                    setInterval(() => {
                        const last = parseInt(localStorage.getItem(this.storageKey) || '0', 10);
                        if (last && (Date.now() - last >= this.intervalMs)) {
                            this.open = true;
                        }
                    }, this.intervalMs);
                },
                close() {
                    this.open = false;
                    try { localStorage.setItem(this.storageKey, String(Date.now())); } catch (e) {}
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.total;
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.total) % this.total;
                },
                goTo(i) {
                    this.currentIndex = i;
                }
            }"
            x-show="open"
            x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[110] overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            @keydown.escape.window="if (open) close()"
            role="dialog"
            aria-modal="true"
            aria-label="Announcements carousel"
        >
            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500/75 z-0"
                @click="close()"
            ></div>

            {{-- Modal: carousel --}}
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-10 max-w-3xl mx-auto shadow-xl overflow-hidden"
                @click.stop
            >
                {{-- Carousel: only the current slide is visible; next/prev never shown — fade between slides --}}
                <div class="relative overflow-hidden" style="height: 70vh; width: 100%;">
                    {{-- Close button --}}
                    <button
                        type="button"
                        @click="close()"
                        class="absolute top-3 right-0 p-2 w-9 h-9 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/50 transition z-30"
                        aria-label="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>

                    @foreach($announcements as $index => $announcement)
                        <div
                            x-show="currentIndex === {{ $index }}"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0 h-auto top-[18%]"
                            style="display: none;"
                        >
                            @if($announcement->link_url)
                                <a href="{{ $announcement->link_url }}" rel="noopener noreferrer" class="block h-auto w-full focus:outline-none">
                                    <img src="{{ $announcement->banner_url }}" alt="{{ $announcement->title }}" class="w-full h-full object-contain object-center">
                                </a>
                            @else
                                <img src="{{ $announcement->banner_url }}" alt="{{ $announcement->title }}" class="w-full h-full object-contain object-center">
                            @endif
                        </div>
                    @endforeach

                    {{-- Prev / Next (only when more than one slide) --}}
                    @if($announcements->count() > 1)
                        <button
                            type="button"
                            @click="prev()"
                            class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/50 transition z-20"
                            aria-label="Previous"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="next()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/50 transition z-20"
                            aria-label="Next"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
