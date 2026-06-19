<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#16a34a">
        <meta name="background-color" content="#ffffff">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Green Express">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="description" content="Application de gestion Green Express - Livraison de repas et administration">

        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/logo.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/logo-192.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/logo-192.png">

        <title>@yield('title', 'Green Express')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || 'light';
                if (theme === 'dark') document.documentElement.classList.add('dark');
            })();
        </script>
        <style>
            /* View Transitions API native */
            @view-transition {
                navigation: auto;
            }
            /* Custom transition styles */
            ::view-transition-old(root) {
                animation: fade-out 0.25s ease-out forwards;
            }
            ::view-transition-new(root) {
                animation: fade-in 0.35s ease-out forwards;
            }
            @keyframes fade-out {
                from { opacity: 1; transform: scale(1); }
                to   { opacity: 0; transform: scale(0.985); }
            }
            @keyframes fade-in {
                from { opacity: 0; transform: scale(1.015); }
                to   { opacity: 1; transform: scale(1); }
            }
            /* Fallback overlay transition */
            #page-transition {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: #16a34a;
                opacity: 0;
                pointer-events: none;
                transform: scaleX(0);
                transform-origin: left;
                transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
            }
            #page-transition.active {
                opacity: 1;
                transform: scaleX(1);
                transform-origin: right;
            }
            #page-transition.exit {
                transform-origin: right;
                transform: scaleX(0);
                opacity: 0;
            }
            /* Smooth page entrance */
            .page-enter {
                animation: page-enter 0.5s ease-out forwards;
            }
            @keyframes page-enter {
                from { opacity: 0; transform: translateY(12px); }
                to   { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200 overscroll-none safe-area">
        <div id="page-transition"></div>
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
            @auth
                @include('layouts.sidebar')
            @endauth

            <div class="flex-1 flex flex-col min-w-0 @auth lg:ml-64 @endauth">
                @auth
                    @include('layouts.topbar')
                @endauth

                <main id="main-content" class="flex-1 p-4 lg:p-8 page-enter" @auth style="padding-top: calc(1rem + 4rem + env(safe-area-inset-top))" @endauth>
                    @if(session('success'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-100">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Opération réussie</p>
                                    <p class="text-sm opacity-90">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('whatsapp_link'))
                        <div class="mb-4 rounded-xl border border-green-200 bg-white p-4 text-slate-800 shadow-sm dark:border-green-800 dark:bg-slate-900 dark:text-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold">Message WhatsApp prêt</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Vérifiez le contenu avant envoi au destinataire.</p>
                            </div>
                            <a href="{{ session('whatsapp_link') }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                                Ouvrir WhatsApp
                            </a>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm dark:border-red-800 dark:bg-red-950/40 dark:text-red-100">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Action impossible</p>
                                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
                        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 shadow-sm dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100">
                            <p class="mb-2 text-sm font-semibold">Veuillez corriger les informations suivantes :</p>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                </main>
            </div>
        </div>
        <x-pwa-install />
        @stack('scripts')
        @include('components.currency-converter')
        <script>
            (function() {
                // ========== PAGE TRANSITIONS ==========
                const supportsViewTransition = document.startViewTransition;
                const overlay = document.getElementById('page-transition');

                function isInternalLink(el) {
                    return el && el.tagName === 'A' && el.href &&
                           el.href.startsWith(window.location.origin) &&
                           !el.hasAttribute('download') &&
                           el.getAttribute('target') !== '_blank' &&
                           !el.href.includes('#') &&
                           !el.closest('[data-no-transition]');
                }

                document.addEventListener('click', function(e) {
                    const link = e.composedPath ? e.composedPath().find(el => el.tagName === 'A') : e.target.closest('a');
                    if (!link || !isInternalLink(link)) return;
                    if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
                    const url = new URL(link.href);
                    if (url.pathname === window.location.pathname && url.search === window.location.search) return;

                    e.preventDefault();

                    if (supportsViewTransition) {
                        document.startViewTransition(() => {
                            window.location.href = link.href;
                        });
                    } else {
                        overlay.classList.add('active');
                        setTimeout(() => {
                            window.location.href = link.href;
                        }, 300);
                    }
                });

                // Reveal on back/forward navigation (pageshow)
                window.addEventListener('pageshow', function(e) {
                    if (e.persisted) {
                        overlay.classList.remove('active');
                        document.getElementById('main-content')?.classList.add('page-enter');
                    }
                });

                // ========== SERVICE WORKER ==========
                if ('serviceWorker' in navigator) {
                    window.addEventListener('load', () => {
                        navigator.serviceWorker.register('/sw.js', { scope: '/' })
                            .then((registration) => {
                                if (registration.waiting) {
                                    registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                                }
                                registration.addEventListener('updatefound', () => {
                                    const worker = registration.installing;
                                    if (worker) {
                                        worker.addEventListener('statechange', () => {
                                            if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                                                worker.postMessage({ type: 'SKIP_WAITING' });
                                            }
                                        });
                                    }
                                });

                                // ========== PUSH NOTIFICATIONS ==========
                                setupPushNotifications(registration);
                            })
                            .catch(() => {});
                    });
                }

                function setupPushNotifications(registration) {
                    if (!('Notification' in window) || !registration.pushManager) return;

                    // Ask permission after a short delay (not intrusive on first load)
                    let asked = localStorage.getItem('push-asked');
                    if (!asked && Notification.permission === 'default') {
                        setTimeout(() => {
                            Notification.requestPermission().then(permission => {
                                localStorage.setItem('push-asked', '1');
                                if (permission === 'granted') {
                                    subscribeToPush(registration);
                                }
                            });
                        }, 3000);
                    } else if (Notification.permission === 'granted') {
                        subscribeToPush(registration);
                    }
                }

                async function subscribeToPush(registration) {
                    try {
                        const existing = await registration.pushManager.getSubscription();
                        if (existing) {
                            await sendSubscriptionToServer(existing);
                            return;
                        }

                        // Fetch VAPID public key from backend
                        const keyRes = await fetch('/api/vapid-public-key');
                        const keyData = await keyRes.json();
                        if (!keyData.publicKey) return;

                        const converted = urlBase64ToUint8Array(keyData.publicKey);

                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: converted
                        });
                        await sendSubscriptionToServer(subscription);
                    } catch (err) {
                        console.warn('Push subscription failed', err);
                    }
                }

                async function sendSubscriptionToServer(subscription) {
                    try {
                        await fetch('/api/push-subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify(subscription.toJSON())
                        });
                    } catch (e) {
                        console.warn('Could not send push subscription to server', e);
                    }
                }

                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                    const raw = window.atob(base64);
                    const out = new Uint8Array(raw.length);
                    for (let i = 0; i < raw.length; ++i) {
                        out[i] = raw.charCodeAt(i);
                    }
                    return out;
                }
            })();
        </script>
    </body>
</html>
