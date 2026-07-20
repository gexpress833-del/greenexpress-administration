importScripts('https://www.gstatic.com/firebasejs/11.10.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.10.1/firebase-messaging-compat.js');

let messaging = null;

fetch('/firebase-config')
    .then((response) => response.json())
    .then((config) => {
        if (!config.enabled) return;

        firebase.initializeApp(config.firebase);
        messaging = firebase.messaging();

        messaging.onBackgroundMessage((payload) => {
            const data = payload.data || {};
            const title = data.title || 'Green Express';
            const body = data.body || '';
            const url = data.url || '/notifications';
            const tag = data.tag || 'green-express';

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
            });
        });
    })
    .catch(() => {});

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
