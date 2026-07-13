<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Logs d'activité</h1>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
        @if(request('search'))
            <a href="{{ route('admin.activity_logs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-100 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50"><tr><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Date</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Utilisateur</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Action</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Modèle</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Description</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">IP</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $log->user?->name ?? 'Système' }}</td>
                            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $log->action }}</span></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $log->model_type ? class_basename($log->model_type) . ($log->model_id ? ' #' . $log->model_id : '') : '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-md truncate">{{ $log->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-3 text-gray-500 dark:text-gray-400" colspan="6">Aucun log</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
