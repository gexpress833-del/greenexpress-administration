<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes abonnements</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Début</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Fin</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Jours restants</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->type_label }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $sub->start_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $sub->end_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sub->remaining_days }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('client.subscriptions.show', $sub) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="6">Aucun abonnement</td>
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
