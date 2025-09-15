import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '10.0.0.4',
            port: 5173,
        },
    },
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
