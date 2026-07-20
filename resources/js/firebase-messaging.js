const FIREBASE_VERSION = '11.6.0';

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

function setButtonState(button, text, disabled = false) {
    if (!button) return;
    button.textContent = text;
    button.disabled = disabled;
    if (disabled) {
        button.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        button.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

async function startNotifications() {
    const button = document.querySelector('[data-fcm-enable]');
    const panel = button?.closest('[data-fcm-permission-panel]');

    if (!button || !('Notification' in window) || !('serviceWorker' in navigator)) {
        panel?.remove();
        return;
    }

    if (detectPlatform() === 'ios' && !isIosPwa()) {
        setButtonState(button, 'Installer la PWA pour activer les notifications', true);
        return;
    }

    if (Notification.permission === 'denied') {
        setButtonState(button, 'Notifications bloquées dans le navigateur', true);
        return;
    }

    if (Notification.permission === 'granted') {
        setButtonState(button, 'Activation...');
        try {
            await enableNotifications(button, panel);
        } catch (error) {
            console.warn('FCM auto-enable failed:', error);
            setButtonState(button, 'Réessayer');
        }
        return;
    }

    setButtonState(button, 'Activer');
    button.addEventListener('click', async () => {
        setButtonState(button, 'Activation...');
        try {
            await enableNotifications(button, panel);
        } catch (error) {
            console.warn('FCM enable failed:', error);
            setButtonState(button, 'Échec — Réessayer');
        }
    }, { once: true });
}

async function enableNotifications(button, panel) {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        setButtonState(button, 'Notifications refusées', true);
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

    const registration = await Promise.race([
        navigator.serviceWorker.ready,
        new Promise((_, reject) => setTimeout(() => reject(new Error('Service worker non disponible')), 5000)),
    ]);

    const token = await getToken(messaging, {
        vapidKey: config.vapid_key,
        serviceWorkerRegistration: registration,
    });

    if (!token) throw new Error('Firebase n\'a pas fourni de token.');
    await registerToken(token);

    setButtonState(button, 'Notifications activées', true);
    setTimeout(() => panel?.remove(), 2000);

    onMessage(messaging, (payload) => {
        window.dispatchEvent(new CustomEvent('fcm-message', { detail: payload }));

        if (Notification.permission !== 'granted') return;

        const data = payload.data || {};
        const title = data.title || 'Green Express';
        const notification = new Notification(title, {
            body: data.body || '',
            icon: data.icon || '/logo-192.png',
            badge: data.badge || '/logo-192.png',
            tag: data.tag || 'green-express',
        });

        notification.onclick = () => {
            window.location.assign(data.url || '/notifications');
            notification.close();
        };
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => startNotifications().catch((error) => console.warn('FCM non disponible:', error)));
} else {
    startNotifications().catch((error) => console.warn('FCM non disponible:', error));
}
