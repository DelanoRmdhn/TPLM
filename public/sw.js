// Service worker PWA SDN 002 Karangmulya.
// Sengaja sederhana: halaman tetap diambil dari server (data tugas & nilai harus selalu baru).
// Hanya halaman "Tidak ada internet" dan ikon yang disimpan, untuk ditampilkan saat HP offline.

const CACHE = 'sdn002-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/icons/icon-192.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)))),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Hanya navigasi halaman (GET). Form, unggah foto, dan file tidak disentuh.
    if (event.request.mode !== 'navigate') {
        return;
    }

    event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
});
