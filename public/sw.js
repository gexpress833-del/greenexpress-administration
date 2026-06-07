const CACHE_NAME = 'green-express-v2';
const STATIC_CACHE = 'green-express-static-v2';
const DYNAMIC_CACHE = 'green-express-dynamic-v2';

const STATIC_ASSETS = [
    '/',
    '/login',
    '/logo.png',
    '/favicon.ico',
    '/manifest.json'
];

// Precache des assets Vite (on les découvre dynamiquement)
async function precacheStaticAssets() {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.keys();
    if (cached.length > 0) return; // déjà precached

    // Assets de base toujours présents
    await cache.addAll(STATIC_ASSETS);

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
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames
                    .filter((name) => ![STATIC_CACHE, DYNAMIC_CACHE].includes(name))
                    .map((name) => caches.delete(name))
            )
        )
    );
    self.clients.claim();
});

// Helpers
function isStaticAsset(url) {
    return url.pathname.startsWith('/build/') ||
           url.pathname === '/' ||
           url.pathname === '/login';
}

function isApiRequest(url) {
    return url.pathname.startsWith('/api/') ||
           url.pathname.startsWith('/sanctum/') ||
           url.search.includes('_token=') ||
           url.search.includes('livewire');
}

function isExternal(url) {
    return !url.origin.includes(self.location.hostname);
}

// Stratégie Stale-While-Revalidate pour les assets statiques
async function staleWhileRevalidate(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
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
    if (networkResponse && networkResponse.status === 200) {
        cache.put(request, networkResponse.clone());
    }
    return networkResponse;
}

// Stratégie Network First pour les pages HTML
async function networkFirst(request) {
    const cache = await caches.open(DYNAMIC_CACHE);

    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) return cached;

        // Offline fallback
        if (request.mode === 'navigate') {
            const offlinePage = await cache.match('/');
            if (offlinePage) return offlinePage;
        }

        return new Response('Hors ligne', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/plain' }
        });
    }
}

// Fetch
self.addEventListener('fetch', (event) => {
    const { request } = event;
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
    } else if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirst(request));
    }
});
