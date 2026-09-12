import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Admin/Blade entry (Bootstrap via CDN, no Tailwind needed here)
                'resources/css/app.css',
                'resources/js/app.js',
                // Customer PWA React entry
                'resources/react/main.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
