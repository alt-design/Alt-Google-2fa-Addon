import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import statamic from '@statamic/cms/vite-plugin';
import tailwindcss from '@tailwindcss/vite'; 

export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'resources/dist/hot',
            input: [
                'resources/css/alt-google-2fa-frontend.css'
            ],
            publicDirectory: 'resources/dist',
        }),
        tailwindcss(),  
        statamic(),
    ],
});