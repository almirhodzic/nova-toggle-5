import vue from '@vitejs/plugin-vue';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [vue()],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        lib: {
            entry: 'resources/js/toggle.js',
            name: 'field',
            formats: ['iife'],
            fileName: () => 'js/toggle.js',
        },
        rollupOptions: {
            external: ['vue', 'laravel-nova-ui'],
            output: {
                globals: {
                    vue: 'Vue',
                },
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(
                __dirname,
                '../../vendor/laravel/nova/resources/js',
            ),
        },
    },
});
