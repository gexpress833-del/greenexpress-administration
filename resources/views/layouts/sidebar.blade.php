<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-green-900 dark:bg-green-950 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0">
    <div class="flex items-center justify-between h-16 px-6 bg-green-950 dark:bg-black/20">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="/logo.png" alt="Green Express" class="h-8 w-auto" onerror="this.style.display='none'; document.getElementById('nav-title').style.display='block';">
            <span id="nav-title" class="text-xl font-bold tracking-wide hidden">Green Express</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="px-4 py-4 space-y-1">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.users.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Utilisateurs</span>
            </a>
            <a href="{{ route('admin.meals.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.meals.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Repas</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.orders.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Commandes</span>
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.subscriptions.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Abonnements</span>
            </a>
            <a href="{{ route('admin.subscription-types.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.subscription-types.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Types d'abonnement</span>
            </a>
            <a href="{{ route('admin.deliveries.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.deliveries.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Livraisons</span>
            </a>
            <a href="{{ route('admin.exchange-rates.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.exchange-rates.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Taux de change</span>
            </a>
            <a href="{{ route('admin.commissions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.commissions.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Commissions</span>
            </a>
            <a href="{{ route('admin.complaints.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.complaints.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Réclamations</span>
            </a>
            <a href="{{ route('admin.activity_logs.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.activity_logs.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Logs</span>
            </a>
        @endif

        @if(auth()->user()->isAgent())
            <a href="{{ route('agent.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('agent.dashboard') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('agent.orders.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('agent.orders.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Commandes</span>
            </a>
            <a href="{{ route('agent.subscriptions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('agent.subscriptions.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Abonnements</span>
            </a>
            <a href="{{ route('agent.commissions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('agent.commissions.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Points & Comm.</span>
            </a>
            <a href="{{ route('agent.withdrawals.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('agent.withdrawals.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Retraits</span>
            </a>
        @endif

        @if(auth()->user()->isLivreur())
            <a href="{{ route('livreur.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('livreur.dashboard') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('livreur.deliveries.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('livreur.deliveries.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Livraisons</span>
            </a>
        @endif

        @if(auth()->user()->isCuisinier())
            <a href="{{ route('cuisinier.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('cuisinier.dashboard') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('cuisinier.orders.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('cuisinier.orders.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Commandes</span>
            </a>
        @endif

        @if(auth()->user()->isClient())
            <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('client.dashboard') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Dashboard</span>
            </a>
            <a href="{{ route('client.subscriptions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('client.subscriptions.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Abonnements</span>
            </a>
            <a href="{{ route('client.orders.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('client.orders.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Commandes</span>
            </a>
            <a href="{{ route('client.complaints.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('client.complaints.*') ? 'bg-green-800' : '' }}">
                <span class="ml-2">Réclamations</span>
            </a>
        @endif

        {{-- Profil --}}
        <div class="pt-4 mt-4 border-t border-green-800">
            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('profile.*') ? 'bg-green-800' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="ml-2">Mon profil</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Overlay mobile -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition.opacity></div>
