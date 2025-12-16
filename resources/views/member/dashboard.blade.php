<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <button type="button" title="My Points" class="flex items-center gap-1 text-sm text-gray-500 border border-gray-300 rounded-lg px-2 py-1">
                <svg class="w-5 h-5" fill="#000000" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256 S397.391,0,256,0z M256,472c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z"></path> <g> <path d="M256,336.219c-30.031,0-61.438-6.328-79.844-19.703c0,0.219-0.156,0.438-0.156,0.641c0,3.359,0,15.719,0,19.062 C176,353.781,211.812,368,256,368s80-14.219,80-31.781c0-3.344,0-15.703,0-19.062c0-0.203-0.156-0.422-0.156-0.641 C317.438,329.891,286.016,336.219,256,336.219z"></path> <path d="M256,288.547c-30.031,0-61.438-6.312-79.844-19.688c0,0.219-0.156,0.422-0.156,0.641c0,3.359,0,15.703,0,19.047 c0,17.562,35.812,31.797,80,31.797s80-14.234,80-31.797c0-3.344,0-15.688,0-19.047c0-0.219-0.156-0.422-0.156-0.641 C317.438,282.234,286.016,288.547,256,288.547z"></path> <path d="M176,222.656c0,4.031,0,15.078,0,18.25c0,17.562,35.812,31.781,80,31.781s80-14.219,80-31.781c0-3.172,0-14.219,0-18.25 c-18.375,13.469-49.891,19.844-80,19.844S194.375,236.125,176,222.656z"></path> <path d="M256,144c-44.188,0-80,14.219-80,31.781c0,3.344,0,15.703,0,19.062c0,17.531,35.812,31.766,80,31.766s80-14.234,80-31.766 c0-3.359,0-15.719,0-19.062C336,158.219,300.188,144,256,144z"></path> </g> </g>
                </svg>
                <span class="font-semibold text-gray-900 text-sm">{{ auth()->user()->total_points ? number_format(auth()->user()->total_points, 0) : '0 point' }}</span>
            </button>
        </div>
    </x-slot>

    <div class="py-12 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <div 
                            id="qr-code-container" 
                            class="cursor-pointer hover:opacity-80 transition-opacity"
                            onclick="openQrCodeModal()"
                        >
                            <div class="bg-white p-0.5 rounded-none shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <img 
                                    id="qr-code-thumbnail" 
                                    src="" 
                                    alt="QR Code" 
                                    class="w-10 h-10"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Your member overview and quick access.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Screen QR Code Modal -->
            @if(auth()->user()->qr_code)
            <div 
                id="qr-code-modal" 
                class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4"
                onclick="closeQrCodeModal(event)"
            >
                <div class="bg-white rounded-lg p-8 max-w-md w-full relative" onclick="event.stopPropagation()">
                    <button 
                        onclick="closeQrCodeModal(event)"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your QR Code</h3>
                        <p class="text-gray-600 mb-6">Scan this code for facility entry</p>
                        <div class="bg-white p-4 rounded-lg border-2 border-gray-300 inline-block mb-4">
                            <img 
                                id="qr-code-full" 
                                src="" 
                                alt="QR Code" 
                                class="w-64 h-64 mx-auto"
                            >
                        </div>
                        <p class="text-sm text-gray-500 font-mono">{{ auth()->user()->qr_code }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Anchor targets for bottom navigation -->
            <div id="my-vouchers">
                <div>
                    <p class="text-xl font-bold text-gray-700 mb-1">My Vouchers</p>
                    <p class="text-xs text-gray-600">Your claimed vouchers will appear here.</p>
                </div>

                @php
                    // If you later pass real claimed vouchers from backend, provide `$claimedVouchers`.
                    // For now we keep sample items unless `$claimedVouchers` is explicitly set to an empty array/collection.
                    $claimedVouchers = auth()->user()->claimedVouchers ?? ['10', '20', '30', '40', '50'];
                @endphp

                <div class="flex flex-nowrap gap-2 min-h-[140px] items-center overflow-x-auto overflow-y-hidden">
                    @forelse($claimedVouchers as $value)
                        <div class="hover:-translate-y-1 hover:shadow-lg hover:bg-orange-50 transition-all duration-300 shrink-0 flex flex-col w-24 h-24 rounded-full items-center justify-center border border-gray-200 bg-white">
                            <p class="text-md text-gray-400 font-bold">P{{ $value }}</p>
                            <p class="text-xs text-gray-400 font-semibold">Voucher</p>
                        </div>
                    @empty
                        <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                            No Voucher found
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="my-recent-events" class="mt-12">
                <div class="mb-8 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-xl font-bold text-gray-700 mb-1">My Recent Events</p>
                        <p class="text-xs text-gray-600">Your most recently registered events.</p>
                    </div>
                    <a href="{{ route('member.events') }}" class="border border-indigo-600/60 rounded-lg px-2 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        View all
                    </a>
                </div>

                @php
                    $recentRegistrations = auth()->user()
                        ->eventRegistrations()
                        ->whereNotNull('event_id')
                        ->with('event.location', 'event.media')
                        ->latest('registered_at')
                        ->limit(5)
                        ->get();
                @endphp

                <div class="flex flex-nowrap gap-4 min-h-[120px] items-center overflow-x-auto overflow-y-hidden">
                    @forelse($recentRegistrations as $registration)
                        @php
                            $event = $registration->event;
                            if (!$event) {
                                continue;
                            }

                            $eventImageUrl = $event->thumbnail_url ?? null;
                            if (!$eventImageUrl) {
                                $apiKey = config('services.google_maps.api_key');
                                $location = $event->location;
                                if ($apiKey && $location) {
                                    if (isset($location->latitude) && isset($location->longitude) && $location->latitude && $location->longitude) {
                                        $lat = $location->latitude;
                                        $lng = $location->longitude;
                                        $eventImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$lat},{$lng}&zoom=15&size=420x220&markers=color:red|{$lat},{$lng}&key={$apiKey}";
                                    } elseif (!empty($location->address)) {
                                        $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                        $eventImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=420x220&markers=color:red|{$address}&key={$apiKey}";
                                    }
                                }
                            }

                            $start = $event->start_date;
                            $end = $event->end_date;
                            $schedule = 'TBA';
                            if ($start && $end) {
                                $schedule = $start->format('M d, Y');
                                if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
                                    $schedule .= ' • ' . $start->format('g:i A') . ' - ' . $end->format('g:i A');
                                } else {
                                    $schedule = $start->format('M d, Y g:i A') . ' - ' . $end->format('M d, Y g:i A');
                                }
                            } elseif ($start) {
                                $schedule = $start->format('M d, Y g:i A');
                            }
                        @endphp

                        <a href="{{ route('member.events.profile', $event->event_code) }}" class="hover:-translate-y-1 hover:shadow-lg transition-all duration-300 shrink-0 w-56 rounded-xl border border-gray-300 bg-white hover:bg-orange-50 overflow-hidden">
                            <div class="h-36 w-full bg-gray-100">
                                @if(!empty($eventImageUrl ?? null))
                                    <img
                                        src="{{ $eventImageUrl ?? '' }}"
                                        alt="{{ $event->title }}"
                                        class="h-36 w-full object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="h-36 w-full flex items-center justify-center text-xs text-gray-400">
                                        No image
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 min-h-24">
                                <p class="text-md font-bold text-gray-900 leading-snug">
                                    {{ \Illuminate\Support\Str::words($event->title, 5, '...') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $schedule }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50/50 p-4 w-full text-center py-8 text-sm text-gray-400/60 font-semibold">
                            No Event found
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="activities-table" class="mt-12 mb-3">
                <p class="text-lg font-bold text-gray-900">My Recent Activities</p>
                <p class="text-xs text-gray-600">Your recent activities will appear here.</p>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-4">
    
                    <!-- Mobile-first list -->
                    <div class="space-y-3 sm:hidden">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg text-xs font-semibold">Entry</span>
                                    <p class="text-xs text-gray-600">+100 Points</p>
                                </div>
    
                                <span class="text-xs text-gray-500">Dec 03, 2025 • 8:00</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-900">Cebu City Sports Club</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-600 bg-blue-50 px-2 py-1 border border-blue-600/60 rounded-lg text-xs font-semibold">Use</span>
                                    <p class="text-xs text-gray-600">+50 Points</p>
                                </div>
                                <span class="text-xs text-gray-500">Dec 03, 2025 • 10:00</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-900">Cebu City Sports Club - Swimming Pool</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-yellow-600 bg-yellow-50 px-2 py-1 border border-yellow-600/60 rounded-lg text-xs font-semibold">Join</span>
                                    <p class="text-xs text-gray-600">+10 Points</p>
                                </div>
                                <span class="text-xs text-gray-500">Dec 03, 2025 • 15:00</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-900">Cebu City Sports Club - Badminton Court</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-yellow-600 bg-yellow-50 px-2 py-1 border border-yellow-600/60 rounded-lg text-xs font-semibold">Join</span>
                                    <p class="text-xs text-gray-600">+10 Points</p>
                                </div>
                                <span class="text-xs text-gray-500">Dec 03, 2025 • 15:00</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-900">Cebu City Sports Club - Badminton Court</p>
                        </div>
                    </div>
    
                    <!-- Table for larger screens -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="py-4">
                                        <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg">Entry</span>
                                    </td>
                                    <td class="py-2">Cebu City Sports Club</td>
                                    <td class="py-2">December 03, 2025 - 8:00</td>
                                    <td class="py-2">
                                        <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg">100 Points</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-4">
                                        <span class="text-blue-600 bg-blue-50 px-2 py-1 border border-blue-600/60 rounded-lg">Use</span>
                                    </td>
                                    <td class="py-2">Cebu City Sports Club - Swimming Pool</td>
                                    <td class="py-2">December 03, 2025 - 10:00</td>
                                    <td class="py-2">
                                        <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg">50 Points</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-4">
                                        <span class="text-yellow-600 bg-yellow-50 px-2 py-1 border border-yellow-600/60 rounded-lg">Join</span>
                                    </td>
                                    <td class="py-2">Cebu City Sports Club - Badminton Court</td>
                                    <td class="py-2">December 03, 2025 - 15:00</td>
                                    <td class="py-2">
                                        <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg">10 Points</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-4">
                                        <span class="text-yellow-600 bg-yellow-50 px-2 py-1 border border-yellow-600/60 rounded-lg">Join</span>
                                    </td>
                                    <td class="py-2">Cebu City Sports Club - Basketball Game</td>
                                    <td class="py-2">December 05, 2025 - 17:00</td>
                                    <td class="py-2">
                                        <span class="text-green-600 bg-green-50 px-2 py-1 border border-green-600/60 rounded-lg">10 Points</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->qr_code)
    @push('scripts')
    <script>
        // Load QR code on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadQrCode();
        });

        function loadQrCode() {
            fetch('{{ route("member.qr-code") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.image) {
                    document.getElementById('qr-code-thumbnail').src = data.image;
                    document.getElementById('qr-code-full').src = data.image;
                }
            })
            .catch(error => {
                console.error('Error loading QR code:', error);
            });
        }

        function openQrCodeModal() {
            const modal = document.getElementById('qr-code-modal');
            // Load full size QR code
            fetch('{{ route("member.qr-code.full") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.image) {
                    document.getElementById('qr-code-full').src = data.image;
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error loading full QR code:', error);
                // Fallback to thumbnail if full size fails
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeQrCodeModal(event) {
            if (event) {
                event.stopPropagation();
            }
            const modal = document.getElementById('qr-code-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('qr-code-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeQrCodeModal();
                }
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>

