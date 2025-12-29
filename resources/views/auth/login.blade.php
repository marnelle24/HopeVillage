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
                    :value="old('email')" 
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
                <input id="password" placeholder="{{ 
                    request()->get('lang') === 'bang' ? 'পাসওয়ার্ড' : 
                    (request()->get('lang') === 'zh' ? '密码' : 'Password') 
                }}" class="mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500" type="password" name="password" required autocomplete="current-password" />
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
        </form>
    </x-authentication-card>
</x-guest-layout>
