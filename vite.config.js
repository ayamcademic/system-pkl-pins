import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const hmrHost = process.env.VITE_HMR_HOST || 'localhost';
const hmrPort = Number(process.env.VITE_HMR_PORT || 5173);

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: true,          // biar bisa diakses dari device lain juga kalau perlu
    port: 5173,
    strictPort: true,
    hmr: {
      host: hmrHost,     // <-- dynamic
      port: hmrPort,
    },
  },
});
