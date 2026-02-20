import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/common.css',
                'resources/css/login.css',
                'resources/css/recipes.css',
                'resources/css/form.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        // tailwindcss(),
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
