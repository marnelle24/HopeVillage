<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="Hope Village Logo" class="w-32">
        </x-slot>

        <div class="mb-6 mt-8 text-sm text-gray-600">
            {{ 
                request()->get('lang') === 'bang' ? 'পাসওয়ার্ড ভুলে গেছেন? আপনার নতুন পাসওয়ার্ড কীভাবে গ্রহণ করতে চান তা নির্বাচন করুন।' : 
                (request()->get('lang') === 'zh' ? '忘记密码？选择您希望如何接收新密码。' : 'Forgot your password? Choose how you want to receive your new password.') 
            }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="/forgot-password" id="password-reset-form" data-lang="{{ request()->get('lang', '') }}">
            @csrf

            <!-- Reset Method Selection -->
            <div class="mb-4">
                <label class="block text-lg leading-tight font-medium text-gray-700 mb-4">
                    {{ 
                        request()->get('lang') === 'bang' ? 'আপনি কীভাবে আপনার নতুন পাসওয়ার্ড গ্রহণ করতে চান?' : 
                        (request()->get('lang') === 'zh' ? '您希望如何接收新密码？' : 'How would you like to receive your new password?') 
                    }}
                </label>
                <div class="space-y-2">
                    {{-- <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="reset_method" 
                            value="whatsapp"  
                            class="mr-2"
                            onchange="toggleInputFields()"
                        >
                        <span class="text-sm text-gray-700">{{ 
                            request()->get('lang') === 'bang' ? 'হোয়াটসঅ্যাপ নম্বর' : 
                            (request()->get('lang') === 'zh' ? 'WhatsApp 号码' : 'WhatsApp Number') 
                        }}</span>
                    </label> --}}
                    {{-- <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="reset_method" 
                            value="sms" 
                            class="mr-2"
                            onchange="toggleInputFields()"
                        >
                        <span class="text-sm text-gray-700">{{ 
                            request()->get('lang') === 'bang' ? 'এসএমএস' : 
                            (request()->get('lang') === 'zh' ? '短信' : 'SMS') 
                        }}</span>
                    </label> --}}
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="reset_method" 
                            value="email" 
                            class="mr-2"
                            checked
                            onchange="toggleInputFields()"
                        >
                        <span class="text-sm text-gray-700">{{ 
                            request()->get('lang') === 'bang' ? 'ইমেল ঠিকানা' : 
                            (request()->get('lang') === 'zh' ? '电子邮件地址' : 'Email Address') 
                        }}</span>
                    </label>
                </div>
            </div>

            <!-- Email Input -->
            <div class="hidden" id="email-field">
                <x-label for="email" value="{{ 
                    request()->get('lang') === 'bang' ? 'ইমেল ঠিকানা' : 
                    (request()->get('lang') === 'zh' ? '电子邮件地址' : 'Email Address') 
                }}" />
                <x-input 
                    id="email" 
                    class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                    placeholder="{{ 
                        request()->get('lang') === 'bang' ? 'ইমেল ঠিকানা' : 
                        (request()->get('lang') === 'zh' ? '电子邮件地址' : 'Email Address') 
                    }}"
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    autocomplete="username" 
                />
            </div>

            <!-- WhatsApp Input -->
            <div class="block" id="whatsapp-field">
                <x-label for="whatsapp_number" value="{{ 
                    request()->get('lang') === 'bang' ? 'হোয়াটসঅ্যাপ নম্বর' : 
                    (request()->get('lang') === 'zh' ? 'WhatsApp 号码' : 'WhatsApp Number') 
                }}" />
                <div class="relative">
                    <input 
                        id="whatsapp_number" 
                        class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="tel" 
                        name="whatsapp_number" 
                        value="{{ old('whatsapp_number') }}" 
                        placeholder="{{ 
                            request()->get('lang') === 'bang' ? 'যোগাযোগ নম্বর' : 
                            (request()->get('lang') === 'zh' ? '联系电话' : 'Mobile Number')
                        }}"
                        required
                        autofocus 
                        autocomplete="tel" 
                    />
                    <input type="hidden" name="whatsapp_country_code" id="whatsapp_country_code" value="{{ old('whatsapp_country_code', '+65') }}" />
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    {{ 
                        request()->get('lang') === 'bang' ? 'আন্তর্জাতিক ফরম্যাটে আপনার হোয়াটসঅ্যাপ নম্বর লিখুন (যেমন: +6591234567)' : 
                        (request()->get('lang') === 'zh' ? '以国际格式输入您的 WhatsApp 号码（例如：+6591234567）' : 'Enter your WhatsApp number in international format (e.g., +6591234567)') 
                    }}
                </p>
            </div>

            <!-- SMS Input -->
            <div class="hidden" id="sms-field">
                <x-label for="sms_number" value="{{ 
                    request()->get('lang') === 'bang' ? 'এসএমএস নম্বর' : 
                    (request()->get('lang') === 'zh' ? '短信号码' : 'SMS Number') 
                }}" />
                <div class="relative">
                    <input 
                        id="sms_number" 
                        class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="tel" 
                        name="sms_number" 
                        value="{{ old('sms_number') }}" 
                        placeholder="{{ 
                            request()->get('lang') === 'bang' ? 'যোগাযোগ নম্বর' : 
                            (request()->get('lang') === 'zh' ? '联系电话' : 'Mobile Number')
                        }}"
                        autocomplete="tel" 
                    />
                    <input type="hidden" name="sms_country_code" id="sms_country_code" value="{{ old('sms_country_code', '+65') }}" />
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    {{ 
                        request()->get('lang') === 'bang' ? 'আন্তর্জাতিক ফরম্যাটে আপনার ফোন নম্বর লিখুন (যেমন: +6591234567)' : 
                        (request()->get('lang') === 'zh' ? '以国际格式输入您的电话号码（例如：+6591234567）' : 'Enter your phone number in international format (e.g., +6591234567)') 
                    }}
                </p>
            </div>

            <div class="flex items-center justify-end mt-4">
                <button 
                    type="submit" 
                    id="submit-btn"
                    class="w-full flex justify-center items-center gap-2 py-4 text-white bg-orange-500 hover:bg-orange-600 active:bg-orange-700 focus:bg-orange-600 rounded-full disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                >
                    <span id="button-text">
                        {{ 
                            request()->get('lang') === 'bang' ? 'পাসওয়ার্ড রিসেট পাঠান' : 
                            (request()->get('lang') === 'zh' ? '发送密码重置' : 'Send Password Reset') 
                        }}
                    </span>
                    <svg id="loading-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-4 text-left">
            <a href="{{ route('login') }}" class="text-sm text-orange-600 hover:text-orange-500 underline">
                {{ 
                    request()->get('lang') === 'bang' ? 'লগইনে ফিরে যান' : 
                    (request()->get('lang') === 'zh' ? '返回登录' : 'Back to Login') 
                }}
            </a>
        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.7/build/js/intlTelInput.min.js"></script>
    <script>
        // Initialize intl-tel-input instances
        let whatsappIti = null;
        let smsIti = null;

        function initIntlTelInput(inputId, countryCodeId) {
            const input = document.getElementById(inputId);
            if (!input) return null;
            
            // Check if already initialized
            if (input.closest('.iti')) {
                return input.closest('.iti').intlTelInputInstance || null;
            }

            const iti = window.intlTelInput(input, {
                initialCountry: "sg", // Singapore as default
                onlyCountries: ["sg"], // Only allow Singapore country code
                separateDialCode: true,
                showSelectedDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.7/build/js/utils.js",
            });

            // Store instance reference
            input.closest('.iti').intlTelInputInstance = iti;

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
                
                flagContainer.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
            }

            // Set initial value if there's an old input
            @if(old('whatsapp_number') || old('sms_number'))
                const oldValue = inputId === 'whatsapp_number' ? "{{ old('whatsapp_number') }}" : "{{ old('sms_number') }}";
                if (oldValue) {
                    iti.setNumber(oldValue);
                }
            @endif

            // Update hidden country code field on change
            function updateCountryCode() {
                const countryData = iti.getSelectedCountryData();
                if (countryData) {
                    const dialCode = '+' + countryData.dialCode;
                    const countryCodeInput = document.getElementById(countryCodeId);
                    if (countryCodeInput) {
                        countryCodeInput.value = dialCode;
                    }
                }
            }

            // Initialize country code display on load
            updateCountryCode();
            input.addEventListener('countrychange', updateCountryCode);
            input.addEventListener('input', updateCountryCode);

            return iti;
        }

        function toggleInputFields() {
            const resetMethod = document.querySelector('input[name="reset_method"]:checked').value;
            const emailField = document.getElementById('email-field');
            const whatsappField = document.getElementById('whatsapp-field');
            const smsField = document.getElementById('sms-field');
            const emailInput = document.getElementById('email');
            const whatsappInput = document.getElementById('whatsapp_number');
            const smsInput = document.getElementById('sms_number');

            // Hide all fields first
            emailField.classList.add('hidden');
            whatsappField.classList.add('hidden');
            smsField.classList.add('hidden');
            
            // Remove required from all inputs
            emailInput.removeAttribute('required');
            whatsappInput.removeAttribute('required');
            smsInput.removeAttribute('required');

            // Show and set required for selected method
            if (resetMethod === 'email') {
                emailField.classList.remove('hidden');
                emailInput.setAttribute('required', 'required');
            } else if (resetMethod === 'sms') {
                smsField.classList.remove('hidden');
                smsInput.setAttribute('required', 'required');
                // Initialize intl-tel-input for SMS if not already initialized
                if (!smsIti) {
                    setTimeout(() => {
                        smsIti = initIntlTelInput('sms_number', 'sms_country_code');
                    }, 100);
                }
            } else {
                // whatsapp (default)
                whatsappField.classList.remove('hidden');
                whatsappInput.setAttribute('required', 'required');
                // Initialize intl-tel-input for WhatsApp if not already initialized
                if (!whatsappIti) {
                    setTimeout(() => {
                        whatsappIti = initIntlTelInput('whatsapp_number', 'whatsapp_country_code');
                    }, 100);
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleInputFields();
            
            // Initialize intl-tel-input for visible field (WhatsApp by default)
            setTimeout(() => {
                whatsappIti = initIntlTelInput('whatsapp_number', 'whatsapp_country_code');
            }, 100);
            
            // Handle form submission
            const form = document.getElementById('password-reset-form');
            const submitBtn = document.getElementById('submit-btn');
            const buttonText = document.getElementById('button-text');
            const loadingSpinner = document.getElementById('loading-spinner');
            
            form.addEventListener('submit', function(e) {
                // Get full number with country code before submit
                const resetMethod = document.querySelector('input[name="reset_method"]:checked').value;
                
                if (resetMethod === 'whatsapp' && whatsappIti) {
                    const fullNumber = whatsappIti.getNumber();
                    if (fullNumber) {
                        document.getElementById('whatsapp_number').value = fullNumber;
                    }
                } else if (resetMethod === 'sms' && smsIti) {
                    const fullNumber = smsIti.getNumber();
                    if (fullNumber) {
                        document.getElementById('sms_number').value = fullNumber;
                    }
                }
                
                // Get loading text based on language
                const lang = form.getAttribute('data-lang') || new URLSearchParams(window.location.search).get('lang') || '';
                let loadingText = 'Sending...';
                if (lang === 'bang') {
                    loadingText = 'পাঠানো হচ্ছে...';
                } else if (lang === 'zh') {
                    loadingText = '发送中...';
                }
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600', 'active:bg-orange-700', 'focus:bg-orange-600');
                submitBtn.classList.add('bg-gray-400');
                buttonText.textContent = loadingText;
                loadingSpinner.classList.remove('hidden');
            });
        });
    </script>
    @endpush
</x-guest-layout>
