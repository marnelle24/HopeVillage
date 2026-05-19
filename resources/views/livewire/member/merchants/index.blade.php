<div class="pb-20">
    <div>
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-900 mb-4">{{ __('Partner Merchants') }}</h1>
            <label class="relative block">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="w-full pl-10 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder:text-gray-400 py-3"
                    placeholder="{{ __('Search merchants…') }}"
                />
            </label>
            <p class="text-xs text-base-content/60 mt-4 ml-2 italic text-left">
                {{ $merchants->total() }} {{ $merchants->total() === 1 ? __('merchant') : __('merchants') }} {{ __('found') }}
            </p>
        </div>

        <div class="space-y-4">
            @forelse($merchants as $merchant)
                <a
                    href="{{ route('member.merchants.profile', $merchant->merchant_code) }}"
                    class="block bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 border border-gray-100"
                >
                    <div class="flex items-stretch min-h-[5.5rem]">
                        @if($merchant->logo_url)
                            <img
                                src="{{ $merchant->logo_url }}"
                                alt="{{ $merchant->name }}"
                                class="w-24 shrink-0 object-cover bg-orange-50"
                            >
                        @else
                            <div class="w-24 shrink-0 bg-orange-100 flex items-center justify-center text-orange-500 font-bold text-xl">
                                {{ strtoupper(substr($merchant->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="p-4 flex-1 min-w-0">
                            <h2 class="font-bold text-gray-900 line-clamp-1 text-lg">{{ $merchant->name }}</h2>
                            @if($merchant->description)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $merchant->description }}</p>
                            @endif
                            @php
                                $location = trim(implode(', ', array_filter([
                                    $merchant->city,
                                    $merchant->province,
                                ])));
                            @endphp
                            @if($location)
                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span class="truncate">{{ $location }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 text-center">
                    <img
                        width="64"
                        height="64"
                        src="https://img.icons8.com/cotton/64/online-store.png"
                        alt=""
                        class="mx-auto mb-4 opacity-60"
                    >
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No merchants yet') }}</h3>
                    <p class="text-sm text-gray-500">
                        @if($search)
                            {{ __('Try a different search.') }}
                        @else
                            {{ __('Check back later for partner merchants in Hope Village.') }}
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if($merchants->hasPages())
            <div class="mt-6">
                {{ $merchants->links() }}
            </div>
        @endif
    </div>
</div>
