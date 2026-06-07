<header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-8 transition-colors duration-200">
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div class="flex items-center gap-2 sm:gap-4 ml-auto">
        {{-- Notifications --}}
        <x-notifications-dropdown />

        {{-- Dark mode toggle --}}
        <button onclick="toggleTheme()" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Changer de thème">
            <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg id="theme-icon-moon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        </button>

        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 dark:text-gray-300 hidden md:block truncate max-w-[120px] hover:text-green-600 dark:hover:text-green-400 transition" title="Mon profil">
            {{ auth()->user()->name }}
        </a>
        <span class="hidden xs:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 uppercase">
            {{ auth()->user()->role }}
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium flex items-center gap-1" title="Déconnexion">
                <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="hidden sm:inline">Déconnexion</span>
            </button>
        </form>
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
