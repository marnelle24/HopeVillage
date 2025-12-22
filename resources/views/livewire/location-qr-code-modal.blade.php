<div>
    <div
        x-data="{ open: @entangle('open').live }"
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
            <div class="pr-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Location QR Code
                </h2>
                
                <div class="space-y-4 mt-6">
                    <!-- QR Code Type -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Location
                            </span>
                        </div>
                    </div>

                    <!-- Location Code -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Location Code</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm font-mono text-gray-900 break-all">{{ $locationCode }}</p>
                        </div>
                    </div>

                    <!-- Location Details -->
                    @if($location)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Location Name</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $location->name }}</p>
                    </div>
                    @if($location->address)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Address</label>
                        <p class="mt-1 text-sm text-gray-700">{{ $location->address }}</p>
                    </div>
                    @endif
                    @else
                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800">Location not found in database.</p>
                    </div>
                    @endif

                    <!-- Member FIN -->
                    @if($memberFin)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Member FIN</label>
                        <div class="mt-1 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm font-mono font-semibold text-blue-900">{{ $memberFin }}</p>
                        </div>
                    </div>
                    @else
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Member FIN</label>
                        <div class="mt-1 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">Not logged in</p>
                        </div>
                    </div>
                    @endif

                    <!-- Error Message -->
                    @if($error)
                    <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-sm text-red-800">{{ $error }}</p>
                    </div>
                    @endif

                    <!-- Success Message -->
                    @if($success)
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800 font-semibold">✓ Location scan processed successfully!</p>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-3">
                    <button
                        @click="$wire.close()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                    >
                        Close
                    </button>
                    @if($location && $memberFin && !$success)
                    <button
                        @click="$wire.processScan()"
                        :disabled="$wire.processing"
                        class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!$wire.processing">Process Scan</span>
                        <span x-show="$wire.processing">Processing...</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
