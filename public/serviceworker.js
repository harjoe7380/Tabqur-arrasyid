var staticCacheName = "pwa-v1";
var filesToCache = [
    '/',
    '/offline',
    '/icon.svg'
];

// Cache on install
self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    // Only cache GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request).then(response => {
                return response || caches.match('/offline');
            });
        })
    )
});
