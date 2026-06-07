<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#16a34a">
        <meta name="background-color" content="#ffffff">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Green Express">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="description" content="Application de gestion Green Express - Livraison de repas et administration">

        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/logo.png">

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
    <body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
            @auth
                @include('layouts.sidebar')
            @endauth

            <div class="flex-1 flex flex-col min-w-0">
                @auth
                    @include('layouts.topbar')
                @endauth

                <main class="flex-1 p-4 lg:p-8">
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

                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
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
