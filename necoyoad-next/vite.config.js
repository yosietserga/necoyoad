import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import alpine from 'alpinejs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
