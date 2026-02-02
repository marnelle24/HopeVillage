import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const ngrokDomain = process.env.VITE_HMR_HOST || process.env.APP_URL?.replace(/^https?:\/\//, '').replace(/\/$/, '');
const isNgrok = ngrokDomain?.includes('ngrok');

// Plugin to suppress @property CSS warnings
const suppressCssWarnings = () => {
    return {
        name: 'suppress-css-warnings',
        buildStart() {
            // Suppress console warnings about @property during build
            const originalWarn = console.warn;
            console.warn = (...args) => {
                const message = args.join(' ');
                if (message.includes('@property') || message.includes('Unknown at rule') || message.includes('--radialprogress')) {
                    return;
                }
                originalWarn.apply(console, args);
            };
        },
    };
};

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
        suppressCssWarnings(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/chart.js')) return 'chart';
                    if (id.includes('node_modules/@tiptap')) return 'tiptap';
                },
            },
            onwarn(warning, warn) {
                // Suppress @property warnings from DaisyUI
                if (warning.message?.includes('@property') || warning.message?.includes('Unknown at rule')) {
                    return;
                }
                warn(warning);
            },
        },
        chunkSizeWarningLimit: 600,
        // Use esbuild for CSS minification to avoid @property warnings
        cssMinify: 'esbuild',
    },
    css: {
        devSourcemap: true,
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
