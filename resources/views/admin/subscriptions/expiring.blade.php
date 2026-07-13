<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Abonnements à cours / expirés</h1>
        <form method="GET" action="{{ route('admin.subscriptions.expiring') }}" class="flex gap-2">
            <select name="days" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm">
                <option value="3" {{ $days == 3 ? 'selected' : '' }}">Expirent dans 3 jours</option>
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Expirent dans 7 jours</option>
                <option value="14" {{ $days == 14 ? 'selected' : '' }}>Expirent dans 14 jours</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Filtrer</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agent</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Fin</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Jours restants</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->client?->name ?? $sub->client_name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->agent?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->type_label }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->end_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->daysRemaining() }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.subscriptions.show', $sub) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="7">Aucun abonnement à cours ou expiré</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $subscriptions->links() }}
        </div>
    </div>
</x-app-layout>
