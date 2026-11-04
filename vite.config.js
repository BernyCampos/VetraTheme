import { defineConfig } from 'vite';

// Standalone verification/distribution build. Pelican discovers resources/css
// and compiles the same source through its own unchanged Vite configuration.
export default defineConfig({
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: 'resources/css/vetra.css',
            output: { assetFileNames: 'vetra.[ext]' },
        },
    },
});
