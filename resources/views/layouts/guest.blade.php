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

        <title>Green Express</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || 'light';
                if (theme === 'dark') document.documentElement.classList.add('dark');
            })();
        </script>
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased overscroll-none">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900" style="padding-top: max(1.5rem, env(safe-area-inset-top)); padding-bottom: env(safe-area-inset-bottom);">
            <div class="mt-6 w-full sm:max-w-md px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-xl dark:border dark:border-gray-700">
                {{ $slot }}
            </div>
        </div>
        <x-pwa-install />
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' })
                        .then((registration) => {
                            console.log('SW registered:', registration.scope);
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
                        })
                        .catch((error) => {
                            console.log('SW registration failed:', error);
                        });
                });
            }
        </script>
    </body>
</html>
