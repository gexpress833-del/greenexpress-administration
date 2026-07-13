<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200 overscroll-none safe-area">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
            @auth
                @include('layouts.sidebar')
            @endauth

            <div class="flex-1 flex flex-col min-w-0 @auth lg:ml-64 @endauth">
                @auth
                    @include('layouts.topbar')
                @endauth

                <main class="flex-1 p-4 lg:p-8" @auth style="padding-top: calc(1rem + 4rem + env(safe-area-inset-top))" @endauth>
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

                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                </main>
            </div>
        </div>
        <x-toasts />
        <x-pwa-install />
        @stack('scripts')
        @include('components.currency-converter')
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' })
                        .then((registration) => {
                            console.log('SW registered:', registration.scope);
                        })
                        .catch((error) => {
                            console.log('SW registration failed:', error);
                        });
                });
            }
        </script>
    </body>
</html>
