<x-guest-layout>
    <x-authentication-card x-cloak>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label class="tracking-wider" for="name" value="{{ __('Name') }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <input id="name" placeholder="Complete Name" class="mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="whatsapp_number" value="{{ __('Mobile Number') }}" />
                </div>
                <div class="relative">
                    <input id="whatsapp_number" 
                           class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                           type="tel" 
                           name="whatsapp_number" 
                           value="{{ old('whatsapp_number') }}" 
                           required
                           autocomplete="tel"
                           placeholder="Enter phone number" />
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', '+65') }}" />
                </div>
                {{-- <p class="mt-1 text-xs text-gray-500">
                    If the number is on WhatsApp, we'll send your verification code there too.
                </p> --}}
                <p id="whatsapp-validation-message" class="mt-1 text-xs hidden"></p>
                @error('whatsapp_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <span class="ml-1 text-xs text-gray-500">(Optional)</span>
                </div>
                <input id="email" placeholder="Email Address (Optional)" class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="email" name="email" :value="old('email')" autocomplete="username" />
                {{-- <p class="mt-1 text-xs text-gray-500">
                    If not provided, an email will be generated based on your WhatsApp number.
                </p> --}}
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="col-span-1">
                    <div class="flex items-start">
                        <x-label for="fin" value="{{ __('FIN/NIRC') }}" />
                        <span class="ml-1 text-red-500 text-xl">*</span>
                    </div>
                    <input id="fin" class="block w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="fin" maxlength="4" :value="old('fin')" required autocomplete="off" placeholder="Last 4-digits" />
                    {{-- <p class="mt-1 text-xs text-gray-500">
                        Format: F/G/M + 7 digits + checksum letter.
                    </p> --}}
                </div>
    
                <div class="col-span-2" x-data="{ typeOfWork: '{{ old('type_of_work', 'Migrant worker') }}' }">
                    <div class="flex items-center">
                        <x-label for="type_of_work" value="{{ __('Type of Work') }}" />
                    </div>
                    <select 
                        id="type_of_work" 
                        name="type_of_work" 
                        x-model="typeOfWork"
                        class="block mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="Migrant worker" {{ old('type_of_work', 'Migrant worker') === 'Migrant worker' ? 'selected' : '' }}>Migrant worker</option>
                        <option value="Migrant domestic worker" {{ old('type_of_work') === 'Migrant domestic worker' ? 'selected' : '' }}>Migrant domestic worker</option>
                        <option value="Others" {{ old('type_of_work') === 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                    
                    <div x-show="typeOfWork === 'Others'" x-cloak x-transition class="mt-2">
                        <input 
                            id="type_of_work_custom" 
                            class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                            type="text" 
                            name="type_of_work_custom" 
                            value="{{ old('type_of_work_custom') }}" 
                            placeholder="Please specify your type of work"
                            x-bind:required="typeOfWork === 'Others'"
                        />
                        @error('type_of_work_custom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
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
                <input id="password" placeholder="Password" class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <input id="password_confirmation" placeholder="Confirm Password" class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-start">
                            <x-checkbox 
                                name="terms" 
                                id="terms" 
                                class="focus:border-orange-500 focus:ring-orange-500"
                                required 
                            />

                            <div class="ms-2">
                                {!! __('By selecting ths option, your data can be stored for future forms. You can learn more about how we handle your personal information and your rights by reviewing our :privacy_policy', [
                                        'privacy_policy' => '<a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <!-- Google reCAPTCHA -->
            @if(config('services.recaptcha.site_key'))
                <div class="mt-4 w-full">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="flex items-center justify-center mt-12">
                <button 
                    type="submit"
                    x-bind:disabled="submitting"
                    :class="submitting ? 'opacity-80 cursor-not-allowed bg-gray-400 hover:bg-gray-400 active:bg-gray-400 focus:bg-gray-400 disabled:bg-gray-400 disabled:hover:bg-gray-400 disabled:active:bg-gray-400 disabled:focus:bg-gray-400 disabled:opacity-80 disabled:cursor-not-allowed' : ''"
                    class="cursor-pointer ms-4 w-3/4 flex justify-center py-4 text-white bg-orange-500 hover:bg-orange-600 duration-300 transition-all rounded-full"
                >
                    <span x-show="!submitting">{{ __('Register') }}</span>
                    <span x-show="submitting" x-cloak>{{ __('Registering...') }}</span>
                </button>
            </div>
        </form>
    </x-authentication-card>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.7/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }
        .iti__flag-container {
            z-index: 10;
        }
        /* Ensure the input takes full width when separateDialCode is true */
        .iti__selected-flag {
            z-index: 4;
            padding: 0 8px 0 8px;
        }
        /* Make the dial code more visible */
        .iti__selected-dial-code {
            font-weight: 600;
            color: #374151;
            padding: 0 4px;
        }
        /* Ensure dial code is visible in dropdown */
        .iti__country-list .iti__dial-code {
            color: #6b7280;
            margin-left: 6px;
        }
        /* Show dial code prominently in selected flag area */
        .iti__flag-container + .iti__selected-dial-code {
            display: inline-block !important;
        }
        /* Disable dropdown when only one country is available */
        .iti--single-country .iti__selected-flag {
            pointer-events: none;
            cursor: default;
            border-top-left-radius: 32px;
            border-bottom-left-radius: 32px;
            color:#fff;
            background: #ffc28b;
        }
        .iti--single-country .iti__flag-container {
            pointer-events: none;
            cursor: default;
        }
    </style>
    @endpush

    @push('scripts')
    @if(config('services.recaptcha.site_key'))
        <!-- Google reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.7/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('#whatsapp_number');
            if (!input) return;

            const iti = window.intlTelInput(input, {
                initialCountry: "sg", // Singapore as default
                onlyCountries: ["sg"], // Only allow Singapore country code
                separateDialCode: true,
                showSelectedDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.7/build/js/utils.js",
            });

            // Disable dropdown since only Singapore is available
            const itiContainer = input.closest('.iti');
            const flagContainer = itiContainer.querySelector('.iti__selected-flag');
            
            // Add class to indicate single country mode
            itiContainer.classList.add('iti--single-country');
            
            // Prevent dropdown from opening by blocking click events
            if (flagContainer) {
                flagContainer.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                // Also prevent mousedown which might trigger the dropdown
                flagContainer.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
            }

            // Set initial value if there's an old input
            @if(old('whatsapp_number'))
                const oldValue = "{{ old('whatsapp_number') }}";
                if (oldValue) {
                    iti.setNumber(oldValue);
                }
            @endif

            // Update hidden country code field and form field on change
            function updateCountryCode() {
                const countryData = iti.getSelectedCountryData();
                if (countryData) {
                    const dialCode = '+' + countryData.dialCode;
                    document.getElementById('country_code').value = dialCode;
                    // Ensure dial code is visible
                    const selectedFlag = input.parentElement.querySelector('.iti__selected-flag');
                    if (selectedFlag) {
                        const dialCodeElement = selectedFlag.querySelector('.iti__selected-dial-code');
                        if (dialCodeElement) {
                            dialCodeElement.textContent = dialCode;
                        }
                    }
                    // Update the main input to include full number with country code if number exists
                    const currentValue = input.value.replace(/^\+?\d+\s*/, ''); // Remove any existing country code
                    if (currentValue) {
                        const fullNumber = iti.getNumber();
                        if (fullNumber) {
                            input.value = fullNumber;
                        }
                    }
                }
            }

            // Initialize country code display on load
            updateCountryCode();

            input.addEventListener('countrychange', updateCountryCode);
            input.addEventListener('input', function() {
                // Validate phone number format on input
                if (iti.isValidNumber()) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                    updateCountryCode();
                } else if (input.value.length > 0) {
                    input.classList.remove('border-green-500');
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500', 'border-green-500');
                }
            });

            // Validate WhatsApp on blur (optional - async check)
            input.addEventListener('blur', async function() {
                const fullNumber = iti.getNumber();
                const validationMsg = document.getElementById('whatsapp-validation-message');
                
                if (fullNumber && iti.isValidNumber()) {
                    validationMsg.classList.remove('hidden');
                    validationMsg.textContent = 'Checking WhatsApp status...';
                    validationMsg.classList.remove('text-red-500', 'text-green-500', 'text-yellow-500');
                    
                    try {
                        const response = await fetch('{{ route("api.check-whatsapp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ phone: fullNumber })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        
                        const data = await response.json();
                        
                        // If service is not enabled (null response), hide the message
                        if (data.is_whatsapp === null) {
                            validationMsg.textContent = '';
                            validationMsg.classList.add('hidden');
                        } else if (data.is_whatsapp === true) {
                            validationMsg.textContent = '✓ This number is registered on WhatsApp';
                            validationMsg.classList.add('text-green-600');
                            validationMsg.classList.remove('text-yellow-500', 'text-red-500', 'hidden');
                        } else {
                            validationMsg.textContent = '⚠ This number may not be registered on WhatsApp';
                            validationMsg.classList.add('text-yellow-500');
                            validationMsg.classList.remove('text-green-600', 'text-red-500', 'hidden');
                        }
                    } catch (error) {
                        // Silently fail - validation will happen on server side
                        validationMsg.textContent = '';
                        validationMsg.classList.add('hidden');
                    }
                } else {
                    validationMsg.textContent = '';
                    validationMsg.classList.add('hidden');
                }
            });

            // Before form submit, ensure the full number with country code is set
            const form = input.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const fullNumber = iti.getNumber();
                    if (fullNumber && iti.isValidNumber()) {
                        input.value = fullNumber;
                        updateCountryCode();
                    } else {
                        e.preventDefault();
                        alert('Please enter a valid phone number.');
                        return false;
                    }
                });
            }
        });
    </script>
    @endpush
</x-guest-layout>
