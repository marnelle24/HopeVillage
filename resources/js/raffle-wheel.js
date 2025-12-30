import { Wheel } from 'spin-wheel';

let wheelInstance = null;
let isSpinning = false;
let isInitializing = false; // Prevent multiple simultaneous initializations

export function initRaffleWheel(containerElement, entries, forceReinit = false) {
    // Prevent multiple simultaneous initializations
    if (isInitializing) {
        console.warn('Wheel initialization already in progress, skipping...');
        return wheelInstance;
    }
    
    isInitializing = true;
    // Don't reinitialize if wheel exists and we're not forcing it
    // Check if container already has a canvas (wheel is rendered)
    const hasExistingWheel = containerElement.querySelector('canvas') !== null;
    
    if (wheelInstance && hasExistingWheel && !forceReinit) {
        // Wheel is already rendered, keep it
        isInitializing = false; // Reset initialization flag
        return wheelInstance;
    }

    // Clean up existing wheel if any
    if (wheelInstance && forceReinit) {
        wheelInstance = null;
    }

    // Only clear container if we're forcing reinit or no wheel exists
    if (forceReinit || !hasExistingWheel) {
        // Always clear container when forcing reinit or when no wheel exists
        containerElement.innerHTML = '';
    } else {
        // Wheel exists and is visible, don't clear
        isInitializing = false; // Reset initialization flag
        return wheelInstance;
    }

    if (!entries || entries.length === 0) {
        containerElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">No entries loaded</div>';
        isInitializing = false; // Reset initialization flag
        return null;
    }

    // Prepare items for spin-wheel with default colors
    const colors = ['#fde68a', '#bfdbfe', '#fecaca', '#bbf7d0', '#e9d5ff', '#fed7aa', '#fbcfe8', '#c7d2fe'];
    
    const items = entries.map((entry, index) => {
        // Truncate labels - shorter for QR codes, longer for other entries
        const maxLength = entry.length > 20 ? 12 : 15; // QR codes are often longer
        const label = entry.length > maxLength ? entry.substring(0, maxLength) + '...' : entry;
        
        return {
            label: label,
            backgroundColor: colors[index % colors.length],
            value: entry, // Store full value
        };
    });

    // Configure wheel properties
    const props = {
        items: items,
        pointerAngle: 0, // Pointer at top (0 degrees)
        radius: 0.95,
        itemLabelColors: ['#000000'],
        itemLabelFontSizeMax: 12, // Maximum font size for labels (in pixels)
        itemLabelRadius: 0.8, // Position labels near the edge (0.9 = 90% from center, near outer edge)
        itemLabelRotation: 0,
        itemLabelAlign: 'center',
        borderColor: '#af7d0f',
        borderWidth: 2,
        lineColor: '#FFFFFF',
        lineWidth: 1,
    };

    // Create wheel instance
    wheelInstance = new Wheel(containerElement, props);

    // Set up event listeners
    wheelInstance.onRest = (event) => {
        isSpinning = false;
        // Dispatch event to Livewire and Alpine.js (with bubbles for Alpine to catch it)
        window.dispatchEvent(new CustomEvent('wheel-rest', {
            bubbles: true,
            detail: { currentIndex: event.currentIndex }
        }));
    };

    wheelInstance.onSpin = (event) => {
        isSpinning = true;
    };

    wheelInstance.onCurrentIndexChange = (event) => {
        // Optional: can be used for tick sounds or visual feedback
    }

    isInitializing = false; // Reset initialization flag
    return wheelInstance;
}

export function spinToItem(itemIndex, duration = 10000) {
    if (!wheelInstance || isSpinning) {
        return;
    }

    isSpinning = true;
    
    // Use spinToItem with easing
    // Parameters: (targetItemIndex, duration, spinClockwise, spinRevolutions, spinOffset, easing)
    wheelInstance.spinToItem(
        itemIndex,
        duration,
        true, // spin clockwise
        2, // 2 full revolutions
        1, // spin offset
        (t) => 1 - Math.pow(1 - t, 3) // cubic ease out
    );
}

export function getWheelInstance() {
    return wheelInstance;
}

export function isWheelSpinning() {
    return isSpinning;
}

export function ensureWheelVisible(containerElement) {
    if (!wheelInstance || !containerElement) {
        return false;
    }
    
    // Check if canvas exists
    const canvas = containerElement.querySelector('canvas');
    if (!canvas) {
        // Canvas is missing, need to reinitialize
        return false;
    }
    
    // Ensure canvas is visible
    canvas.style.display = '';
    canvas.style.visibility = 'visible';
    
    return true;
}

