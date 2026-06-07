<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Historique des notifications</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Toutes vos notifications reçues sur Green Express</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notification->read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-blue-500' }}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap ml-2">
                                        {{ $notification->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                @if(!empty($notification->data['url']))
                                    <div class="mt-2">
                                        <a href="{{ $notification->data['url'] }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            Voir les détails →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-medium">Aucune notification</p>
                <p class="text-xs mt-1">Vous n'avez pas encore reçu de notification.</p>
            </div>
        @endif
    </div>
</x-app-layout>
