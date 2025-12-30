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

        <form method="POST" action="{{ route('login') }}" class="mt-8 mb-4">
            @csrf

            <div>
                <x-label for="email" value="{{ 
                    request()->get('lang') === 'bang' ? 'যোগাযোগ নম্বর বা ইমেল ঠিকানা' : 
                    (request()->get('lang') === 'zh' ? '联系电话或电子邮箱' : 'Mobile Number or Email Address') 
                }}" />
                <input 
                    id="email" 
                    placeholder="{{ 
                        request()->get('lang') === 'bang' ? '+65XXXXXX বা email@example.com' : 
                        (request()->get('lang') === 'zh' ? '+65XXXXXX 或 email@example.com' : '+65XXXXXX or email@example.com') 
                    }}" 
                    class="mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    type="text" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                <p class="mt-1 text-xs text-gray-500">
                    {{ 
                        request()->get('lang') === 'bang' ? 'আপনি আপনার ইমেল ঠিকানা দিয়ে লগ ইন করতে পারেন।' : 
                        (request()->get('lang') === 'zh' ? '您可以使用电子邮箱登录。' : 'You can login using your email address.') 
                    }}
                </p>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ 
                    request()->get('lang') === 'bang' ? 'পাসওয়ার্ড' : 
                    (request()->get('lang') === 'zh' ? '密码' : 'Password') 
                }}" />
                <div class="relative">
                    <input 
                        id="password" 
                        placeholder="{{ 
                            request()->get('lang') === 'bang' ? 'পাসওয়ার্ড' : 
                            (request()->get('lang') === 'zh' ? '密码' : 'Password') 
                        }}" 
                        class="mt-2 w-full rounded-full px-4 py-2 pr-12 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password" 
                    />
                    <button 
                        type="button" 
                        id="togglePassword" 
                        class="absolute right-4 top-[1.85rem] transform -translate-y-1/2 text-gray-500 hover:text-orange-500 focus:outline-none cursor-pointer"
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
                {{-- <x-input id="password" class="block mt-1 w-full" type="password" name="password" value="123123123" required autocomplete="current-password" /> --}}
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ 
                        request()->get('lang') === 'bang' ? 'মনে রাখুন' : 
                        (request()->get('lang') === 'zh' ? '记住我' : 'Remember me') 
                    }}</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-button class="w-3/4 flex justify-center py-4 text-white bg-orange-500 hover:bg-orange-600 active:bg-orange-700 focus:bg-orange-600 rounded-full">
                    {{ 
                        request()->get('lang') === 'bang' ? 'লগ ইন করুন' : 
                        (request()->get('lang') === 'zh' ? '登录' : 'Log in') 
                    }}
                </x-button>
            </div>

            <div class="flex items-center justify-center mt-4">
                <a href="/forgot-password" class="text-sm text-orange-600 hover:text-orange-500 underline">
                    {{ 
                        request()->get('lang') === 'bang' ? 'পাসওয়ার্ড ভুলে গেছেন?' : 
                        (request()->get('lang') === 'zh' ? '忘记密码？' : 'Forgot Password?') 
                    }}
                </a>
            </div>
        </form>
    </x-authentication-card>

    <script>
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
    </script>
</x-guest-layout>
