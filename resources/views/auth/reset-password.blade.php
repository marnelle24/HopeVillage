<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="w-32">
        </x-slot>


        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            @php
                $lang = request()->get('lang', 'en');
            @endphp

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" 
                    value="{{ 
                        match($lang) {
                            'bang' => 'যোগাযোগ নম্বর বা ইমেল ঠিকানা',
                            'zh' => '联系电话或电子邮箱',
                            'ta' => 'மொபைல் எண் அல்லது மின்னஞ்சல் முகவரி',
                            default => 'Email Address',
                        }
                    }}"
                />
                <input 
                    id="email" 
                    class="mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    type="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="{{ 
                        match($lang) {
                            'bang' => 'email@example.com',
                            'zh' => 'email@example.com',
                            'ta' => 'email@example.com',
                            default => 'email@example.com',
                        }
                    }}"
                />
            </div>

            <div class="mt-4">
                <x-label for="password" 
                    value="{{ 
                        match($lang) {
                            'bang' => 'পাসওয়ার্ড',
                            'zh' => '密码',
                            'ta' => 'கடவுச்சொல்',
                            default => 'Password',
                        }
                    }}"
                />
                <input 
                    id="password" 
                    class="mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="{{ 
                        match($lang) {
                            'bang' => 'পাসওয়ার্ড',
                            'zh' => '密码',
                            'ta' => 'கடவுச்சொல்',
                            default => 'Password',
                        }
                    }}"
                />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <input 
                    id="password_confirmation" 
                    class=" mt-2 w-full rounded-full px-4 py-2 border border-orange-400 focus:border-orange-500 focus:ring-orange-500"
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="{{ 
                        match($lang) {
                            'bang' => 'পাসওয়ার্ড',
                            'zh' => '密码',
                            'ta' => 'கடவுச்சொல்',
                            default => 'Confirm Password',
                        }
                    }}"
                />
            </div>

            <div class="flex items-center justify-center mt-8">
                <x-button class="w-3/4 flex justify-center py-4 text-white bg-orange-500 hover:bg-orange-600 active:bg-orange-700 focus:bg-orange-600 rounded-full">
                    {{ 
                        match($lang) {
                            'bang' => 'পাসওয়ার্ড রিসেট করুন',
                            'zh' => '重置密码',
                            'ta' => 'கடவுச்சொல் மறுப்பு',
                            default => 'Reset Password',
                        }
                    }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
