const CACHE_NAME = 'green-express-v7';
const STATIC_CACHE = 'green-express-static-v7';
const DYNAMIC_CACHE = 'green-express-dynamic-v7';

const STATIC_ASSETS = [
    '/logo.png',
    '/logo-192.png',
    '/logo-512.png',
    '/favicon.ico',
    '/manifest.json'
];

// Precache des assets Vite (on les découvre dynamiquement)
async function precacheStaticAssets() {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.keys();
    if (cached.length > 0) return; // déjà precached

    // Assets de base toujours présents
    await Promise.allSettled(
        STATIC_ASSETS.map((asset) =>
            fetch(asset, { cache: 'reload' }).then((response) => {
                if (response && response.ok && !response.redirected) {
                    return cache.put(asset, response);
                }
            })
        )
    );

    // Découverte des assets buildés par Vite
    try {
        const manifestResponse = await fetch('/build/manifest.json');
        if (manifestResponse.ok) {
            const manifest = await manifestResponse.json();
            const assets = [];
            for (const key in manifest) {
                const file = manifest[key];
                if (file.file) assets.push('/build/' + file.file);
                if (file.css) file.css.forEach((css) => assets.push('/build/' + css));
            }
            await cache.addAll(assets);
        }
    } catch (e) {
        // manifest.json peut ne pas exister
    }
}

// Installation
self.addEventListener('install', (event) => {
    event.waitUntil(precacheStaticAssets());
    self.skipWaiting();
});

// Activation
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) =>
                Promise.all(
                    cacheNames
                        .filter((name) => name.startsWith('green-express-') && ![CACHE_NAME, STATIC_CACHE, DYNAMIC_CACHE].includes(name))
                        .map((name) => caches.delete(name))
                )
            )
            .then(() => self.clients.claim())
    );
});

// Helpers
function isStaticAsset(url) {
    return url.pathname.startsWith('/build/') ||
           url.pathname === '/manifest.json' ||
           url.pathname === '/logo.png' ||
           url.pathname === '/logo-192.png' ||
           url.pathname === '/favicon.ico';
}

function isApiRequest(url) {
    return url.pathname.startsWith('/api/') ||
           url.pathname.startsWith('/sanctum/') ||
           url.search.indexOf('_token=') !== -1 ||
           url.search.indexOf('livewire') !== -1;
}

function isExternal(url) {
    return !url.origin.includes(self.location.hostname);
}

// Stratégie Stale-While-Revalidate pour les assets statiques
async function staleWhileRevalidate(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200 && !networkResponse.redirected) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    }).catch(() => cached);

    return cached || fetchPromise;
}

// Stratégie Cache First pour les assets buildés (immuable)
async function cacheFirst(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);
    if (cached) return cached;

    const networkResponse = await fetch(request);
    if (networkResponse && networkResponse.status === 200 && !networkResponse.redirected) {
        cache.put(request, networkResponse.clone());
    }
    return networkResponse;
}

// Fetch : on n'intercepte que les assets statiques pour éviter les faux hors-ligne
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;
    if (isExternal(url)) return;
    if (isApiRequest(url)) return;

    if (isStaticAsset(url)) {
        if (url.pathname.startsWith('/build/')) {
            event.respondWith(cacheFirst(request));
        } else {
            event.respondWith(staleWhileRevalidate(request));
        }
    }
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
