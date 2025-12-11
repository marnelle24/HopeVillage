<div>
    <!-- Register Button -->
    <button 
        wire:click="openModal"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Register Another Merchant
    </button>

    <x-dialog-modal wire:model="showModal" maxWidth="4xl">
        <x-slot name="title">
            Register Another Merchant
        </x-slot>

        <x-slot name="content">
            @if($showSuccess)
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold">Merchant Registration Submitted Successfully!</h3>
                            <p class="mt-1">The merchant application has been submitted and is pending admin approval. You have been automatically linked to this merchant and can switch to it once approved.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6">
                    <p class="text-gray-600">Fill out the form below to register another merchant. The application will be reviewed by our admin team.</p>
                </div>

                <form wire:submit="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Business Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name"
                                    wire:model.blur="name" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                                    placeholder="Your Business Name"
                                >
                                @error('name') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Contact Name -->
                            <div>
                                <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Person Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="contact_name"
                                    wire:model.blur="contact_name" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contact_name') border-red-500 @enderror"
                                    placeholder="Contact Person Name"
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
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror"
                                    placeholder="+65 1234 5678"
                                >
                                @error('phone') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email"
                                    wire:model.blur="email" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
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
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('website') border-red-500 @enderror"
                                    placeholder="https://www.example.com"
                                >
                                @error('website') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Street Address
                                </label>
                                <input 
                                    type="text" 
                                    id="address"
                                    wire:model.blur="address" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-500 @enderror"
                                    placeholder="Street Address"
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
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-500 @enderror"
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
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('province') border-red-500 @enderror"
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
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('postal_code') border-red-500 @enderror"
                                        placeholder="123456"
                                    >
                                    @error('postal_code') 
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Description
                                </label>
                                <textarea 
                                    id="description"
                                    wire:model.blur="description" 
                                    rows="4"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                                    placeholder="Tell us about your business..."
                                ></textarea>
                                @error('description') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Logo Upload -->
                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Logo (Optional)
                                </label>
                                <input 
                                    type="file" 
                                    id="logo"
                                    wire:model="logo" 
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold bg-gray-50 border border-gray-300 rounded-lg file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200"
                                >
                                @error('logo') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                                @if($logo)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600">Preview:</p>
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo preview" class="mt-1 w-32 h-32 object-contain border border-gray-300 rounded-lg">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </x-slot>

        <x-slot name="footer">
            @if($showSuccess)
                <button 
                    wire:click="closeModal"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
                >
                    Close
                </button>
            @else
                <button 
                    wire:click="closeModal"
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold mr-3"
                >
                    Cancel
                </button>
                <button 
                    wire:click="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
                >
                    Submit Application
                </button>
            @endif
        </x-slot>
    </x-dialog-modal>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('merchant-registered', () => {
                setTimeout(() => {
                    @this.closeModal();
                }, 3000);
            });
        });
    </script>
</div>
