<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed lg:top-0 bottom-0 left-0 z-50 w-64 bg-green-900 dark:bg-green-950 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col" style="top: calc(4rem + env(safe-area-inset-top)); max-width: 85vw;">
    <div class="flex items-center justify-between h-16 px-6 bg-green-950 dark:bg-black/20 shrink-0">
        <a href="{{ route('about') }}" class="flex items-center gap-2 group">
            <img src="/logo.png" alt="Green Express" class="h-10 w-auto transition-transform group-hover:scale-105" onerror="this.style.display='none'; document.getElementById('nav-title').style.display='block';">
            <span id="nav-title" class="text-xl font-bold tracking-wide hidden">Green Express</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 pt-3 pb-4">
        @if(auth()->user()->isAdmin())
            <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>">Dashboard</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'/></svg>">Utilisateurs</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.meals.index') }}" :active="request()->routeIs('admin.meals.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'/></svg>">Repas</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'/></svg>">Catégories</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.orders.index') }}" :active="request()->routeIs('admin.orders.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'/></svg>">Commandes</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.subscriptions.index') }}" :active="request()->routeIs('admin.subscriptions.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 9a2 2 0 11-4 0 2 2 0 014 0zm-4 4a2 2 0 11-4 0 2 2 0 014 0zm4 4a2 2 0 11-4 0 2 2 0 014 0z'/></svg>">Abonnements</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.subscriptions.expiring') }}" :active="request()->routeIs('admin.subscriptions.expiring')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>">À cours / expirés</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.subscription-types.index') }}" :active="request()->routeIs('admin.subscription-types.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'/></svg>">Types d'abonnement</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.deliveries.index') }}" :active="request()->routeIs('admin.deliveries.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path d='M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0z'/></svg>">Livraisons</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.exchange-rates.index') }}" :active="request()->routeIs('admin.exchange-rates.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>">Taux de change</x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.commissions.index') }}" :active="request()->routeIs('admin.commissions.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>">Commissions</x-sidebar-link>
            <div
                x-data
                @click="
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'info', message: 'Redirection vers le Service Client Green Express...' } }));
                    setTimeout(() => window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank'), 800);
                "
                class="flex items-center gap-3 pl-4 pr-5 py-3.5 rounded-lg mb-1 transition border-l-4 border-transparent hover:bg-green-800 cursor-pointer"
            >
                <svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/></svg>
                <span>Réclamations</span>
            </div>
            <x-sidebar-link href="{{ route('admin.activity_logs.index') }}" :active="request()->routeIs('admin.activity_logs.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/></svg>">Logs</x-sidebar-link>
        @endif

        @if(auth()->user()->isAgent())
            <x-sidebar-link href="{{ route('agent.dashboard') }}" :active="request()->routeIs('agent.dashboard')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>">Dashboard</x-sidebar-link>
            <x-sidebar-link href="{{ route('agent.orders.index') }}" :active="request()->routeIs('agent.orders.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'/></svg>">Commandes</x-sidebar-link>
            <x-sidebar-link href="{{ route('agent.subscriptions.index') }}" :active="request()->routeIs('agent.subscriptions.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 9a2 2 0 11-4 0 2 2 0 014 0zm-4 4a2 2 0 11-4 0 2 2 0 014 0zm4 4a2 2 0 11-4 0 2 2 0 014 0z'/></svg>">Abonnements</x-sidebar-link>
            <x-sidebar-link href="{{ route('agent.commissions.index') }}" :active="request()->routeIs('agent.commissions.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'/></svg>">Points & Comm.</x-sidebar-link>
            <x-sidebar-link href="{{ route('agent.withdrawals.index') }}" :active="request()->routeIs('agent.withdrawals.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'/></svg>">Retraits</x-sidebar-link>
        @endif

        @if(auth()->user()->isLivreur())
            <x-sidebar-link href="{{ route('livreur.dashboard') }}" :active="request()->routeIs('livreur.dashboard')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>">Dashboard</x-sidebar-link>
            <x-sidebar-link href="{{ route('livreur.deliveries.index') }}" :active="request()->routeIs('livreur.deliveries.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path d='M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0z'/></svg>">Livraisons</x-sidebar-link>
            <x-sidebar-link href="{{ route('livreur.points.index') }}" :active="request()->routeIs('livreur.points.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'/></svg>">Mes points</x-sidebar-link>
        @endif

        @if(auth()->user()->isCuisinier())
            <x-sidebar-link href="{{ route('cuisinier.dashboard') }}" :active="request()->routeIs('cuisinier.dashboard')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>">Dashboard</x-sidebar-link>
            <x-sidebar-link href="{{ route('cuisinier.orders.index') }}" :active="request()->routeIs('cuisinier.orders.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'/></svg>">Commandes</x-sidebar-link>
        @endif

        @if(auth()->user()->isClient())
            <x-sidebar-link href="{{ route('client.dashboard') }}" :active="request()->routeIs('client.dashboard')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>">Dashboard</x-sidebar-link>
            <x-sidebar-link href="{{ route('client.subscriptions.index') }}" :active="request()->routeIs('client.subscriptions.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 9a2 2 0 11-4 0 2 2 0 014 0zm-4 4a2 2 0 11-4 0 2 2 0 014 0zm4 4a2 2 0 11-4 0 2 2 0 014 0z'/></svg>">Abonnements</x-sidebar-link>
            <x-sidebar-link href="{{ route('client.orders.index') }}" :active="request()->routeIs('client.orders.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'/></svg>">Commandes</x-sidebar-link>
            <div
                x-data
                @click="
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'info', message: 'Redirection vers le Service Client Green Express...' } }));
                    setTimeout(() => window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank'), 800);
                "
                class="flex items-center gap-3 pl-4 pr-5 py-3.5 rounded-lg mb-1 transition border-l-4 border-transparent hover:bg-green-800 cursor-pointer"
            >
                <svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/></svg>
                <span>Réclamations</span>
            </div>
        @endif

        {{-- Nos repas (page publique) --}}
        <div class="pt-2">
            <x-sidebar-link href="{{ route('meals.public') }}" :active="request()->routeIs('meals.public')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'/></svg>">Nos Repas</x-sidebar-link>
        </div>

        {{-- Taux actuel (page publique) --}}
        <div class="pt-2">
            <x-sidebar-link href="{{ route('exchange-rate.show') }}" :active="request()->routeIs('exchange-rate.show')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>">Taux actuel</x-sidebar-link>
        </div>

        {{-- Profil --}}
        <div class="pt-4 mt-4 border-t border-green-800">
            <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')" icon="<svg class='w-5 h-5 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'/></svg>">Mon profil</x-sidebar-link>
        </div>
    </nav>
</aside>

<!-- Overlay mobile -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition.opacity></div>
