// Service Worker désactivé pour éviter les conflits CORS
// Le cache sera géré par le navigateur automatiquement

self.addEventListener('install', event => {
    // Skip waiting pour éviter les problèmes de cache
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    // Nettoyer les anciens caches
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});

// Ne pas intercepter les fetch pour éviter les conflits CORS
self.addEventListener('fetch', event => {
    // Laisser le navigateur gérer les requêtes normalement
    return;
});
