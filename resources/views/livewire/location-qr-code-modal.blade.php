<div
    x-data="{
        open: @entangle('open').live,
        success: @entangle('success').live,
        waitingForGeolocation: @entangle('waitingForGeolocation').live,
        init() {
            this.$watch('success', (value) => {
                if (value) {
                    setTimeout(() => $wire.close(), 2000);
                }
            });
            this.$watch('waitingForGeolocation', (value) => {
                if (value && navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => $wire.processLocationEntry(pos.coords.latitude, pos.coords.longitude),
                        (err) => {
                            $wire.processLocationEntry(null, null);
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else if (value && !navigator.geolocation) {
                    $wire.processLocationEntry(null, null);
                }
            });
        }
    }"
    @show-proximity-alert.window="alert($event.detail?.message || 'You must be physically at this facility to check in.')"
>
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
        @keydown.escape.window="$wire.close()"
        @click.self="$wire.close()"
        style="display: none;"
    >
        <div
            @click.stop
            class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative z-[10000]"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Close button -->
            <button
                @click="$wire.close()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition"
                aria-label="Close"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="pr-8 min-h-[200px] flex items-center justify-center">
                <!-- Processing Message -->
                @if($processing)
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Processing location entry...</p>
                </div>
                @elseif($success)
                <!-- Success Message -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-2xl text-green-800 font-bold mb-4">SUCCESS</p>
                    <p class="text-sm text-green-700 px-4">Redirecting to dashboard...</p>
                    {{-- add 3 seconds delay  with message redirecting to dashboard--}}
                    <script>
                        setTimeout(() => {
                            // reload the page
                            window.location.reload();
                        }, 3000);
                    </script>
                </div>
                @elseif($error)
                <!-- Error Message -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        {{-- <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg> --}}
                        <svg class="w-16 h-16 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>

                    </div>
                    {{-- <p class="text-2xl text-red-800 font-bold mb-4">FAIL</p> --}}
                    <p class="text-2xl text-green-600 px-4">{{ $error }}</p>
                    
                    <!-- Action Buttons -->
                    <div class="mt-6">
                        <button
                            @click="$wire.close()"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                        >
                            Close
                        </button>
                    </div>
                </div>
                @elseif($waitingForGeolocation)
                <!-- Verifying location -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Verifying your location...</p>
                </div>
                @else
                <!-- Default/Loading state -->
                <div class="text-center py-8 w-full">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Loading...</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
