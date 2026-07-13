<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Réclamations</h1>
    </div>

    <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
               class="w-full sm:flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <div class="flex gap-2">
            <select name="status" class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Tous les statuts</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Résolu</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeté</option>
            </select>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-xs sm:text-sm font-medium py-2 px-3 sm:px-4 rounded-lg transition shrink-0">Filtrer</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.complaints.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-xs sm:text-sm font-medium py-2 px-3 sm:px-4 rounded-lg transition shrink-0 text-center">Réinit.</a>
            @endif
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50"><tr><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Commande</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 hidden sm:table-cell">Client</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Statut</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 hidden md:table-cell">Date</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Action</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($complaints as $complaint)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $complaint->order->code ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100 hidden sm:table-cell">{{ $complaint->client->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $complaint->type_label }}</td>
                            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color_class }}">{{ $complaint->status }}</span></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell">{{ $complaint->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><a href="{{ route('admin.complaints.show', $complaint) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs sm:text-sm font-medium">Voir</a></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center" colspan="6">Aucune réclamation</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 dark:border-gray-700">{{ $complaints->links() }}</div>
    </div>
</x-app-layout>
