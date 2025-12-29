<x-app-layout>
    <div class="min-h-screen pb-20">
        <div class="max-w-md mx-auto min-h-screen">
            <!-- Header Section with Avatar and Greeting -->
            <div class="bg-gradient-to-br from-orange-300 to-orange-400 p-6 pb-8 rounded-b-4xl shadow-lg">
                <div class="flex items-center justify-between animate-fade-in">
                    <div class="flex items-start gap-3">
                        <div>
                            <p class="text-orange-100 text-lg font-bold mb-4 drop-shadow">Welcome back!</p>
                            <div class="flex items-start gap-2">
                                <div class="border-2 border-white bg-white/70 shadow-lg rounded-xl p-2">
                                    <svg class="size-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 13H16C17.7107 13 19.1506 14.2804 19.3505 15.9795L20 21.5M8 13C5.2421 12.3871 3.06717 10.2687 2.38197 7.52787L2 6M8 13V18C8 19.8856 8 20.8284 8.58579 21.4142C9.17157 22 10.1144 22 12 22C13.8856 22 14.8284 22 15.4142 21.4142C16 20.8284 16 19.8856 16 18V17" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"></path> <circle cx="12" cy="6" r="4" stroke="#ffffff" stroke-width="1.5"></circle> </g>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-orange-900 text-3xl font-bold flex items-center gap-2">
                                        {{ auth()->user()->name }}
                                    </h2>
                                    <div class="flex items-center gap-2">
                                        <p class="text-white font-mono tracking-wider text-sm font-medium">{{ auth()->user()->qr_code }}</p>
                                        {{-- verified badge --}}
                                        @if(auth()->user()->is_verified)
                                            <span class="text-white text-[11px] font-medium bg-green-400 text-green-300 px-1.5 py-0.5 rounded-full">Verified</span>
                                        @else
                                            <span class="text-white text-[11px] font-medium bg-red-400 text-red-400 px-1.5 py-0.5 rounded-full">Unverified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="flex flex-col">
                                <span class="text-white text-md font-medium">My points</span>
                                <div class="flex items-center gap-2">
                                    <svg class="size-6" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css">  .st0{fill:#ffffff;}  </style> <g> <path class="st0" d="M336.563,225.156c67.797,0,122.938,55.156,122.938,122.938s-55.141,122.938-122.938,122.938 c-67.766,0-122.922-55.156-122.922-122.938S268.797,225.156,336.563,225.156 M336.563,184.188 c-90.5,0-163.891,73.375-163.891,163.906S246.063,512,336.563,512c90.516,0,163.906-73.375,163.906-163.906 S427.078,184.188,336.563,184.188z"></path> <path class="st0" d="M341.922,293.406h-34.891c-5.578,0-10.078,4.5-10.078,10.063v102.875c0,5.563,4.5,10.094,10.078,10.094h5.063 c5.563,0,10.078-4.531,10.078-10.094v-28.953h19.75c26.422,0,47.922-18.828,47.922-42 C389.844,312.25,368.344,293.406,341.922,293.406z M341.922,355.281h-19.75V315.5h19.75c12.516,0,22.688,8.938,22.688,19.891 S354.438,355.281,341.922,355.281z"></path> <path class="st0" d="M144.391,0C71,0,11.531,28.969,11.531,64.719v34.563C11.531,135.031,71,164,144.391,164 c73.375,0,132.859-28.969,132.859-64.719V64.719C277.25,28.969,217.766,0,144.391,0z M144.391,26.875 c64.703,0,105.969,24.844,105.969,37.844s-41.266,37.844-105.969,37.844S38.422,77.719,38.422,64.719S79.688,26.875,144.391,26.875 z"></path> <path class="st0" d="M144.375,216c24.578,0,47.594-3.281,67.359-8.938c-19.016,16.719-34.578,37.375-45.563,60.563 c-7.063,0.563-14.344,0.906-21.797,0.906c-73.359,0-132.844-29.016-132.844-64.75v-40.625c0-0.656,0.484-1.25,1.141-1.313 c0.328-0.094,0.656,0.063,0.906,0.406c-0.156,0,0,0.469,0.891,2.688C27.344,194.125,80.609,216,144.375,216z"></path> <path class="st0" d="M148.063,348.094c0,7.969,0.484,15.75,1.547,23.438c-1.719,0.094-3.438,0.094-5.234,0.094 c-73.359,0-132.844-28.938-132.844-64.656v-40.75c0-0.563,0.484-1.125,1.141-1.219c0.531-0.063,0.891,0.25,1.125,0.625 c-0.297-0.406-0.641-0.625,0.672,2.5c12.875,29.156,66.141,51.063,129.906,51.063c1.969,0,4.016,0,5.969-0.188 C148.797,328.516,148.063,338.188,148.063,348.094z"></path> <path class="st0" d="M193.203,470.281c-15.078,2.969-31.547,4.5-48.828,4.5c-73.359,0-132.844-28.906-132.844-64.719v-40.656 c0-0.656,0.484-1.156,1.141-1.219c0.5-0.094,0.984,0.156,1.141,0.656c-0.313-0.406-0.734-0.813,0.656,2.375 c12.875,29.25,66.141,51.125,129.906,51.125c6.313,0,12.609-0.25,18.672-0.656C170.594,439.469,180.844,455.875,193.203,470.281z"></path> </g> </g>
                                    </svg>
                                    <p class="text-white text-3xl font-bold">{{ auth()->user()->total_points }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <a href="{{ route('member.vouchers') }}" class="btn btn-accent btn-sm gap-2 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        JOIN NOW
                    </a> --}}
                </div>
            </div>

            <!-- Main Content -->
            <div class="px-4 mt-10 relative z-10">
                <!-- Search Section -->
                <div class="mb-6 animate-slide-down">
                    <h1 class="text-2xl font-bold text-base-content mb-4">What's on your mind?</h1>
                    <label class="w-full input input-bordered rounded-full flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-5 w-5 opacity-70">
                            <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                        </svg>
                        <input type="text" class="w-full grow" placeholder="Ask anything about Hope Village" id="location-search" class="p-5 rounded-full focus:ring-0 focus:border-0" />
                    </label>
                </div>

                <!-- Categories Section -->
                <div class="mb-8 animate-slide-up delay-100">
                    <h2 class="text-xl font-bold text-orange-400 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-3 gap-4">
                        @php
                            $categories = [
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" /></svg>',
                                    'name' => 'Locations',
                                    'route' => route('member.events'),
                                    'color' => 'primary'
                                ],
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>',
                                    'name' => 'Events',
                                    'route' => route('member.events'),
                                    'color' => 'secondary'
                                ],
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236c2.026-.099 4.024-.3 5.974-.461a48.34 48.34 0 0 0 2.96-.493m-9.934 0a50.697 50.697 0 0 1 9.934 0m0 0a48.191 48.191 0 0 1 2.96-.493m-9.934 0c.18.865.428 1.712.981 3.172m0 0a48.415 48.415 0 0 0 2.923-.744M11.348 7.752c-.17.865-.428 1.712-.981 3.172M11.348 7.752l3.496 4.499m9.656-8.994l-3.496 4.499m0 0-3.497 4.499m3.497-4.499L21 12m-9.652-4.248L8.977 10.53" /></svg>',
                                    'name' => 'Vouchers',
                                    'route' => route('member.vouchers'),
                                    'color' => 'accent'
                                ],
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 1 3 2.48Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.546 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" /></svg>',
                                    'name' => 'Merchants',
                                    'route' => route('member.events'),
                                    'color' => 'info'
                                ],
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                                    'name' => 'Programes',
                                    'route' => route('member.events'),
                                    'color' => 'success'
                                ],
                                [
                                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 4.497a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443a55.381 55.381 0 0 1 5.25 2.882V15" /></svg>',
                                    'name' => 'Amenities',
                                    'route' => route('member.events'),
                                    'color' => 'warning'
                                ],
                            ];
                        @endphp

                        @foreach($categories as $index => $category)
                            @php
                                $colorClasses = [
                                    'primary' => 'text-primary',
                                    'secondary' => 'text-secondary',
                                    'accent' => 'text-accent',
                                    'info' => 'text-info',
                                    'success' => 'text-success',
                                    'warning' => 'text-warning',
                                ];
                                $iconColorClass = $colorClasses[$category['color']] ?? 'text-primary';
                            @endphp
                            <a href="{{ $category['route'] }}" 
                               class="card bg-base-100 shadow-md hover:shadow-xl border border-base-300 rounded-2xl p-4 transition-all duration-300 hover:scale-105 hover:-translate-y-1 animate-fade-in"
                               style="animation-delay: {{ ($index + 1) * 50 }}ms">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="{{ $iconColorClass }} mb-1">
                                        {!! $category['icon'] !!}
                                    </div>
                                    <p class="text-xs font-semibold text-base-content text-center leading-tight">{{ $category['name'] }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Popular Section -->
                <div class="mt-18 mb-6 animate-slide-up delay-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-orange-500">Recreation Centers</h2>
                        <a href="{{ route('member.events') }}" class="text-orange-500 text-sm font-medium flex items-center gap-1 hover:gap-2 transition-all">
                            See All
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>

                    @php
                        $popularLocations = \App\Models\Location::where('is_active', true)
                            ->with('media')
                            ->take(6)
                            ->get();
                    @endphp

                    <div class="carousel carousel-center space-x-4 pb-4 overflow scrollbar-hide" style="scroll-snap-type: x mandatory;">
                        @forelse($popularLocations as $index => $location)
                            @php
                                $apiKey = config('services.google_maps.api_key');
                                $locationImageUrl = $location->thumbnail_url ?? null;
                                if (!$locationImageUrl && $apiKey && $location->address) {
                                    $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                                    $locationImageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=400x200&markers=color:red|{$address}&key={$apiKey}";
                                }
                                
                                // Calculate distance (mock for now, you can implement real distance calculation)
                                $distance = rand(5, 50);
                            @endphp
                            <div class="carousel-item flex-none w-64 animate-slide-in" style="animation-delay: {{ ($index + 1) * 100 }}ms; scroll-snap-align: start;">
                                <a href="{{ route('member.events') }}" class="card bg-base-100 shadow-lg hover:shadow-2xl rounded-2xl overflow-hidden transition-all duration-300 hover:scale-105 w-full">
                                    <figure class="h-40 bg-base-200 overflow-hidden">
                                        @if($locationImageUrl)
                                            <img src="{{ $locationImageUrl }}" alt="{{ $location->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-base-300">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </figure>
                                    <div class="card-body p-4">
                                        <p class="text-xs text-primary font-semibold mb-1">{{ $distance }} km away</p>
                                        <h3 class="card-title text-base font-bold text-base-content line-clamp-2">{{ $location->name }}</h3>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="card bg-base-100 shadow-lg rounded-2xl w-64">
                                <div class="card-body items-center justify-center h-64">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-base-300 mb-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <p class="text-sm text-base-300 font-medium">No locations available</p>
                                </div>
                            </div>
                        @endforelse
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
                    const thumbnail = document.getElementById('qr-code-thumbnail');
                    const full = document.getElementById('qr-code-full');
                    if (thumbnail) thumbnail.src = data.image;
                    if (full) full.src = data.image;
                }
            })
            .catch(error => {
                console.error('Error loading QR code:', error);
            });
        }

        function openQrCodeModal() {
            const modal = document.getElementById('qr-code-modal');
            if (modal) {
                // Load full size QR code
                fetch('{{ route("qr-code.full") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    const full = document.getElementById('qr-code-full');
                    if (data.image && full) {
                        full.src = data.image;
                    }
                    modal.showModal();
                })
                .catch(error => {
                    console.error('Error loading full QR code:', error);
                    modal.showModal();
                });
            }
        }
    </script>
    @endpush
    @endif

    @push('styles')
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes wave {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(20deg);
            }
            75% {
                transform: rotate(-20deg);
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-slide-down {
            animation: slide-down 0.6s ease-out forwards;
        }

        .animate-slide-up {
            animation: slide-up 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-slide-in {
            animation: slide-in 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-wave {
            display: inline-block;
            animation: wave 1s ease-in-out infinite;
            transform-origin: 70% 70%;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush
</x-app-layout>

