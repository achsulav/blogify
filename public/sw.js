const CACHE_NAME = 'blogify-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/css/Main.css',
  '/offline.html'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(ASSETS_TO_CACHE);
      })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Simple passthrough. We removed the offline fallback for local dev.
  event.respondWith(
    fetch(event.request).catch((err) => {
      console.warn('Network request failed, but offline page fallback disabled for local dev', err);
      // Let it fail normally so local dev works without internet
      throw err;
    })
  );
});
