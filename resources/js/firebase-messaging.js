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

function hasPermissionGranted() {
    const key = 'green-express-fcm-permission-granted';
    return localStorage.getItem(key) === 'true';
}

function setPermissionGranted() {
    const key = 'green-express-fcm-permission-granted';
    localStorage.setItem(key, 'true');
}

function hasTokenRegistered() {
    const key = 'green-express-fcm-token-registered';
    return localStorage.getItem(key) === 'true';
}

function setTokenRegistered() {
    const key = 'green-express-fcm-token-registered';
    localStorage.setItem(key, 'true');
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
    
    // Succès : marquer que le token est enregistré
    setTokenRegistered();
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

async function loadFirebaseMessaging() {
    const [{ initializeApp }, { getMessaging, getToken, onMessage }] = await Promise.all([
        import(`https://www.gstatic.com/firebasejs/${FIREBASE_VERSION}/firebase-app.js`),
        import(`https://www.gstatic.com/firebasejs/${FIREBASE_VERSION}/firebase-messaging.js`),
    ]);

    const config = await fetch('/firebase-config').then((response) => response.json());
    if (!config.enabled) {
        return null;
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
    setTokenRegistered();

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
            window.location.assign(data.url || '/notifications/history');
            notification.close();
        };
    });

    return true;
}

async function startNotifications() {
    const button = document.querySelector('[data-fcm-enable]');
    const panel = button?.closest('[data-fcm-permission-panel]');

    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        panel?.remove();
        return;
    }

    if (detectPlatform() === 'ios' && !isIosPwa()) {
        if (button) {
            setButtonState(button, 'Installer la PWA pour activer les notifications', true);
        }
        return;
    }

    if (Notification.permission === 'denied') {
        if (button) {
            setButtonState(button, 'Notifications bloquées dans le navigateur', true);
        }
        return;
    }

    // Permission déjà accordée ou token déjà enregistré : ne pas afficher le panel
    if (Notification.permission === 'granted' || hasPermissionGranted() || hasTokenRegistered()) {
        try {
            if (!hasTokenRegistered()) {
                await loadFirebaseMessaging();
                setPermissionGranted();
            }
        } catch (error) {
            console.warn('FCM token refresh failed:', error);
        }
        panel?.remove();
        return;
    }

    if (!button) {
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
            button.addEventListener('click', async () => {
                setButtonState(button, 'Activation...');
                try {
                    await enableNotifications(button, panel);
                } catch (retryError) {
                    console.warn('FCM enable failed:', retryError);
                    setButtonState(button, 'Échec — Réessayer');
                }
            }, { once: true });
        }
    }, { once: true });
}

async function enableNotifications(button, panel) {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        setButtonState(button, 'Notifications refusées', true);
        return;
    }

    setPermissionGranted();

    const enabled = await loadFirebaseMessaging();
    if (!enabled) {
        panel?.remove();
        return;
    }

    setButtonState(button, 'Notifications activées', true);
    setTimeout(() => panel?.remove(), 2000);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => startNotifications().catch((error) => console.warn('FCM non disponible:', error)));
} else {
    startNotifications().catch((error) => console.warn('FCM non disponible:', error));
}
