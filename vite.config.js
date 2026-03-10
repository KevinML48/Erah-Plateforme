import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/dev-console.css',
                'resources/js/app.js',
                'resources/js/help-center.jsx',
                'resources/js/dev-console.js',
            ],
            refresh: true,
        }),
    ],
});
