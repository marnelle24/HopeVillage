<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
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

    <div class="py-12 md:px-0 px-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
                <div class="mb-4 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-xl font-bold text-gray-700 mb-1">My Vouchers</p>
                        <p class="text-xs text-gray-600">Your most recently claimed vouchers.</p>
                    </div>
                    <a
                        href="{{ route('member.vouchers', ['status' => 'my-vouchers']) }}"
                        class="border border-indigo-600/60 rounded-lg px-2 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700"
                    >
                        View all
                    </a>
                </div>

                @php
                    $recentClaimedVouchers = auth()->user()
                        ->vouchers()
                        ->wherePivot('status', 'claimed')
                        ->latest('user_voucher.claimed_at')
                        ->limit(6)
                        ->get();
                @endphp

                <div class="flex flex-nowrap gap-2 min-h-[140px] items-center overflow-x-auto overflow-y-hidden">
                    @forelse($recentClaimedVouchers as $voucher)
                        @livewire('member.vouchers.card', ['value' => (string) $voucher->voucher_code], key('member-voucher-card-' . $voucher->id . '-' . $voucher->voucher_code))
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

