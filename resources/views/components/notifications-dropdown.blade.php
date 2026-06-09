<div x-data="notifications()" x-init="initNotifications()" class="relative">
    <button type="button" @click="open = !open" class="relative z-10 p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Notifications">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <template x-if="unreadCount > 0">
            <span x-text="unreadCount > 9 ? '9+' : unreadCount" class="absolute top-0 right-0 block h-4 min-w-[1rem] px-1 text-[10px] leading-4 font-bold text-white bg-red-500 rounded-full text-center"></span>
        </template>
    </button>

    <!-- Overlay transparent pour fermer au clic extérieur -->
    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-[9998]"></div>

    <div x-show="open" x-cloak x-transition class="fixed right-2 sm:right-4 top-20 w-[calc(100%-1rem)] sm:w-80 max-w-[20rem] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-[9999] overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifications</h3>
            <div class="flex items-center gap-2">
                <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Tout marquer comme lu</button>
                <button @click="open = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Fermer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="max-h-80 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucune notification
                </div>
            </template>
            <template x-for="notif in notifications" :key="notif.id">
                <div @click="markAsRead(notif)" class="cursor-pointer px-4 py-3 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                     :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notif.read_at }">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                             :class="iconBgColor(notif.data.color)">
                            <svg class="w-4 h-4" :class="iconTextColor(notif.data.color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(notif.data.icon)" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100" x-text="notif.data.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="notif.data.message"></p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1" x-text="formatDate(notif.created_at)"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <a href="{{ route('notifications.history') }}" class="block text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                Voir l'historique complet
            </a>
        </div>
    </div>
</div>

<script>
    function notifications() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,
            initNotifications() {
                this.fetchNotifications();
                setInterval(() => this.fetchNotifications(), 30000);
            },
            async fetchNotifications() {
                try {
                    const res = await fetch('{{ route('notifications.index') }}');
                    const data = await res.json();
                    this.notifications = data;
                    this.unreadCount = data.filter(n => !n.read_at).length;
                } catch (e) {
                    console.error(e);
                }
            },
            async markAsRead(notif) {
                if (notif.read_at) {
                    if (notif.data.url) window.location.href = notif.data.url;
                    return;
                }
                try {
                    await fetch('{{ url('/notifications') }}/' + notif.id + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        }
                    });
                    notif.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    if (notif.data.url) window.location.href = notif.data.url;
                } catch (e) {
                    console.error(e);
                }
            },
            async markAllAsRead() {
                try {
                    await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        }
                    });
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                } catch (e) {
                    console.error(e);
                }
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
            },
            iconBgColor(color) {
                const map = {
                    green: 'bg-green-100 dark:bg-green-900/30',
                    blue: 'bg-blue-100 dark:bg-blue-900/30',
                    amber: 'bg-amber-100 dark:bg-amber-900/30',
                    purple: 'bg-purple-100 dark:bg-purple-900/30',
                    red: 'bg-red-100 dark:bg-red-900/30',
                };
                return map[color] || 'bg-gray-100 dark:bg-gray-700';
            },
            iconTextColor(color) {
                const map = {
                    green: 'text-green-600 dark:text-green-300',
                    blue: 'text-blue-600 dark:text-blue-300',
                    amber: 'text-amber-600 dark:text-amber-300',
                    purple: 'text-purple-600 dark:text-purple-300',
                    red: 'text-red-600 dark:text-red-300',
                };
                return map[color] || 'text-gray-600 dark:text-gray-300';
            },
            iconPath(icon) {
                const map = {
                    'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'clipboard-list': 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    'truck': 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
                    'banknote': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'user-check': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    'refresh': 'M4 4v5h.582m15.356 2A8.966 8.966 0 016.716 4.044m0 0L4.957 6.003M20 20v-5h-.581m0 0a8.966 8.966 0 01-12.986 4.93m0 0L19.043 17.997',
                    'pause-circle': 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'play-circle': 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'x-circle': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                };
                return map[icon] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
            }
        }
    }
</script>
