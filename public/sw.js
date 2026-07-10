const CACHE_NAME = 'firekontrol-v2';

// Static assets to pre-cache on install
const PRECACHE_URLS = [
    '/',
    '/offline',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// ─── Install: pre-cache static shell ────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// ─── Activate: clean up old caches ──────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ─── Fetch strategy ─────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and non-same-origin requests
    if (request.method !== 'GET' || url.origin !== location.origin) return;

    // Skip form submissions and API-like POST routes
    if (url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/logout')) return;

    // Static assets → cache-first
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?|ttf)$/)) {
        event.respondWith(
        caches.open(CACHE_NAME).then(cache =>
            cache.match(request).then(cached => {

                const networkFetch = fetch(request).then(response => {
                    if (response && response.status === 200) {
                        cache.put(request, response.clone());
                    }
                    return response;
                });

                return cached || networkFetch;
            })
        )
    );
    return;
    }   

    // HTML pages → network-first, fall back to offline page
    event.respondWith(
        fetch(request)
            .then(response => {
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                return response;
            })
            .catch(() =>
                caches.match(request).then(cached =>
                    cached || caches.match('/offline')
                )
            )
    );
});
