import './bootstrap';
import Chart from 'chart.js/auto';
import * as RaffleWheel from './raffle-wheel.js';

// Make Chart available globally for Alpine.js components
window.Chart = Chart;

// Make RaffleWheel functions available globally
window.RaffleWheel = RaffleWheel;
