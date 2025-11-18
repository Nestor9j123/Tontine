// Service Worker pour mode hors ligne - Tontine App
const CACHE_NAME = 'tontine-app-v1';
const OFFLINE_URL = '/offline.html';

// Ressources critiques à mettre en cache
const CORE_CACHE_URLS = [
    '/',
    '/dashboard',
    '/clients',
    '/products',
    '/payments',
    '/notifications',
    OFFLINE_URL,
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/icons/icon-192.png'
];

// Installation : Mise en cache des ressources critiques
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(CORE_CACHE_URLS.map(url => new Request(url, {
                    cache: 'reload'
                })));
            })
            .catch(err => {
                // Continuer même si certaines ressources échouent
            })
    );
    self.skipWaiting();
});

// Activation : Nettoyage des anciens caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Stratégie de cache : Network First avec fallback cache
self.addEventListener('fetch', event => {
    // Ignorer les requêtes non-GET et les requêtes API externes
    if (event.request.method !== 'GET' || 
        event.request.url.includes('api.') ||
        event.request.url.includes('google') ||
        event.request.url.includes('facebook')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Si la réponse est OK, la mettre en cache (sauf pages dynamiques)
                if (response.status === 200 && !event.request.url.includes('/api/')) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // En cas d'échec réseau, chercher en cache
                return caches.match(event.request)
                    .then(cachedResponse => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        
                        // Pour les pages HTML, retourner la page offline
                        if (event.request.headers.get('accept').includes('text/html')) {
                            return caches.match(OFFLINE_URL);
                        }
                        
                        // Pour les autres ressources, retourner une réponse d'erreur
                        return new Response('Ressource non disponible hors ligne', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    });
            })
    );
});
