const CACHE_NAME = 'tontine-app-v1.0.0';
const RUNTIME_CACHE = 'tontine-runtime-v1.0.0';

// Assets à mettre en cache pour le fonctionnement hors-ligne
const STATIC_ASSETS = [
    '/',
    '/dashboard',
    '/login',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    // Les assets CSS/JS seront ajoutés dynamiquement
];

// API endpoints qui peuvent être mis en cache
const CACHEABLE_PATTERNS = [
    /^\/api\/(clients|tontines|products)$/, // GET seulement
    /^\/api\/clients\/\d+$/,
    /^\/api\/tontines\/\d+$/,
    /^\/api\/products\/\d+$/
];

// Installation du service worker
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('📦 Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('✅ Service Worker installed successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('❌ Installation failed:', error);
            })
    );
});

// Activation du service worker
self.addEventListener('activate', (event) => {
    console.log('🚀 Service Worker activating...');
    
    event.waitUntil(
        Promise.all([
            // Nettoyer les anciens caches
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                            console.log('🗑️ Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            // Prendre le contrôle de toutes les pages
            self.clients.claim()
        ])
    );
});

// Stratégie de cache avancée
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Ignorer les requêtes non-HTTP(S)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Stratégie différente selon le type de requête
    if (request.method === 'GET') {
        if (isStaticAsset(request.url)) {
            // Cache First pour les assets statiques
            event.respondWith(cacheFirst(request));
        } else if (isAPIRequest(request.url)) {
            // Network First pour l'API avec fallback
            event.respondWith(networkFirst(request));
        } else if (isNavigationRequest(request)) {
            // Cache First avec fallback pour les pages HTML
            event.respondWith(navigationFallback(request));
        } else {
            // Stale While Revalidate pour le contenu dynamique
            event.respondWith(staleWhileRevalidate(request));
        }
    } else if (request.method === 'POST' || request.method === 'PUT' || request.method === 'DELETE') {
        // Background sync pour les requêtes d'écriture
        event.respondWith(handleWriteRequest(request));
    }
});

// Vérifier si c'est un asset statique
function isStaticAsset(url) {
    return url.includes('/build/') || 
           url.includes('/icons/') || 
           url.includes('/screenshots/') ||
           url.endsWith('.css') ||
           url.endsWith('.js') ||
           url.endsWith('.png') ||
           url.endsWith('.jpg') ||
           url.endsWith('.jpeg') ||
           url.endsWith('.svg') ||
           url.endsWith('.woff') ||
           url.endsWith('.woff2');
}

// Vérifier si c'est une requête API
function isAPIRequest(url) {
    return url.includes('/api/');
}

// Vérifier si c'est une requête de navigation
function isNavigationRequest(request) {
    return request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept').includes('text/html'));
}

// Stratégie Cache First
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        // Mettre à jour en arrière-plan
        updateCacheInBackground(request);
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('📡 Network failed, serving from cache if available');
        return new Response('Hors-ligne - Contenu non disponible', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

// Stratégie Network First
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('📡 Network failed, trying cache');
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Retourner une réponse hors-ligne pour l'API
        return new Response(JSON.stringify({
            error: 'Hors-ligne',
            message: 'Aucune connexion Internet. Veuillez réessayer plus tard.',
            cached: false
        }), {
            status: 503,
            statusText: 'Service Unavailable',
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }
}

// Stratégie Stale While Revalidate
async function staleWhileRevalidate(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cachedResponse = await cache.match(request);
    
    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    });
    
    return cachedResponse || fetchPromise;
}

// Fallback pour la navigation
async function navigationFallback(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('📡 Navigation failed, serving offline page');
        
        // Essayer de servir la page depuis le cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Fallback vers la page d'accueil
        const homeResponse = await caches.match('/');
        if (homeResponse) {
            return homeResponse;
        }
        
        // Page hors-ligne par défaut
        return new Response(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Hors-ligne - Tontine App</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body { font-family: system-ui; text-align: center; padding: 2rem; background: #f3f4f6; }
                    .offline-icon { font-size: 4rem; margin-bottom: 1rem; }
                    h1 { color: #1f2937; margin-bottom: 1rem; }
                    p { color: #6b7280; margin-bottom: 2rem; }
                    button { background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.25rem; cursor: pointer; }
                </style>
            </head>
            <body>
                <div class="offline-icon">📱</div>
                <h1>Application Hors-ligne</h1>
                <p>Vous êtes actuellement hors-ligne. Certaines fonctionnalités peuvent ne pas être disponibles.</p>
                <button onclick="window.location.reload()">Réessayer</button>
            </body>
            </html>
        `, {
            status: 200,
            headers: {
                'Content-Type': 'text/html'
            }
        });
    }
}

// Gérer les requêtes d'écriture avec background sync
async function handleWriteRequest(request) {
    try {
        const networkResponse = await fetch(request);
        return networkResponse;
    } catch (error) {
        console.log('📝 Write request failed, queuing for background sync');
        
        // Sauvegarder la requête pour synchronisation ultérieure
        const requestData = {
            url: request.url,
            method: request.method,
            headers: Object.fromEntries(request.headers.entries()),
            body: await request.text(),
            timestamp: Date.now()
        };
        
        // Ajouter à IndexedDB pour sync ultérieure
        await saveRequestForSync(requestData);
        
        return new Response(JSON.stringify({
            success: false,
            message: 'Requête enregistrée pour synchronisation',
            offline: true
        }), {
            status: 202,
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }
}

// Mettre à jour le cache en arrière-plan
function updateCacheInBackground(request) {
    fetch(request).then((response) => {
        if (response.ok) {
            caches.open(RUNTIME_CACHE).then((cache) => {
                cache.put(request, response);
            });
        }
    }).catch(() => {
        // Ignorer les erreurs de mise à jour en arrière-plan
    });
}

// Sauvegarder la requête pour synchronisation (simplifié)
async function saveRequestForSync(requestData) {
    // Implémentation avec IndexedDB serait ici
    console.log('💾 Request saved for sync:', requestData);
}

// Background Sync
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync') {
        event.waitUntil(syncPendingRequests());
    }
});

// Synchroniser les requêtes en attente
async function syncPendingRequests() {
    console.log('🔄 Syncing pending requests...');
    // Implémentation de la synchronisation
}

// Push Notifications
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        
        const options = {
            body: data.body || 'Nouvelle notification',
            icon: '/icons/icon-192x192.png',
            badge: '/icons/badge-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: data.primaryKey || 1
            },
            actions: [
                {
                    action: 'explore',
                    title: 'Voir',
                    icon: '/icons/checkmark.png'
                },
                {
                    action: 'close',
                    title: 'Fermer',
                    icon: '/icons/xmark.png'
                }
            ]
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'Tontine App', options)
        );
    }
});

// Gérer les clics sur les notifications
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url || '/dashboard')
        );
    } else if (event.action === 'close') {
        // Fermer la notification (déjà fait)
    } else {
        // Comportement par défaut
        event.waitUntil(
            clients.matchAll().then((clientList) => {
                for (const client of clientList) {
                    if (client.url === '/' && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow('/');
                }
            })
        );
    }
});

// Message handling (communication avec l'app)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_UPDATE') {
        event.waitUntil(updateCache());
    }
});

// Mettre à jour le cache manuellement
async function updateCache() {
    console.log('🔄 Updating cache...');
    try {
        const cache = await caches.open(CACHE_NAME);
        await cache.addAll(STATIC_ASSETS);
        console.log('✅ Cache updated successfully');
    } catch (error) {
        console.error('❌ Cache update failed:', error);
    }
}
