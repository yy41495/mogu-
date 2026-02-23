import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/base.css',
                'resources/css/login.css',
                'resources/css/recipes.css',
                'resources/css/recipe-form.css',
                'resources/css/recipe-detail.css',
                'resources/js/app.js',
                'resources/js/index.js',
                'resources/js/recipe-form.js',
                'resources/js/sleep.js',
                'resources/js/delete.js',
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
