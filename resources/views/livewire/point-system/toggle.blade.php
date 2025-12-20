<div>
    <div class="flex items-center overflow-hidden shadow-md rounded-lg">
        @php
            // When system is disabled, Enable button is active (green)
            // When system is enabled, Disable button is active (green)
            $enableButtonClass = $pointSystemEnabled 
                ? 'bg-green-500 hover:bg-green-600' 
                : 'bg-gray-300 hover:bg-gray-400';
            $disableButtonClass = !$pointSystemEnabled 
                ? 'bg-red-500 hover:bg-red-600' 
                : 'bg-gray-300 hover:bg-gray-400';
        @endphp
        <button 
            type="button"
            @if($pointSystemEnabled) disabled @endif
            wire:click="togglePointSystem"
            wire:loading.attr="disabled"
            class="disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white rounded-l-lg transition cursor-pointer {{ $enableButtonClass }}">
            <span wire:loading.remove wire:target="togglePointSystem">Enable</span>
            <span wire:loading wire:target="togglePointSystem">Loading...</span>
        </button>
        <button 
            type="button"
            @if(!$pointSystemEnabled) disabled @endif
            wire:click="togglePointSystem"
            wire:loading.attr="disabled"
            class="disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white rounded-r-lg transition cursor-pointer {{ $disableButtonClass }}">
            <span wire:loading.remove wire:target="togglePointSystem">Disable</span>
            <span wire:loading wire:target="togglePointSystem">Loading...</span>
        </button>
    </div>
</div>
