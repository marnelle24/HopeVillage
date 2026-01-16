<x-guest-layout>
    <x-authentication-card x-cloak>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>

        <x-validation-errors class="mb-4" />

        @php
            $lang = request()->get('lang', 'en');
        @endphp

        <form method="POST" action="{{ route('register') }}" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            {{-- Preserve referral code if present in URL --}}
            @if(request()->has('ref'))
                <input type="hidden" name="ref" value="{{ request()->get('ref') }}">
            @endif

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label class="tracking-wider" for="name" value="{{ 
                        match($lang) {
                            'bang' => 'নাম',
                            'zh' => '姓名',
                            'ta' => 'பெயர்',
                            default => 'Name',
                        }
                    }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <input id="name" placeholder="{{ 
                    match($lang) {
                        'bang' => 'নাম',
                        'zh' => '姓名',
                        'ta' => 'பெயர்',
                        default => 'Name',
                    }
                }}" class="mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="whatsapp_number" value="{{ 
                        match($lang) {
                            'bang' => 'যোগাযোগ নম্বর',
                            'zh' => '联系电话',
                            'ta' => 'மொபைல் எண்',
                            default => 'Mobile Number',
                        }
                    }}" />
                </div>
                <div class="relative">
                    <input id="whatsapp_number" 
                           class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                           type="tel" 
                           name="whatsapp_number" 
                           value="{{ old('whatsapp_number') }}" 
                           required
                           maxlength="9"
                           {{-- autocomplete="tel" --}}
                           placeholder="{{ 
                               match($lang) {
                                   'bang' => 'যোগাযোগ নম্বর',
                                   'zh' => '联系电话',
                                   'ta' => 'மொபைல் எண்',
                                   default => 'Mobile Number',
                               }
                           }}" />
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', '+65') }}" />
                </div>
                @error('whatsapp_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <div class="flex items-center">
                    <x-label for="email" value="{{ 
                        match($lang) {
                            'bang' => 'ইমেল (ঐচ্ছিক)',
                            'zh' => '电子邮箱（可选）',
                            'ta' => 'மின்னஞ்சல் (விரும்பினால்)',
                            default => 'Email (Optional)',
                        }
                    }}" />
                </div>
                <input id="email" placeholder="{{ 
                    match($lang) {
                        'bang' => 'ইমেল (ঐচ্ছিক)',
                        'zh' => '电子邮箱（可选）',
                        'ta' => 'மின்னஞ்சல் (விரும்பினால்)',
                        default => 'Email Address (Optional)',
                    }
                }}" class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="email" name="email" value="{{ old('email') }}" autocomplete="username" />
            </div>

            <div class="mt-4">
                <div class="flex items-start">
                    <x-label for="fin" value="{{ 
                        match($lang) {
                            'bang' => 'FIN/NRIC',
                            'zh' => 'FIN/NRIC',
                            'ta' => 'FIN/NRIC',
                            default => 'FIN/NRIC',
                        }
                    }}" />
                    <span class="ml-1 text-red-500 text-xl">*</span>
                </div>
                <div class="relative">
                    <input id="fin" class="block w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="text" name="fin" maxlength="4" value="{{ old('fin') }}" required autocomplete="off" 
                        placeholder="{{ 
                            match($lang) {
                                'bang' => 'শেষ ৪-অক্ষরর মাত্র (e.g. 124X)',
                                'zh' => '最后4个字符 (e.g. 124X)',
                                'ta' => 'கடைசி 4 எழுத்துகள் (எ.கா. 124X)',
                                default => 'Last 4-characters (e.g. 124X)',
                            }
                        }}" 
                    />
                </div>
            </div>

            <div class="mt-4" x-data="{ typeOfWork: '{{ old('type_of_work', 'Migrant worker') }}' }">
                <div class="flex items-center">
                    <x-label for="type_of_work" value="{{ 
                        match($lang) {
                            'bang' => 'প্রকল্পের ধরণ',
                            'zh' => '工作类型',
                            'ta' => 'வேலை வகை',
                            default => 'Type of Work',
                        }
                    }}" />
                </div>
                <select 
                    id="type_of_work" 
                    name="type_of_work" 
                    x-model="typeOfWork"
                    class="block mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                >
                    <option value="Migrant worker" {{ old('type_of_work', 'Migrant worker') === 'Migrant worker' ? 'selected' : '' }}>
                        {{ 
                            match($lang) {
                                'bang' => 'মিরাজ শ্রমিক',
                                'zh' => '外劳',
                                'ta' => 'புலம்பெயர்ந்த தொழிலாளி',
                                default => 'Migrant worker',
                            }
                        }}
                    </option>
                    <option value="Migrant domestic worker" {{ old('type_of_work') === 'Migrant domestic worker' ? 'selected' : '' }}>
                        {{ 
                            match($lang) {
                                'bang' => 'মিরাজ অভিবাসী শ্রমিক',
                                'zh' => '外劳',
                                'ta' => 'புலம்பெயர்ந்த வீட்டுப் பணியாளர்',
                                default => 'Migrant domestic worker',
                            }
                        }}
                    </option>
                    <option value="Others" {{ old('type_of_work') === 'Others' ? 'selected' : '' }}>
                        {{ 
                            match($lang) {
                                'bang' => 'অন্যান্য',
                                'zh' => '其他',
                                'ta' => 'மற்றவர்கள்',
                                default => 'Others',
                            }
                        }}
                    </option>
                </select>
                
                <div x-show="typeOfWork === 'Others'" x-cloak x-transition class="mt-2">
                    <input 
                        id="type_of_work_custom" 
                        class="block mt-1 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="text" 
                        name="type_of_work_custom" 
                        value="{{ old('type_of_work_custom') }}" 
                        placeholder="{{ 
                            match($lang) {
                                'bang' => 'আপনার কাজের ধরণ বর্ণনা করুন',
                                'zh' => '请描述您的工作类型',
                                'ta' => 'உங்கள் வேலை வகையை விவரிக்கவும்',
                                default => 'Specify your type of work',
                            }
                        }}"
                        x-bind:required="typeOfWork === 'Others'"
                    />
                    @error('type_of_work_custom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-10">
                <x-label for="password" value="{{ 
                    match($lang) {
                        'bang' => 'নিশ্চিত করুন',
                        'zh' => '密码',
                        'ta' => 'கடவுச்சொல்',
                        default => 'Password',
                    }
                }}" />
                <div class="relative">
                    <input 
                        id="password" 
                        placeholder="{{ 
                            match($lang) {
                                'bang' => 'নিশ্চিত করুন',
                                'zh' => '密码',
                                'ta' => 'கடவுச்சொல்',
                                default => 'Password',
                            }
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
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ 
                    match($lang) {
                        'bang' => 'পাসওয়ার্ড পাসওয়ার্ড',
                        'zh' => '确认密码',
                        'ta' => 'கடவுச்சொல்லை உறுதிப்படுத்தவும்',
                        default => 'Confirm Password',
                    }
                }}" />
                <div class="relative">
                    <input 
                        id="password_confirmation" 
                        placeholder="{{ 
                            match($lang) {
                                'bang' => 'পাসওয়ার্ড পাসওয়ার্ড',
                                'zh' => '确认密码',
                                'ta' => 'கடவுச்சொல்லை உறுதிப்படுத்தவும்',
                                default => 'Confirm Password',
                            }
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

            <div class="mt-4 mb-8 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                <p class="text-xs font-semibold text-gray-700 mb-1"></p>
                    {{ match($lang) {
                        'bang' => 'পাসওয়ার্ডের প্রয়োজনীয়তা:',
                        'zh' => '密码要求：',
                        'ta' => 'கடவுச்சொல் தேவை:',
                        default => 'Password Requirements:',
                    } }}
                </p>
                <ul class="text-xs text-gray-600 space-y-0.5 list-disc list-inside">
                    <li>
                        {{ match($lang) {
                            'bang' => 'সর্বনিম্ন ৮টি অক্ষর',
                            'zh' => '至少 8 个字符',
                            'ta' => 'குறைந்தபட்ச 8 எழுத்துகள்',
                            default => 'Minimum 8 characters',
                        } }}
                    </li>
                    <li>
                        {{ match($lang) {
                            'bang' => 'অন্তত ১টি বড় হাতের অক্ষর (A-Z)',
                            'zh' => '至少 1 个大写字母 (A-Z)',
                            'ta' => 'குறைந்தபட்ச 1 பொருள் மீதி எழுத்து (A-Z)',
                            default => 'At least 1 uppercase letter (A-Z)',
                        } }}
                    </li>
                    <li>
                        {{ 
                            match($lang) {
                                'bang' => 'অন্তত ১টি ছোট হাতের অক্ষর (a-z)',
                                'zh' => '至少 1 个小写字母 (a-z)',
                                'ta' => 'குறைந்தபட்ச 1 சிறிய எழுத்து (a-z)',
                                default => 'At least 1 lowercase letter (a-z)',
                            } 
                        }}
                    </li>
                    <li>
                        {{ 
                            match($lang) {
                                'bang' => 'অন্তত ১টি সংখ্যা (0-9)',
                                'zh' => '至少 1 个数字 (0-9)',
                                'ta' => 'குறைந்தபட்ச 1 எண் (0-9)',
                                default => 'At least 1 number (0-9)',
                            } 
                        }}
                    </li>
                    <li>
                        {{ 
                            match($lang) {
                                'bang' => 'অন্তত ১টি বিশেষ অক্ষর (!@#$%^&*...)',
                                'zh' => '至少 1 个特殊字符 (!@#$%^&*...)',
                                'ta' => 'குறைந்தபட்ச 1 பயன்பாட்டு எழுத்து (!@#$%^&*...)',
                                default => 'At least 1 special character (!@#$%^&*...)',
                            } 
                        }}
                    </li>
                </ul>
                <p class="text-xs text-gray-600 mt-2">
                    <span class="font-semibold">
                        {{ 
                            match($lang) {
                                'bang' => 'উদাহরণ:',
                                'zh' => '示例：',
                                'ta' => 'எடுத்துக்காட்டு:',
                                default => 'Example:',
                            } 
                        }}
                    </span> 
                    <span class="font-mono text-gray-700">MyP@ssw0rd</span>
                </p>
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
                                {!! 
                                    match($lang) {
                                        'bang' => 'এই বিকল্প বাছাই করলে, আপনার তথ্য ভবিষ্যতের ফর্মের জন্য সংরক্ষণ করা যাবে। আপনি আমাদের আপনার ব্যক্তিগত তথ্য এবং আপনার অধিকার সম্পর্কে আরও জানতে আমাদের গোপনীয়তা নীতি দেখুন <a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">গোপনীয়তা নীতি</a>',
                                        'zh' => '通过选择此选项，您的数据可以存储在未来表单中。您可以通过查看我们的隐私政策了解更多关于我们如何处理您的个人信息和您的权利。<a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">隐私政策</a>',
                                        'ta' => 'இந்த விருப்பத்தை தேர்ந்தெடுத்தால், உங்கள் தரவு எதிர்காலத்தில் படிப்படியாக சேமிக்கப்படும். நாங்கள் உங்கள் தனியுரிமை தகவல்களை எவ்வாறு நாங்கள் செயல்படுத்துகிறோம் மற்றும் உங்கள் அதிகாரங்களை எவ்வாறு நாங்கள் செயல்படுத்துகிறோம் என்பதை எங்களின் தனியுரிமை தன்மை நீதியை பார்க்கவும் <a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">தனியுரிமை நீதி</a>',
                                        default => 'By selecting this option, your data can be stored for future forms. You can learn more about how we handle your personal information and your rights by reviewing our <a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-indigo-500">Privacy Policy</a>',
                                    }
                                !!}
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
                    <span x-show="!submitting">
                        {{ 
                            match($lang) {
                                'bang' => 'নিবন্ধন করুন',
                                'zh' => '注册',
                                'ta' => 'பதிவுசெய்யவும்',
                                default => 'Register',
                            }
                        }}
                    </span>
                    <span x-show="submitting" x-cloak>
                        {{ 
                            match($lang) {
                                'bang' => 'নিবন্ধন করছে...',
                                'zh' => '注册中...',
                                'ta' => 'பதிவுசெய்யப்படுகிறது...',
                                default => 'Registering...',
                            }
                        }}
                    </span>
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

                        console.log(fullNumber);

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
