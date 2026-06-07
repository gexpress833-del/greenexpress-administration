<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mes abonnements</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Début</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Fin</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Jours restants</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-3">{{ $sub->type === 'weekly' ? 'Hebdomadaire' : 'Mensuel' }}</td>
                            <td class="px-6 py-3">{{ $sub->start_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">{{ $sub->end_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">{{ $sub->remaining_days }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('client.subscriptions.show', $sub) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500" colspan="6">Aucun abonnement</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $subscriptions->links() }}
        </div>
    </div>
</x-app-layout>
