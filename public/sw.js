const CACHE_NAME = 'koriro-pwa-v1';
const urlsToCache = [
    '/',
    '/manifest.json',
    // Assets dasar jika offline (font, css dasar)
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap'
];

// Install event: cache initial assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
    self.skipWaiting();
});

// Activate event: bersihkan cache lama
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event: Network-first untuk API, Cache-first untuk statik assets
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Jangan cache request POST/PUT/DELETE atau webhook Midtrans
    if (request.method !== 'GET') return;

    // Strategi untuk request API (/api/v1/...) -> Network First, fallback to Cache
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache hasil terbaru
                    const resClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, resClone));
                    return response;
                })
                .catch(() => caches.match(request)) // Fallback ke data offline
        );
        return;
    }

    // Strategi untuk asset statik (gambar, js, css Vite) -> Cache First, fallback to Network
    if (url.pathname.match(/\.(png|jpg|jpeg|svg|css|js|woff2)$/)) {
        event.respondWith(
            caches.match(request).then(response => {
                if (response) return response; // Return dari cache jika ada
                
                return fetch(request).then(networkResponse => {
                    const resClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, resClone));
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Default navigasi page (HTML) -> Network First
    event.respondWith(
        fetch(request).catch(() => {
            return caches.match(request).then(response => {
                if (response) return response;
                // Jika offline dan tidak ada di cache, arahkan ke root app shell
                if (request.mode === 'navigate') {
                    return caches.match('/');
                }
            });
        })
    );
});
