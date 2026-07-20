const CACHE_NAME = 'green-express-v13';
const STATIC_CACHE = 'green-express-static-v13';
const DYNAMIC_CACHE = 'green-express-dynamic-v13';

const STATIC_ASSETS = [
    '/logo.png',
    '/logo-192.png',
    '/logo-512.png',
    '/favicon.ico',
    '/manifest.json'
];

async function precacheStaticAssets() {
    const cache = await caches.open(STATIC_CACHE);

    await Promise.allSettled(
        STATIC_ASSETS.map((asset) =>
            fetch(asset, { cache: 'reload' }).then((response) => {
                if (response && response.ok && !response.redirected) {
                    return cache.put(asset, response);
                }
            })
        )
    );

    try {
        const manifestResponse = await fetch('/build/manifest.json', { cache: 'reload' });
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
    } catch (e) {}
}

self.addEventListener('install', (event) => {
    event.waitUntil(precacheStaticAssets());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((name) => name.startsWith('green-express-') && ![CACHE_NAME, STATIC_CACHE, DYNAMIC_CACHE].includes(name))
                    .map((name) => caches.delete(name))
            ))
    );
});

function isStaticAsset(url) {
    return url.pathname.startsWith('/build/') ||
        url.pathname === '/manifest.json' ||
        url.pathname === '/logo.png' ||
        url.pathname === '/logo-192.png' ||
        url.pathname === '/logo-512.png' ||
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

async function cacheFirst(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);
    if (cached) return cached;

    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200 && !networkResponse.redirected) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (e) {
        return new Response('', { status: 504, statusText: 'Gateway Timeout' });
    }
}

async function handleNavigation(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (e) {
        const cache = await caches.open(DYNAMIC_CACHE);
        const cached = await cache.match(request);
        if (cached) return cached;

        return new Response(
            '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hors ligne</title><style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#0b1220;color:#fff;text-align:center;padding:2rem;}h1{color:#22c55e;font-size:1.5rem;}p{color:#94a3b8;}</style></head><body><div><h1>Green Express</h1><p>Vous \u00eates hors ligne. V\u00e9rifiez votre connexion internet et r\u00e9essayez.</p></div></body></html>',
            { headers: { 'Content-Type': 'text/html; charset=utf-8' }, status: 200 }
        );
    }
}

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;
    if (isExternal(url)) return;
    if (isApiRequest(url)) return;

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(request));
        return;
    }

    if (isStaticAsset(url)) {
        if (url.pathname.startsWith('/build/')) {
            event.respondWith(cacheFirst(request));
        } else {
            event.respondWith(staleWhileRevalidate(request));
        }
    }
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        if (event.data) {
            payload = event.data.json();
        }
    } catch (e) {
        console.warn('Unable to parse push payload:', e);
    }

    const data = payload.data || payload.notification || payload || {};
    const title = data.title || payload.title || 'Green Express';
    const body = data.body || payload.body || '';
    const url = data.url || '/notifications';
    const tag = data.tag || 'green-express';

    event.waitUntil(
        self.registration.showNotification(title, {
            body,
            icon: data.icon || '/logo-192.png',
            badge: data.badge || '/logo-192.png',
            tag,
            data: { url, notification_id: data.notification_id },
            actions: [
                { action: 'open', title: 'Voir' },
                { action: 'close', title: 'Fermer' },
            ],
        }),
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') return;

    const targetUrl = new URL(event.notification.data?.url || '/notifications', self.location.origin).href;

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }

            return self.clients.openWindow(targetUrl);
        }),
    );
});
