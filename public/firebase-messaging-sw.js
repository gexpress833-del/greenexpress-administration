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
            const title = payload.notification?.title || payload.data?.title || 'Green Express';
            const body = payload.notification?.body || payload.data?.body || '';
            const url = payload.data?.url || payload.fcmOptions?.link || '/notifications';

            self.registration.showNotification(title, {
                body,
                icon: '/logo-192.png',
                badge: '/logo-192.png',
                data: { url },
                tag: payload.data?.notification_id || 'green-express-notification',
            });
        });
    })
    .catch(() => {});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
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
