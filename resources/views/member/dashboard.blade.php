<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Member Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-start gap-3">
                        <div 
                            id="qr-code-container" 
                            class="cursor-pointer hover:opacity-80 transition-opacity lg:scale-100 scale-75"
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
                        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                    </div>
                    @if(auth()->user()->qr_code)
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-blue-100 text-blue-800 border lg:scale-100 scale-75 border-blue-300 rounded-lg">
                            <span class="font-semibold text-sm">Verified Member</span>
                        </div>
                    </div>
                    @else
                    <div class="px-4 py-2 bg-blue-100 text-blue-800 border lg:scale-100 scale-75 border-blue-300 rounded-lg">
                        <span class="font-semibold text-sm">Verified Member</span>
                    </div>
                    @endif
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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Points Card -->
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">Total Points</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ auth()->user()->total_points ?? 0 }}</p>
                        <p class="text-sm text-yellow-700 mt-2">Earn points with activities</p>
                    </div>

                    <!-- Bookings Card -->
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-green-900 mb-2">My Bookings</h3>
                        <p class="text-3xl font-bold text-green-600">0</p>
                        <p class="text-sm text-green-700 mt-2">View your reservations</p>
                    </div>

                    <!-- Events Card -->
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Registered Events</h3>
                        <p class="text-3xl font-bold text-purple-600">0</p>
                        <p class="text-sm text-purple-700 mt-2">Upcoming events</p>
                    </div>
                </div>

                {{-- <div class="mt-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Book Amenity
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            View Events
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            My Activities
                        </button>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            View QR Code
                        </button>
                    </div>
                </div> --}}

                {{-- <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800">
                        <strong>Note:</strong> This is a test UI dashboard for Member users. Full functionality will be implemented later.
                    </p>
                </div> --}}
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-10">
                <p class="lg:text-lg text-sm font-bold text-gray-900 mb-6">My Recent Activities</p>
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

