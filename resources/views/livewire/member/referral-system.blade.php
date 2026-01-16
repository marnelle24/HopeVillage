<x-slot name="header">
    @livewire('member.points-header')
</x-slot>

<div class="max-w-md mx-auto min-h-screen pb-20">
    <div class="px-4 py-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Referral System</h1>
            <p class="text-sm text-gray-600 mt-1">Share your referral link and earn points for each new member</p>
        </div>

        <!-- QR Code Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col items-center gap-4">
                <h2 class="text-lg font-semibold text-gray-800 text-center">Your Referral QR Code</h2>
                
                @if($qrCodeImage)
                    <div class="bg-white p-4 rounded-xl border-2 border-orange-500 shadow-md">
                        <img src="{{ $qrCodeImage }}" alt="Referral QR Code" class="w-64 h-64 object-contain">
                    </div>
                @else
                    <div class="bg-gray-100 p-8 rounded-xl border-2 border-gray-300">
                        <p class="text-gray-500 text-center">QR code not available</p>
                    </div>
                @endif

                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Referral Link</label>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            readonly 
                            value="{{ $referralLink }}" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm font-mono"
                            id="referral-link-input"
                        >
                        <button 
                            type="button"
                            onclick="copyReferralLink()"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
                            id="copy-button"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span id="copy-button-text">Copy</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        Share this link or QR code with others to earn referral points
                    </p>
                </div>
            </div>
        </div>

        <!-- Referrals List Section -->
        <div class="bg-white rounded-2xl shadow-lg">
            <div class="flex items-center justify-between p-6">
                <h2 class="text-lg font-semibold text-gray-800">Referred Members</h2>
                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium">
                    {{ $this->referrals->count() }}
                </span>
            </div>

            @if($this->referrals->count() > 0)
                <div class="overflow-x-auto px-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">QR Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->referrals as $referral)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                                                <span class="text-orange-600 font-semibold text-xs">
                                                    {{ strtoupper(substr($referral->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-medium text-gray-900">{{ $referral->name }}</span>
                                                <span class="text-[11px] text-gray-500">
                                                    {{ $referral->created_at->format('d M Y h:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 border border-gray-300 rounded-full text-gray-700">
                                            {{ $referral->qr_code }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 px-6">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No referrals yet</p>
                    <p class="text-sm text-gray-400 mt-2">Share your referral link to start earning points!</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyReferralLink() {
        const input = document.getElementById('referral-link-input');
        const button = document.getElementById('copy-button');
        const buttonText = document.getElementById('copy-button-text');
        const text = input ? input.value : '';
        
        if (!text) {
            alert('Referral link is not available');
            return;
        }
        
        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                // Success feedback
                showCopySuccess(button, buttonText);
            }).catch((err) => {
                // Fallback to execCommand
                fallbackCopy(text, input, button, buttonText);
            });
        } else {
            // Fallback to execCommand for older browsers
            fallbackCopy(text, input, button, buttonText);
        }
    }
    
    function fallbackCopy(text, input, button, buttonText) {
        if (!input) return;
        
        // Select the text
        input.focus();
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess(button, buttonText);
            } else {
                // If execCommand fails, try to select and show manual copy message
                input.select();
                alert('Please copy the link manually: ' + text);
            }
        } catch (err) {
            console.error('Copy failed:', err);
            alert('Unable to copy. Please copy manually: ' + text);
        }
    }
    
    function showCopySuccess(button, buttonText) {
        if (!button || !buttonText) return;
        
        const originalText = buttonText.textContent;
        const originalClasses = button.className;
        
        // Update button appearance
        buttonText.textContent = 'Copied!';
        button.classList.remove('bg-orange-500', 'hover:bg-orange-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        
        // Restore after 2 seconds
        setTimeout(() => {
            buttonText.textContent = originalText;
            button.className = originalClasses;
        }, 2000);
    }
</script>
@endpush
