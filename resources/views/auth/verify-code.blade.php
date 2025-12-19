<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded p-3">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <div class="mb-4 text-sm text-gray-600">
            <p>Please enter the 6-digit verification code we sent to:</p>
            <ul class="mt-2 list-disc list-inside">
                <li><strong>Email:</strong> {{ $email }}</li>
                @if(!empty($whatsapp))
                    <li><strong>WhatsApp:</strong> {{ $whatsapp }}</li>
                @endif
            </ul>
        </div>

        <form method="POST" action="{{ route('verification.code.verify') }}">
            @csrf

            <div>
                <x-label for="code" value="{{ __('Verification Code') }}" />
                <x-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autocomplete="one-time-code" inputmode="numeric" placeholder="123456" />
            </div>

            <div class="flex items-center justify-between mt-10">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Verify
                </button>
                <a href="#" onclick="document.getElementById('resend-form').submit(); return false;" class="underline text-sm text-orange-500 hover:text-orange-600">
                    Resend code
                </a>
            </div>
        </form>

        <form id="resend-form" method="POST" action="{{ route('verification.code.resend') }}" class="hidden">
            @csrf
        </form>
    </x-authentication-card>
</x-guest-layout>
