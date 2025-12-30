<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Raffle Draw') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ isSpinning: false }" @roulette-spinning.window="isSpinning = $event.detail">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Controls -->
                <div class="bg-white shadow-md rounded-lg p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Select entries source</h3>

                    <div class="space-y-3">
                        <label class="flex items-center gap-2" x-bind:class="isSpinning ? 'opacity-50 cursor-not-allowed' : ''">
                            <input type="radio" wire:model.live="source" value="range" class="text-indigo-600" x-bind:disabled="isSpinning">
                            <span class="text-sm text-gray-800 font-semibold">Set a range of numbers</span>
                        </label>
                        <label class="flex items-center gap-2" x-bind:class="isSpinning ? 'opacity-50 cursor-not-allowed' : ''">
                            <input type="radio" wire:model.live="source" value="members_fin" class="text-indigo-600" x-bind:disabled="isSpinning">
                            <span class="text-sm text-gray-800 font-semibold">All member FIN/NIRC numbers</span>
                        </label>
                        <label class="flex items-center gap-2" x-bind:class="isSpinning ? 'opacity-50 cursor-not-allowed' : ''">
                            <input type="radio" wire:model.live="source" value="members_qr_code" class="text-indigo-600" x-bind:disabled="isSpinning">
                            <span class="text-sm text-gray-800 font-semibold">QR Code of the members</span>
                        </label>
                        <label class="flex items-center gap-2" x-bind:class="isSpinning ? 'opacity-50 cursor-not-allowed' : ''">
                            <input type="radio" wire:model.live="source" value="event_attendees" class="text-indigo-600" x-bind:disabled="isSpinning">
                            <span class="text-sm text-gray-800 font-semibold">Event attendees (FIN/NIRC)</span>
                        </label>
                    </div>

                    <div class="mt-5 space-y-4">
                        @if($source === 'range')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Start</label>
                                    <input type="number" wire:model.live="rangeStart" class="mt-1 w-full text-gray-700 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('rangeStart') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">End</label>
                                    <input type="number" wire:model.live="rangeEnd" class="mt-1 w-full text-gray-700 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('rangeEnd') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        @if($source === 'event_attendees')
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Select Event</label>
                                <select wire:model.live="selectedEventId" class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- choose --</option>
                                    @foreach($events as $e)
                                        <option value="{{ $e['id'] }}">{{ $e['title'] }}</option>
                                    @endforeach
                                </select>
                                @error('selectedEventId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <button type="button" wire:click="loadEntries" class="flex-1 border border-gray-300 text-gray-700 font-semibold rounded-full text-sm py-2 hover:bg-gray-50">
                                Load
                            </button>
                            <button 
                                type="button" 
                                wire:click="spin" 
                                x-bind:disabled="isSpinning"
                                x-bind:class="isSpinning ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-600'"
                                class="flex-1 bg-orange-500 cursor-pointer rounded-full text-white font-semibold text-sm py-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-text="isSpinning ? 'Spinning...' : 'Start Raffle'">Start Raffle</span>
                            </button>
                        </div>

                        @error('entries')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="text-sm text-gray-700">
                            <span class="font-semibold">Entries:</span> {{ count($entries) }}
                        </div>

                        @if(count($entries) > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-bold text-gray-900 mb-2">Entries Preview</h4>
                                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                                    <table class="w-full text-sm text-gray-700">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 border-b border-gray-200">#</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 border-b border-gray-200">Entry</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach(array_slice($entries, 0, 50) as $index => $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-1.5 text-xs text-gray-500">{{ $index + 1 }}</td>
                                                    <td class="px-3 py-1.5 text-xs text-gray-700">{{ $item }}</td>
                                                </tr>
                                            @endforeach
                                            @if(count($entries) > 50)
                                                <tr>
                                                    <td colspan="2" class="px-3 py-2 text-xs text-gray-500 text-center italic">+{{ count($entries) - 50 }} more entries</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Wheel -->
                <div class="bg-white shadow-md rounded-lg p-6 lg:col-span-3">
                    <div
                        x-data="{
                            degrees: 0,
                            spinning: false,
                            spinTo(targetDegrees) {
                                this.spinning = true;
                                // Update parent's isSpinning state
                                $dispatch('roulette-spinning', true);
                                
                                // Always add the target rotation to current position to ensure full spin
                                // This guarantees the wheel always spins forward from its current position
                                this.degrees = this.degrees + targetDegrees;
                                
                                setTimeout(() => { 
                                    this.spinning = false;
                                    // Update parent's isSpinning state
                                    $dispatch('roulette-spinning', false);
                                    // Dispatch event to open winner modal after spin completes
                                    window.dispatchEvent(new CustomEvent('roulette-spin-complete'));
                                }, 4000);
                            }
                        }"
                        @roulette-spun.window="spinTo($event.detail.degrees)"
                        class="flex flex-col items-center"
                    >
                        <div class="relative w-72 h-72 sm:w-96 sm:h-96 lg:w-[500px] lg:h-[500px] xl:w-[600px] xl:h-[600px]">
                            <!-- pointer -->
                            <div class="absolute -top-2 left-1/2 -translate-x-1/2 z-20">
                                <div class="w-0 h-0 border-l-[10px] border-r-[10px] border-t-[18px] border-l-transparent border-r-transparent border-t-orange-500"></div>
                            </div>

                            @php
                                $count = max(1, count($entries));
                                $colors = ['#fde68a', '#bfdbfe', '#fecaca', '#bbf7d0', '#e9d5ff', '#fed7aa'];
                                $gradientParts = [];
                                for ($i = 0; $i < $count; $i++) {
                                    $c = $colors[$i % count($colors)];
                                    $start = ($i * (360 / $count));
                                    $end = (($i + 1) * (360 / $count));
                                    $gradientParts[] = "$c {$start}deg {$end}deg";
                                }
                                $gradient = 'conic-gradient(' . implode(', ', $gradientParts) . ')';
                            @endphp

                            <!-- wheel + labels (rotate together) -->
                            <div
                                class="absolute inset-0"
                                :style="`transform: rotate(${degrees}deg); transition: ${spinning ? 'transform 4s cubic-bezier(0.17, 0.67, 0.12, 0.99)' : 'none'};`"
                            >
                                <!-- wheel background -->
                                <div
                                    class="absolute inset-0 rounded-full border border-gray-200 shadow-inner"
                                    style="background: {{ $gradient }};"
                                ></div>

                                <!-- labels (locked to slices, rotate with wheel) -->
                                @php
                                    $labelCount = count($entries);
                                    // Keep labels readable: show at most ~36 labels.
                                    $showEvery = $labelCount > 0 ? (int) ceil($labelCount / 36) : 1;
                                    $isFinSource = in_array($source ?? '', ['members_fin', 'members_qr_code', 'event_attendees'], true);
                                    $isQrCode = ($source ?? '') === 'members_qr_code';
                                    // Wheel sizes: mobile (288px), sm (384px), lg (500px), xl (600px)
                                    // Radius = half of width/height, minus 20px padding from edge, minus extra padding for QR codes
                                    $extraPadding = $isQrCode ? 10 : 0; // Extra padding for QR codes to prevent overflow
                                    $radiusMobile = (288 / 2) - 20 - $extraPadding; // 124px or 114px for QR
                                    $radiusSm = (384 / 2) - 20 - $extraPadding; // 172px or 162px for QR
                                    $radiusLg = (500 / 2) - 20 - $extraPadding; // 230px or 220px for QR
                                    $radiusXl = (600 / 2) - 20 - $extraPadding; // 280px or 270px for QR
                                @endphp
                                <div class="absolute inset-0 z-10 pointer-events-none select-none">
                                    @foreach($entries as $i => $item)
                                        @if($labelCount > 0 && ($i % $showEvery === 0))
                                            @php
                                                $segment = 360 / $labelCount;
                                                $angle = ($i * $segment) + ($segment / 2);
                                                // Shorten labels more aggressively for QR codes to prevent overflow
                                                if ($isQrCode) {
                                                    $label = is_string($item) ? \Illuminate\Support\Str::limit($item, 8, '…') : (string) $item;
                                                } else {
                                                    $label = is_string($item) ? \Illuminate\Support\Str::limit($item, 10, '…') : (string) $item;
                                                }
                                            @endphp
                                            <!-- Single label per entry - centered and aligned in fan segment, vertical orientation -->
                                            <div
                                                class="absolute left-1/2 top-1/2 font-bold text-gray-800
                                                    {{ $isFinSource ? 'text-[9px] sm:text-[10px] lg:text-xs xl:text-sm' : 'text-[10px] sm:text-[11px] lg:text-sm xl:text-base' }}"
                                                style="transform-origin: center center; text-align: center;"
                                                x-data="{
                                                    radius: {{ $radiusMobile }},
                                                    angle: {{ $angle }},
                                                    rotateText: {{ $isFinSource ? 'false' : 'true' }},
                                                    get transform() {
                                                        // Position at center, rotate to segment angle, move outward along radius
                                                        // Use consistent transform origin and positioning
                                                        let t = `translate(-50%, -50%) rotate(${this.angle}deg) translate(0, -${this.radius}px)`;
                                                        // Rotate text 90 degrees to make it vertical (inline with wheel radial direction)
                                                        t += ` rotate(90deg)`;
                                                        // Counter-rotate text to keep it readable (for non-FIN sources)
                                                        if (this.rotateText) {
                                                            t += ` rotate(-${this.angle}deg)`;
                                                        }
                                                        return t;
                                                    },
                                                    updateRadius() {
                                                        const width = window.innerWidth;
                                                        if (width >= 1280) this.radius = {{ $radiusXl }};
                                                        else if (width >= 1024) this.radius = {{ $radiusLg }};
                                                        else if (width >= 640) this.radius = {{ $radiusSm }};
                                                        else this.radius = {{ $radiusMobile }};
                                                    }
                                                }"
                                                x-init="updateRadius(); window.addEventListener('resize', () => updateRadius())"
                                                :style="`transform: ${transform};`"
                                            >
                                                 <span 
                                                     class="inline-block px-1 py-0.5 rounded text-xs text-gray-800 font-bold whitespace-nowrap"
                                                     style="max-width: {{ $isQrCode ? '60px' : '80px' }}; overflow: hidden; text-overflow: ellipsis;"
                                                 >
                                                     {{ $label }}
                                                 </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- center -->
                            <div class="absolute inset-0 z-20 flex items-center justify-center">
                                <div class="w-20 h-20 rounded-full bg-white border border-gray-200 shadow flex items-center justify-center">
                                    <span class="text-xs font-bold text-gray-700" x-text="spinning ? 'Spinning…' : 'Ready'">Ready</span>
                                </div>
                            </div>
                        </div>

                        <p class="mt-6 text-xs text-gray-500 text-center">
                            Tip: For “Event attendees”, only registrations with status <span class="font-mono">attended</span> are included.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Winner Modal -->
    <dialog 
        x-data="{ 
            openModal() { 
                this.$el.showModal(); 
            },
            closeModal() { 
                this.$el.close(); 
            }
        }"
        x-init="
            window.addEventListener('roulette-spin-complete', () => {
                openModal();
            });
        "
        class="modal"
        @click.away="closeModal()"
    >
        <div class="modal-box text-center">
            <h3 class="font-bold text-2xl mb-4 text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 text-yellow-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                🎉 Winner! 🎉
            </h3>
            @if($winner)
                <div class="py-6">
                    <p class="text-sm text-gray-600 mb-3">The winner is:</p>
                    <p class="text-4xl font-bold text-primary break-all px-4">
                        {{ $winner }}
                    </p>
                    @if($source === 'event_attendees' || $source === 'members_fin')
                        <p class="text-sm text-gray-500 mt-4">FIN Number</p>
                    @elseif($source === 'members_qr_code')
                        <p class="text-sm text-gray-500 mt-4">QR Code</p>
                    @else
                        <p class="text-sm text-gray-500 mt-4">Selected Number</p>
                    @endif
                </div>
            @else
                <p class="text-gray-600 py-4">No winner selected.</p>
            @endif
            <div class="modal-action justify-center">
                <button 
                    @click="$el.close()"
                    class="btn btn-primary"
                >
                    Close
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>
