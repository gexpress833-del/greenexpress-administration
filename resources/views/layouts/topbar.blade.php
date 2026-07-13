<header class="fixed top-0 left-0 right-0 lg:left-64 z-[90] bg-white/95 dark:bg-gray-800/95 backdrop-blur border-b border-gray-200 dark:border-gray-700 transition-colors duration-200" style="padding-top: env(safe-area-inset-top);">
    <div class="flex items-center justify-between h-16 px-4 lg:px-8">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="flex items-center gap-2 sm:gap-4 ml-auto">
            {{-- Notifications --}}
            <x-notifications-dropdown />

            {{-- Nos repas rapide --}}
            <a href="{{ route('meals.public') }}" class="relative p-2 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition group" title="Nos Repas">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </a>

            {{-- Taux de change rapide --}}
            <a href="{{ route('exchange-rate.show') }}" class="relative p-2 rounded-lg text-amber-500 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition group" title="Taux de change USD/FC">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
            </a>

            {{-- Dark mode toggle --}}
            <button onclick="toggleTheme()" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Changer de thème">
                <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg id="theme-icon-moon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            @if(auth()->user()->isAgent())
                <a href="{{ route('agent.points.index') }}" class="flex items-center gap-1 sm:gap-1.5 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800 rounded-full pl-1.5 pr-2 sm:pl-2 sm:pr-3 py-1 transition" title="Voir mon solde de points">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-blue-700 dark:text-blue-300 leading-none">{{ \App\Models\AgentPoint::where('agent_id', auth()->id())->sum('points') ?: 0 }}<span class="hidden sm:inline"> pts</span></span>
                </a>
            @endif

            <div class="flex items-center gap-2 sm:gap-3 pl-2 sm:pl-3 border-l border-gray-200 dark:border-gray-700">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 sm:gap-2.5 group min-w-0" title="Mon profil">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 group-hover:border-green-500 dark:group-hover:border-green-400 transition shrink-0">
                    @else
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-200 dark:border-gray-600 group-hover:border-green-500 dark:group-hover:border-green-400 transition shrink-0">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn($part) => $part[0] ?? '')->take(2)->join('') }}
                        </div>
                    @endif
                    <div class="hidden md:block min-w-0">
                        <p class="text-sm text-gray-700 dark:text-gray-200 font-medium truncate max-w-[140px] group-hover:text-green-600 dark:group-hover:text-green-400 transition">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wide leading-none">
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 sm:px-3 sm:py-1.5 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium flex items-center gap-1 transition" title="Déconnexion">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline text-sm">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function updateThemeIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        document.getElementById('theme-icon-sun').classList.toggle('hidden', !isDark);
        document.getElementById('theme-icon-moon').classList.toggle('hidden', isDark);
    }
    function toggleTheme() {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateThemeIcon();
    }
    updateThemeIcon();
</script>
