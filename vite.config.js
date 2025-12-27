import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const ngrokDomain = process.env.VITE_HMR_HOST || process.env.APP_URL?.replace(/^https?:\/\//, '').replace(/\/$/, '');
const isNgrok = ngrokDomain?.includes('ngrok');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            detectTls: isNgrok ? ngrokDomain : undefined,
        }),
    ],
    build: {
        rollupOptions: {
            onwarn(warning, warn) {
                // Suppress @property warnings from DaisyUI
                if (warning.message?.includes('@property') || warning.message?.includes('Unknown at rule')) {
                    return;
                }
                warn(warning);
            },
        },
    },
    server: {
        host: '0.0.0.0', // Allow external connections
        port: 5173,
        origin: isNgrok ? `https://${ngrokDomain}` : undefined,
        hmr: {
            host: ngrokDomain || 'localhost',
            protocol: isNgrok ? 'wss' : 'ws',
            clientPort: isNgrok ? 443 : 5173,
        },
        strictPort: false,
    },
});
