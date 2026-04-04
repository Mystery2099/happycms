import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import path from 'node:path';

export default defineConfig({
  base: '/public/assets/',
  plugins: [svelte()],
  publicDir: false,
  build: {
    outDir: 'public/assets',
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'frontend/src/main.ts'),
      output: {
        entryFileNames: 'app.js',
        chunkFileNames: 'chunks/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name?.endsWith('.css')) {
            return 'app.css';
          }

          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  }
});
