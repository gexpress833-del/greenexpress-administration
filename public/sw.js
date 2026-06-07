const CACHE_NAME = 'green-express-v1';
const STATIC_ASSETS = [
    '/',
    '/login',
    '/build/assets/app.css',
    '/build/assets/app.js',
    '/logo.png',
    '/favicon.ico'
];

// Installation : cache les assets statiques
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).catch(() => {
            // Si un asset est manquant, on continue quand même
        })
    );
    self.skipWaiting();
});

// Activation : nettoyage des anciens caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch : stratégie Network First avec fallback cache
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Ne pas intercepter les requêtes non-GET
    if (request.method !== 'GET') return;

    // Ne pas intercepter les requêtes API ou externes
    if (request.url.includes('/api/') || !request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        fetch(request)
            .then((networkResponse) => {
                // Mettre à jour le cache avec la réponse fraîche
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                // Fallback sur le cache en cas d'erreur réseau
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Si pas dans le cache et c'est une navigation, retourner la page d'accueil
                    if (request.mode === 'navigate') {
                        return caches.match('/');
                    }
                    return new Response('Hors ligne - Green Express', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: { 'Content-Type': 'text/plain' }
                    });
                });
            })
    );
});
