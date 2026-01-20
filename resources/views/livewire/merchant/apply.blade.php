<div>
    <!-- Loading Overlay -->
    <div wire:loading wire:target="submit" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="rounded-lg p-8 flex flex-col items-center gap-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-400"></div>
            <p class="text-gray-200 text-sm font-semibold">Submitting your application...</p>
        </div>
    </div>

    <div class="min-h-screen flex flex-col justify-center items-center py-10 bg-orange-100">
        @if($showSuccess)
            <div class="w-full sm:max-w-xl flex flex-col gap-10 justify-center items-center">
                <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="lg:w-30 w-20">
                <div class="mb-6 bg-green-100/60 border border-green-600 text-green-700 p-8 rounded-lg">
                    <div class="flex flex-col items-start">
                        <div class="flex gap-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold">Application Submitted Successfully!</h3>
                        </div>
                        <p class="mt-1 pl-11 text-md">Thank you for your interest. Your merchant application has been submitted and is pending admin approval. You will be notified via email once your application is reviewed.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="w-full sm:max-w-3xl my-6 lg:p-12 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-10 flex flex-col gap-6 lg:flex-row items-center lg:items-end justify-center lg:justify-start">
                    <img src="{{ asset('hv-logo.png') }}" alt="hope village Logo" class="lg:w-30 w-20">
                    <div class="lg:text-left text-center">
                        <h2 class="md:text-2xl text-xl font-extrabold text-orange-400">Apply to Become a Merchant</h2>
                        <p class="mt-2 text-gray-600 lg:text-lg text-sm md:text-left text-center">Fill out the form below to apply as a merchant. <br />
                            Your application will be reviewed by our admin.</p>
                    </div>
                </div>

                <form wire:submit="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 border-b border-dashed border-gray-400 pt-6 pb-6">
                        <!-- Logo Upload -->
                        <div class="col-span-1 w-full">
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Logo (Optional)
                            </label>
                            <div class="mt-2 mb-4 w-full max-w-50 h-32 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center bg-gray-50">
                                @if($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" alt="Logo preview" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center w-full h-full">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-600">No image</p>
                                    </div>
                                @endif
                            </div>
                            <input 
                                type="file" 
                                id="logo"
                                wire:model="logo" 
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                            >
                            <div class="flex items-center lg:justify-center justify-start">
                                <label 
                                    for="logo" 
                                    class="inline-block w-32 p-2 text-xs font-semibold text-center text-white bg-orange-400 border border-orange-400 rounded-full cursor-pointer hover:bg-orange-500 transition duration-300"
                                >
                                    Upload Logo
                                </label>
                                @error('logo') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-4 col-span-3">
                            <!-- Business Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Merchant Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name"
                                    wire:model.blur="name" 
                                    class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
                                    placeholder="Your Business Name"
                                    required
                                >
                                @error('name') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Description
                                </label>
                                <textarea 
                                    id="description"
                                    wire:model.blur="description" 
                                    rows="3"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                                    placeholder="Tell us about your business..."
                                ></textarea>
                                @error('description') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        
                        <div class="space-y-4 col-span-4">
                            <!-- Address -->
                            <div>
                                <h3 class="text-lg text-gray-500 mb-6 mt-2 col-span-2 font-bold">Business Address</h3>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address
                                </label>
                                <input 
                                    type="text" 
                                    id="address"
                                    wire:model.blur="address" 
                                    class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('address') border-red-500 @enderror"
                                    placeholder="Street Name, Building Name, etc."
                                >
                                @error('address') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- City, Province, Postal Code -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        City
                                    </label>
                                    <input 
                                        type="text" 
                                        id="city"
                                        wire:model.blur="city" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('city') border-red-500 @enderror"
                                        placeholder="City"
                                    >
                                    @error('city') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                                        Province/State
                                    </label>
                                    <input 
                                        type="text" 
                                        id="province"
                                        wire:model.blur="province" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('province') border-red-500 @enderror"
                                        placeholder="Province"
                                    >
                                    @error('province') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Postal Code
                                    </label>
                                    <input 
                                        type="text" 
                                        id="postal_code"
                                        wire:model.blur="postal_code" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('postal_code') border-red-500 @enderror"
                                        placeholder="123456"
                                    >
                                    @error('postal_code') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <h3 class="text-lg text-gray-500 mb-6 mt-10 col-span-2 border-t border-dashed border-gray-400 pt-3 font-nunito font-bold">Contact Information</h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Contact Name -->
                                <div>
                                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Person Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="contact_name"
                                        wire:model.blur="contact_name" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('contact_name') border-red-500 @enderror"
                                        placeholder="Contact Person Name"
                                        required
                                    >
                                    @error('contact_name') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="phone"
                                        wire:model.blur="phone" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('phone') border-red-500 @enderror"
                                        placeholder="+6512345678"
                                        maxlength="12"
                                        required
                                    >
                                    @error('phone') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address (Optional)
                                    </label>
                                    <input 
                                        type="email" 
                                        id="email"
                                        wire:model.blur="email" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('email') border-red-500 @enderror"
                                        placeholder="business@example.com"
                                    >
                                    @error('email') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                <!-- Website -->
                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                        Website (Optional)
                                    </label>
                                    <input 
                                        type="url" 
                                        id="website"
                                        wire:model.blur="website" 
                                        class="w-full px-4 py-2 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('website') border-red-500 @enderror"
                                        placeholder="https://www.example.com"
                                    >
                                    @error('website') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Login Credentials -->
                            <div>
                                <h3 class="text-lg text-gray-500 mb-3 mt-8 col-span-2 border-t border-dashed border-gray-400 pt-3 font-nunito font-bold">Login Credentials</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    <span class="font-bold text-orange-400">NOTE:</span> 
                                    <span class="text-sm text-orange-400">
                                        You will be able to login to your account using your email address (if provided) or your phone number.
                                    </span>
                                    <br />
                                    @if(!empty($email) || !empty($phone))
                                        <span class="text-sm text-gray-500">
                                            @if(!empty($email))
                                                <br />
                                                Email: <span class="font-mono">{{ $email }}</span>
                                                <br />
                                            @endif
                                            @if(!empty($phone))
                                                Phone: <span class="font-mono">{{ $phone }}</span>
                                            @endif
                                        </span>
                                    @endif
                                </p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Password <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative" x-data="{ showPassword: false }">
                                            <input 
                                                type="password" 
                                                id="password"
                                                wire:model.blur="password" 
                                                x-bind:type="showPassword ? 'text' : 'password'"
                                                class="w-full px-4 py-2 pr-10 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('password') border-red-500 @enderror"
                                                placeholder="Enter password"
                                                required
                                            >
                                            <button
                                                type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                                tabindex="-1"
                                            >
                                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <svg x-show="!showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password') 
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirm Password <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative" x-data="{ showPasswordConfirm: false }">
                                            <input 
                                                type="password" 
                                                id="password_confirmation"
                                                wire:model.blur="password_confirmation" 
                                                x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                                                class="w-full px-4 py-2 pr-10 border rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('password_confirmation') border-red-500 @enderror"
                                                placeholder="Confirm password"
                                            >
                                            <button
                                                type="button"
                                                @click="showPasswordConfirm = !showPasswordConfirm"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                                tabindex="-1"
                                            >
                                                <svg x-show="showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <svg x-show="!showPasswordConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password_confirmation') 
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Requirements Notes -->
                                <div class="mt-4 text-xs text-gray-500 bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <p class="text-orange-600 font-semibold mb-2">Password Requirements:</p>
                                    <ul class="list-disc list-inside space-y-1 text-orange-500">
                                        <li>At least 8 characters long</li>
                                        <li>At least one uppercase letter (A-Z)</li>
                                        <li>At least one lowercase letter (a-z)</li>
                                        <li>At least one number (0-9)</li>
                                        <li>At least one special character (!@#$%^&*...)</li>
                                    </ul>
                                    <p class="mt-2 text-orange-600 font-semibold">Example: MyP@ssw0rd</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-8">
                            <label for="terms" class="flex items-start">
                                <div class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        id="terms"
                                        wire:model="terms" 
                                        class="mt-1 border-gray-300 checked:bg-orange-500 rounded-full p-2 focus:border-orange-500 focus:ring-orange-500"
                                    />
                                    <div class="ms-2">
                                        <span class="text-sm text-gray-700">
                                            By selecting this option, your data can be stored for future forms. You can learn more about how we handle your personal information and your rights by reviewing our 
                                            <a target="_blank" href="https://www.hia.sg/privacy-policy" class="underline text-orange-500 hover:text-orange-600">
                                                Privacy Policy
                                            </a>
                                        </span>
                                        @error('terms') 
                                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endif

                    <!-- Google reCAPTCHA -->
                    @if(config('services.recaptcha.site_key'))
                        <div class="mt-6">
                            <div wire:ignore id="recaptcha-container">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaCallback"></div>
                            </div>
                            @error('gRecaptchaResponse')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-10 flex flex-col lg:flex-row items-center justify-between">
                        <div class="lg:flex hidden items-center gap-2">
                            <span class="text-gray-600 text-sm">Already have an account? </span>
                            <a href="{{ route('login') }}" class="cursor-pointer text-sm text-orange-500 hover:text-orange-500 transition duration-300">
                                Login here
                            </a>
                        </div>
                        <button 
                            type="submit" 
                            wire:target="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-3 bg-orange-400 text-white cursor-pointer rounded-full hover:bg-orange-500 transition duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                            <span wire:loading.remove wire:target="submit">Submit Application</span>
                            <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                        <div class="flex lg:hidden mt-2 items-center gap-2">
                            <span class="text-gray-600 text-sm">Already have an account? </span>
                            <a href="{{ route('login') }}" class="cursor-pointer text-sm text-orange-500 hover:text-orange-500 transition duration-300">
                                Login here
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

@push('scripts')
@if(config('services.recaptcha.site_key'))
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onRecaptchaCallback(token) {
            @this.set('gRecaptchaResponse', token);
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('reset-recaptcha', () => {
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                    @this.set('gRecaptchaResponse', '');
                }
            });
        });
    </script>
@endif
@endpush
