<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" placeholder="Email Address" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                {{-- <x-input id="email" class="block mt-1 w-full" type="email" name="email" value="marnelle24@gmail.com" required autofocus autocomplete="username" /> --}}
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" placeholder="Password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                {{-- <x-input id="password" class="block mt-1 w-full" type="password" name="password" value="123123123" required autocomplete="current-password" /> --}}
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4">
                <x-button class="w-3/4 flex justify-center py-4 text-white bg-orange-500 hover:bg-orange-600 active:bg-orange-700 focus:bg-orange-600 rounded-full">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
