<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="fin" value="{{ __('FIN (Foreign Identification Number)') }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <x-input id="fin" class="block mt-1 w-full" type="text" name="fin" :value="old('fin')" required autocomplete="off" placeholder="F1234567X" />
                {{-- <p class="mt-1 text-xs text-gray-500">
                    Format: F/G/M + 7 digits + checksum letter.
                </p> --}}
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="name" value="{{ __('Name') }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="whatsapp_number" value="{{ __('Contact Number (WhatsApp)') }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <x-input id="whatsapp_number" class="block mt-1 w-full" type="text" name="whatsapp_number" :value="old('whatsapp_number')" autocomplete="tel" placeholder="+65XXXXXXXX" />
                <p class="mt-1 text-xs text-gray-500">
                    If the number is on WhatsApp, we’ll send your verification code there too.
                </p>
            </div>

            {{-- <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-label for="age" value="{{ __('Age') }}" />
                    <x-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" placeholder="Optional" min="0" max="120" />
                </div>
                <div>
                    <x-label for="gender" value="{{ __('Gender') }}" />
                    <x-input id="gender" class="block mt-1 w-full" type="text" name="gender" :value="old('gender')" placeholder="Optional" />
                </div>
            </div> --}}

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
