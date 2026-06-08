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
            <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Tout marquer comme lu</button>
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
                        <div class="mt-0.5 w-2 h-2 rounded-full shrink-0"
                             :class="notif.read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-blue-500'"></div>
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
            }
        }
    }
</script>
