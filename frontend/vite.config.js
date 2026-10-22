import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    envDir: '../backend',
    build: {
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            publicDirectory: '../backend/public',
            hotFile: '../backend/public/hot',
            refresh: [
                'resources/views/**',
                '../backend/app/View/Components/**',
                '../backend/lang/**',
                '../backend/routes/**',
            ],
        }),
        tailwindcss(),
    ],
});
