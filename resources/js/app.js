import './bootstrap';
import { registerSW } from 'virtual:pwa-register';
import Chart from 'chart.js/auto';

registerSW({ immediate: true });
import * as RaffleWheel from './raffle-wheel.js';
import './tiptap-editor.js';

// Make Chart available globally for Alpine.js components
window.Chart = Chart;

// Make RaffleWheel functions available globally
window.RaffleWheel = RaffleWheel;
