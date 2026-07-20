const FIREBASE_VERSION = '11.10.1';

function isIosPwa() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
}

function detectPlatform() {
    const userAgent = navigator.userAgent.toLowerCase();
    if (/iphone|ipad|ipod/.test(userAgent)) return 'ios';
    if (/android/.test(userAgent)) return 'android';
    return 'web';
}

function getDeviceId() {
    const key = 'green-express-fcm-device-id';
    let deviceId = localStorage.getItem(key);
    if (!deviceId) {
        deviceId = crypto.randomUUID();
        localStorage.setItem(key, deviceId);
    }
    return deviceId;
}

async function registerToken(token) {
    const response = await fetch('/notifications/fcm-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            token,
            platform: detectPlatform(),
            device_id: getDeviceId(),
        }),
    });

    if (!response.ok) throw new Error('Le token FCM n\'a pas pu être enregistré.');
}

async function startNotifications() {
    const button = document.querySelector('[data-fcm-enable]');
    const panel = button?.closest('[data-fcm-permission-panel]');
    if (!button || !('Notification' in window) || !('serviceWorker' in navigator)) {
        panel?.remove();
        return;
    }

    if (detectPlatform() === 'ios' && !isIosPwa()) {
        button.textContent = 'Installer la PWA pour activer les notifications';
        button.disabled = true;
        return;
    }

    const [{ initializeApp }, { getMessaging, getToken, onMessage }] = await Promise.all([
        import(`https://www.gstatic.com/firebasejs/${FIREBASE_VERSION}/firebase-app.js`),
        import(`https://www.gstatic.com/firebasejs/${FIREBASE_VERSION}/firebase-messaging.js`),
    ]);
    const config = await fetch('/firebase-config').then((response) => response.json());
    if (!config.enabled) {
        panel?.remove();
        return;
    }

    const app = initializeApp(config.firebase);
    const messaging = getMessaging(app);
    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });

    const enable = async () => {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            button.textContent = 'Notifications refusées';
            return;
        }

        const token = await getToken(messaging, {
            vapidKey: config.vapid_key,
            serviceWorkerRegistration: registration,
        });

        if (!token) throw new Error('Firebase n\'a pas fourni de token.');
        await registerToken(token);
        panel?.remove();
    };

    if (Notification.permission === 'granted') {
        await enable();
    } else if (Notification.permission === 'denied') {
        button.textContent = 'Notifications bloquées dans le navigateur';
        button.disabled = true;
    } else {
        button.addEventListener('click', enable, { once: true });
    }

    onMessage(messaging, (payload) => {
        window.dispatchEvent(new CustomEvent('fcm-message', { detail: payload }));

        if (Notification.permission !== 'granted') return;

        const title = payload.data?.title || 'Green Express';
        const notification = new Notification(title, {
            body: payload.data?.body || '',
            icon: '/logo-192.png',
            tag: payload.data?.notification_id || 'green-express-notification',
        });

        notification.onclick = () => {
            window.location.assign(payload.data?.url || '/notifications');
            notification.close();
        };
    });
}

window.addEventListener('DOMContentLoaded', () => {
    startNotifications().catch((error) => console.warn('FCM non disponible:', error));
});
