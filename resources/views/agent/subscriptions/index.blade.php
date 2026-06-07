<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes abonnements</h1>
        <a href="{{ route('agent.subscriptions.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Nouvel abonnement
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Début</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Fin</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Prix USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Prix FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-3">{{ $sub->client->name }}</td>
                            <td class="px-6 py-3">{{ $sub->type === 'weekly' ? 'Hebdo' : 'Mensuel' }}</td>
                            <td class="px-6 py-3">{{ $sub->start_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">{{ $sub->end_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">$ {{ number_format($sub->price, 2) }}</td>
                            <td class="px-6 py-3">{{ number_format($sub->price_fc, 0, ',', '.') }} FC</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="7">Aucun abonnement</td>
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
