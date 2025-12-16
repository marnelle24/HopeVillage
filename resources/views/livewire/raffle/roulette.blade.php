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

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Controls -->
                <div class="bg-white shadow-md rounded-lg p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Entry Source</h3>

                    <div class="space-y-3">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="source" value="range" class="text-indigo-600">
                            <span class="text-sm text-gray-800 font-semibold">Number range</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="source" value="members_fin" class="text-indigo-600">
                            <span class="text-sm text-gray-800 font-semibold">All member FIN numbers</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="source" value="event_attendees" class="text-indigo-600">
                            <span class="text-sm text-gray-800 font-semibold">Event attendees (FIN)</span>
                        </label>
                    </div>

                    <div class="mt-5 space-y-4">
                        @if($source === 'range')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Start</label>
                                    <input type="number" wire:model.live="rangeStart" class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('rangeStart') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">End</label>
                                    <input type="number" wire:model.live="rangeEnd" class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
                            <button type="button" wire:click="loadEntries" class="flex-1 border border-gray-300 text-gray-700 font-semibold rounded-lg py-2 hover:bg-gray-50">
                                Load
                            </button>
                            <button type="button" wire:click="spin" class="flex-1 bg-indigo-600 text-white font-semibold rounded-lg py-2 hover:bg-indigo-700">
                                Spin
                            </button>
                        </div>

                        @error('entries')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="text-sm text-gray-700">
                            <span class="font-semibold">Entries:</span> {{ count($entries) }}
                        </div>

                        @if($winner)
                            <div class="mt-2 p-3 rounded-lg bg-green-50 border border-green-200">
                                <p class="text-xs text-green-700 font-semibold">Winner</p>
                                <p class="text-lg font-bold text-green-900 break-all">{{ $winner }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Wheel -->
                <div class="bg-white shadow-md rounded-lg p-6 lg:col-span-2">
                    <div
                        x-data="{
                            degrees: 0,
                            spinning: false,
                            spinTo(d) {
                                this.spinning = true;
                                this.degrees = d;
                                setTimeout(() => { this.spinning = false; }, 4200);
                            }
                        }"
                        @roulette-spun.window="spinTo($event.detail.degrees)"
                        class="flex flex-col items-center"
                    >
                        <div class="relative w-72 h-72 sm:w-96 sm:h-96">
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
                                :style="`transform: rotate(${degrees}deg); transition: transform 4s cubic-bezier(0.15, 0.75, 0.25, 1);`"
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
                                    $isFinSource = in_array($source ?? '', ['members_fin', 'event_attendees'], true);
                                @endphp
                                <div class="absolute inset-0 z-10 pointer-events-none select-none">
                                    @foreach($entries as $i => $item)
                                        @if($labelCount > 0 && ($i % $showEvery === 0))
                                            @php
                                                $segment = 360 / $labelCount;
                                                $angle = ($i * $segment) + ($segment / 2);
                                                // Keep labels short for FINs; numbers stay as-is.
                                                $label = is_string($item) ? \Illuminate\Support\Str::limit($item, 10, '…') : (string) $item;
                                            @endphp
                                            <!-- small screens wheel (w-72 / h-72) -->
                                            <div
                                                class="absolute left-1/2 top-1/2 sm:hidden font-bold text-gray-700 {{ $isFinSource ? 'text-[9px]' : 'text-[10px]' }}"
                                                style="transform: translate(-50%, -50%) rotate({{ $angle }}deg) translateY({{ $isFinSource ? '-112px' : '-130px' }}) {{ $isFinSource ? '' : "rotate(-{$angle}deg)" }};"
                                            >
                                                {{ $label }}
                                            </div>

                                            <!-- sm+ screens wheel (w-96 / h-96) -->
                                            <div
                                                class="absolute left-1/2 top-1/2 hidden sm:block font-bold text-gray-700 {{ $isFinSource ? 'text-[10px]' : 'text-[11px]' }}"
                                                style="transform: translate(-50%, -50%) rotate({{ $angle }}deg) translateY({{ $isFinSource ? '-152px' : '-175px' }}) {{ $isFinSource ? '' : "rotate(-{$angle}deg)" }};"
                                            >
                                                {{ $label }}
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

                        <div class="mt-6 w-full">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Entries Preview</h4>
                            <div class="max-h-44 overflow-y-auto border border-gray-200 rounded-lg p-3 text-sm text-gray-700 bg-gray-50">
                                @if(count($entries) === 0)
                                    <p class="text-gray-500">No entries loaded.</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($entries, 0, 50) as $item)
                                            <span class="px-2 py-1 bg-white border border-gray-200 rounded text-xs">{{ $item }}</span>
                                        @endforeach
                                        @if(count($entries) > 50)
                                            <span class="px-2 py-1 bg-white border border-gray-200 rounded text-xs text-gray-500">+{{ count($entries) - 50 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-500">
                            Tip: For “Event attendees”, only registrations with status <span class="font-mono">attended</span> are included.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
