import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import tailwindcss from 'tailwindcss';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/cp.css',
                'resources/js/cp.js'
            ],
            publicDirectory: 'resources/dist',
        }),
        vue(),
    ],
    css: {
        postcss: {
            plugins: [tailwindcss()],
        },
    }
});
