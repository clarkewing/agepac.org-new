import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny, local } from 'laravel-vite-plugin/fonts';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/public.css',
                'resources/js/app.js',
                'resources/js/public.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                local('Inter', {
                    variants: [
                        { src: 'resources/fonts/inter-latin-opsz-normal.woff2', weight: '100 900' },
                        { src: 'resources/fonts/inter-latin-opsz-italic.woff2', weight: '100 900', style: 'italic' },
                    ],
                    preload: [{ weight: '100 900' }],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
