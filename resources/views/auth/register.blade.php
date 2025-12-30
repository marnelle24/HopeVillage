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
                    <x-label class="tracking-wider" for="name" value="{{ 
                        request()->get('lang') === 'bang' ? 'নাম' : 
                        (request()->get('lang') === 'zh' ? '姓名' : 'Name')
                    }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <input id="name" placeholder="{{ 
                    request()->get('lang') === 'bang' ? 'নাম' : 
                    (request()->get('lang') === 'zh' ? '姓名' : 'Name')
                }}" class="mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="whatsapp_number" value="{{ 
                        request()->get('lang') === 'bang' ? 'যোগাযোগ নম্বর' : 
                        (request()->get('lang') === 'zh' ? '联系电话' : 'Mobile Number')
                    }}" />
                </div>
                <div class="relative">
                    <input id="whatsapp_number" 
                           class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                           type="tel" 
                           name="whatsapp_number" 
                           value="{{ old('whatsapp_number') }}" 
                           required
                           autocomplete="tel"
                           placeholder="{{ 
                               request()->get('lang') === 'bang' ? 'যোগাযোগ নম্বর' : 
                               (request()->get('lang') === 'zh' ? '联系电话' : 'Mobile Number')
                           }}" />
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', '+65') }}" />
                </div>
                <p id="whatsapp-validation-message" class="mt-1 text-xs hidden"></p>
                @error('whatsapp_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="email" value="{{ 
                        request()->get('lang') === 'bang' ? 'ইমেল (ঐচ্ছিক)' : 
                        (request()->get('lang') === 'zh' ? '电子邮箱（可选）' : 'Email (Optional)')
                    }}" />
                </div>
                <input id="email" placeholder="{{ 
                    request()->get('lang') === 'bang' ? 'ইমেল (ঐচ্ছিক)' : 
                    (request()->get('lang') === 'zh' ? '电子邮箱（可选）' : 'Email Address (Optional)')
                }}" class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="email" name="email" value="{{ old('email') }}" autocomplete="username" />
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="col-span-1">
                    <div class="flex items-start">
                        <x-label for="fin" value="{{ __('FIN/NIRC') }}" />
                        <span class="ml-1 text-red-500 text-xl">*</span>
                    </div>
                    <div class="relative">
                        <input id="fin" class="block w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="fin" maxlength="4" value="{{ old('fin') }}" required autocomplete="off" 
                            {{-- placeholder="{{ request()->get('lang') === 'bang' ? 'শেষ ৪-সংখ্যার' : (request()->get('lang') === 'zh' ? '最后4位' : 'Last 4-digit')}}"  --}}
                            placeholder="***124X"
                        />
                        <em class="absolute -bottom-4.5 left-0 text-[11px] text-gray-500">Last 4 characters only</em>
                    </div>
                </div>
    
                <div class="col-span-2" x-data="{ typeOfWork: '{{ old('type_of_work', 'Migrant worker') }}' }">
                    <div class="flex items-center">
                        <x-label for="type_of_work" value="{{ 
                            request()->get('lang') === 'bang' ? 'প্রকল্পের ধরণ' : 
                            (request()->get('lang') === 'zh' ? '工作类型' : 'Type of Work')
                        }}" />
                    </div>
                    <select 
                        id="type_of_work" 
                        name="type_of_work" 
                        x-model="typeOfWork"
                        class="block mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="Migrant worker" {{ old('type_of_work', 'Migrant worker') === 'Migrant worker' ? 'selected' : '' }}>{{ 
                            request()->get('lang') === 'bang' ? 'মিরাজ শ্রমিক' : 
                            (request()->get('lang') === 'zh' ? '外劳' : 'Migrant worker')
                        }}</option>
                        <option value="Migrant domestic worker" {{ old('type_of_work') === 'Migrant domestic worker' ? 'selected' : '' }}>{{ 
                            request()->get('lang') === 'bang' ? 'মিরাজ অভিবাসী শ্রমিক' : 
                            (request()->get('lang') === 'zh' ? '外劳' : 'Migrant domestic worker')
                        }}</option>
                        <option value="Others" {{ old('type_of_work') === 'Others' ? 'selected' : '' }}>{{ 
                            request()->get('lang') === 'bang' ? 'অন্যান্য' : 
                            (request()->get('lang') === 'zh' ? '其他' : 'Others')
                        }}</option>
                    </select>
                    
                    <div x-show="typeOfWork === 'Others'" x-cloak x-transition class="mt-2">
                        <input 
                            id="type_of_work_custom" 
                            class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                            type="text" 
                            name="type_of_work_custom" 
                            value="{{ old('type_of_work_custom') }}" 
                            placeholder="{{ request()->get('lang') === 'bang' ? 'আপনার কাজের ধরণ বর্ণনা করুন' : (request()->get('lang') === 'zh' ? '请描述您的工作类型' : 'Specify your type of work') }}"
                            x-bind:required="typeOfWork === 'Others'"
                        />
                        @error('type_of_work_custom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <x-label for="password" value="{{ 
                    request()->get('lang') === 'bang' ? 'নিশ্চিত করুন' : 
                    (request()->get('lang') === 'zh' ? '密码' : 'Password')
                }}" />
                <div class="relative">
                    <input 
                        id="password" 
                        placeholder="{{ 
                            request()->get('lang') === 'bang' ? 'নিশ্চিত করুন' : 
                            (request()->get('lang') === 'zh' ? '密码' : 'Password')
                        }}" 
                        class="block mt-1 w-full rounded-full px-4 py-2 pr-12 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="new-password" 
                    />
                    <button 
                        type="button" 
                        id="togglePassword" 
                        class="absolute right-4 top-[1.40rem] transform -translate-y-1/2 text-gray-500 hover:text-orange-500 focus:outline-none cursor-pointer"
                        onclick="togglePasswordVisibility()"
                        aria-label="Toggle password visibility"
                    >
                        <svg id="eyeIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eyeOffIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-4 mb-8">
                <x-label for="password_confirmation" value="{{ 
                    request()->get('lang') === 'bang' ? 'পাসওয়ার্ড পাসওয়ার্ড' : 
                    (request()->get('lang') === 'zh' ? '确认密码' : 'Confirm Password')
                }}" />
                <div class="relative">
                    <input 
                        id="password_confirmation" 
                        placeholder="{{ 
                            request()->get('lang') === 'bang' ? 'পাসওয়ার্ড পাসওয়ার্ড' : 
                            (request()->get('lang') === 'zh' ? '确认密码' : 'Confirm Password')
                        }}" 
                        class="block mt-1 w-full rounded-full px-4 py-2 pr-12 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" 
                    />
                    <button 
                        type="button" 
                        id="togglePasswordConfirmation" 
                        class="absolute right-4 top-[1.40rem] transform -translate-y-1/2 text-gray-500 hover:text-orange-500 focus:outline-none cursor-pointer"
                        onclick="togglePasswordConfirmationVisibility()"
                        aria-label="Toggle password confirmation visibility"
                    >
                        <svg id="eyeIconConfirmation" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eyeOffIconConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
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
                                @if(request()->get('lang') === 'en' || request()->get('lang') === '' || !request()->get('lang'))
                                    {!! __('By selecting ths option, your data can be stored for future forms. You can learn more about how we handle your personal information and your rights by reviewing our :privacy_policy', [
                                        'privacy_policy' => '<a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                @elseif(request()->get('lang') === 'bang')
                                    {!! __('এই বিকল্প বাছাই করলে, আপনার তথ্য ভবিষ্যতের ফর্মের জন্য সংরক্ষণ করা যাবে। আপনি আমাদের আপনার ব্যক্তিগত তথ্য এবং আপনার অধিকার সম্পর্কে আরও জানতে আমাদের গোপনীয়তা নীতি দেখুন :privacy_policy', [
                                        'privacy_policy' => '<a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">'.__('গোপনীয়তা নীতি').'</a>',
                                    ]) !!}
                                @elseif(request()->get('lang') === 'zh')
                                    {!! __('通过选择此选项，您的数据可以存储在未来表单中。您可以通过查看我们的隐私政策了解更多关于我们如何处理您的个人信息和您的权利。:privacy_policy', [
                                        'privacy_policy' => '<a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">'.__('隐私政策').'</a>',
                                    ]) !!}
                                @endif
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

            <div class="flex items-center justify-center my-6">
                <button 
                    type="submit"
                    x-bind:disabled="submitting"
                    :class="submitting ? 'opacity-80 cursor-not-allowed bg-gray-400 hover:bg-gray-400 active:bg-gray-400 focus:bg-gray-400 disabled:bg-gray-400 disabled:hover:bg-gray-400 disabled:active:bg-gray-400 disabled:focus:bg-gray-400 disabled:opacity-80 disabled:cursor-not-allowed' : ''"
                    class="cursor-pointer ms-4 w-3/4 flex justify-center py-4 text-white bg-orange-500 hover:bg-orange-600 duration-300 transition-all rounded-full"
                >
                    <span x-show="!submitting">{{ 
                        request()->get('lang') === 'bang' ? 'নিবন্ধন করুন' : 
                        (request()->get('lang') === 'zh' ? '注册' : 'Register')
                    }}</span>
                    <span x-show="submitting" x-cloak>{{ 
                        request()->get('lang') === 'bang' ? 'নিবন্ধন করছে...' : 
                        (request()->get('lang') === 'zh' ? '注册中...' : 'Registering...')
                    }}</span>
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
                    validationMsg.textContent = 'Checking Mobile Number formats...';
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
                            validationMsg.textContent = '✓ Mobile Number format is valid.';
                            validationMsg.classList.add('text-green-600');
                            validationMsg.classList.remove('text-yellow-500', 'text-red-500', 'hidden');
                        } else {
                            validationMsg.textContent = '⚠ Mobile Number format is invalid.';
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

        // Password visibility toggle functions
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            }
        }

        function togglePasswordConfirmationVisibility() {
            const passwordInput = document.getElementById('password_confirmation');
            const eyeIcon = document.getElementById('eyeIconConfirmation');
            const eyeOffIcon = document.getElementById('eyeOffIconConfirmation');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            }
        }
    </script>
    @endpush
</x-guest-layout>
