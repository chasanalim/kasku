import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { VitePWA } from 'vite-plugin-pwa';

// vite-plugin-pwa hanya perlu jalan pada build client (dan dev server).
// Saat `vite build --ssr` berjalan, plugin dinonaktifkan agar tidak ikut
// membundle kode service worker ke dalam bundle SSR.
const isSsrBuild = process.argv.includes('--ssr');

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/sass/app.scss'],
            ssr: 'resources/js/ssr.jsx',
            refresh: true,
        }),
        react(),
        // PWA: service worker + web app manifest. Hasil generateSW ditulis ke
        // public/build (sw.js, manifest.webmanifest). Service worker didaftarkan
        // sebagai /build/sw.js dan scope-nya diperlebar ke seluruh origin lewat
        // header `Service-Worker-Allowed: /` di public/.htaccess.
        // Nama/ikon aplikasi dapat diubah di blok `manifest` di bawah.
        ...(!isSsrBuild
            ? [
                  VitePWA({
                      registerType: 'autoUpdate',
                      strategies: 'generateSW',
                      injectRegister: false, // registrasi manual di app.jsx
                      manifest: {
                          name: 'KU KRESEK 1',
                          short_name: 'Kas Kresek',
                          description:
                              'Aplikasi kas masjid / kelompok: pemasukan, pengeluaran, shodaqah, dan laporan keuangan.',
                          lang: 'id',
                          theme_color: '#2da88a',
                          background_color: '#ffffff',
                          display: 'standalone',
                          orientation: 'portrait',
                          start_url: '/',
                          scope: '/',
                          icons: [
                              {
                                  src: '/icons/icon-192x192.png',
                                  sizes: '192x192',
                                  type: 'image/png',
                              },
                              {
                                  src: '/icons/icon-512x512.png',
                                  sizes: '512x512',
                                  type: 'image/png',
                              },
                              {
                                  src: '/icons/icon-512x512-maskable.png',
                                  sizes: '512x512',
                                  type: 'image/png',
                                  purpose: 'maskable',
                              },
                          ],
                      },
                      workbox: {
                          // Precache semua aset hasil build (JS/CSS/font/gambar)
                          globPatterns: [
                              '**/*.{js,css,woff2,woff,ttf,eot,svg,png,ico,json}',
                          ],
                          // Jangan precache file internal (laravel build manifest,
                          // sw & manifest PWA itu sendiri).
                          globIgnores: [
                              'manifest.json',
                              'manifest.webmanifest',
                              'sw.js',
                              'workbox-*.js',
                          ],
                          // Tidak ada index.html (halaman dirender Laravel),
                          // navigasi ditangani lewat runtimeCaching di bawah.
                          navigateFallback: null,
                          runtimeCaching: [
                              {
                                  // Halaman: network-first, fallback ke cache
                                  // halaman terakhir yang berhasil dikunjungi
                                  // (agar tetap bisa dibuka saat offline).
                                  urlPattern: ({ request }) =>
                                      request.mode === 'navigate',
                                  handler: 'NetworkFirst',
                                  options: {
                                      cacheName: 'pages',
                                      networkTimeoutSeconds: 3,
                                  },
                              },
                              {
                                  // Bootstrap & font dari CDN jsdelivr
                                  urlPattern: ({ url }) =>
                                      url.origin === 'https://cdn.jsdelivr.net',
                                  handler: 'StaleWhileRevalidate',
                                  options: {
                                      cacheName: 'cdn',
                                  },
                              },
                              {
                                  // Fonts (bunny / google)
                                  urlPattern: ({ url }) =>
                                      /^https:\/\/(fonts\.bunny\.net|fonts\.googleapis\.com|fonts\.gstatic\.com)/.test(
                                          url.origin
                                      ),
                                  handler: 'StaleWhileRevalidate',
                                  options: {
                                      cacheName: 'google-fonts',
                                  },
                              },
                          ],
                      },
                  }),
              ]
            : []),
    ],
});
