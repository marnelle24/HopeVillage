<div class="pb-20">
    <div>
        <a href="{{ route('member.merchants.index') }}" class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 font-medium mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('Back to Merchants') }}
        </a>

        <article class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
            @if($merchant->logo_url)
                <div class="w-full h-40 overflow-hidden bg-orange-50 flex items-center justify-center">
                    <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-full h-32 bg-orange-100 flex items-center justify-center text-orange-500 font-bold text-4xl">
                    {{ strtoupper(substr($merchant->name, 0, 2)) }}
                </div>
            @endif

            <div class="p-4 md:p-6 space-y-4">
                <h1 class="text-xl font-bold text-gray-900">{{ $merchant->name }}</h1>

                @if($merchant->description)
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $merchant->description }}</p>
                @endif

                <dl class="space-y-3 text-sm">
                    @php
                        $fullAddress = trim(implode(', ', array_filter([
                            $merchant->address,
                            $merchant->city,
                            $merchant->province,
                            $merchant->postal_code,
                        ])));
                    @endphp
                    @if($fullAddress)
                        <div>
                            <dt class="text-gray-500 font-medium">{{ __('Address') }}</dt>
                            <dd class="text-gray-800 mt-0.5">{{ $fullAddress }}</dd>
                        </div>
                    @endif
                    @if($merchant->phone)
                        <div>
                            <dt class="text-gray-500 font-medium">{{ __('Phone') }}</dt>
                            <dd class="mt-0.5">
                                <a href="tel:{{ $merchant->phone }}" class="text-orange-600 hover:text-orange-700">{{ $merchant->phone }}</a>
                            </dd>
                        </div>
                    @endif
                    @if($merchant->email)
                        <div>
                            <dt class="text-gray-500 font-medium">{{ __('Email') }}</dt>
                            <dd class="mt-0.5">
                                <a href="mailto:{{ $merchant->email }}" class="text-orange-600 hover:text-orange-700 break-all">{{ $merchant->email }}</a>
                            </dd>
                        </div>
                    @endif
                    @if($merchant->website)
                        <div>
                            <dt class="text-gray-500 font-medium">{{ __('Website') }}</dt>
                            <dd class="mt-0.5">
                                <a
                                    href="{{ str_starts_with($merchant->website, 'http') ? $merchant->website : 'https://'.$merchant->website }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-orange-600 hover:text-orange-700 break-all"
                                >
                                    {{ $merchant->website }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>

                <a
                    href="{{ route('member.vouchers') }}"
                    class="flex items-center justify-center gap-2 w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-full transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    {{ __('Browse Vouchers') }}
                </a>
            </div>
        </article>
    </div>
</div>
