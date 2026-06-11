<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Types d'abonnement</h1>
        <a href="{{ route('admin.subscription-types.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Ajouter un type
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ordre
                            <span class="ml-1 text-xs text-gray-400" title="Généré automatiquement à la création">&#9432;</span>
                        </th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nom</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Description</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Prix (USD)</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Prix (FC)</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Durée</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($types as $type)
                        <tr>
                            <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $type->display_order }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100 font-medium">{{ $type->name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $type->description ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">$ {{ number_format($type->price, 2) }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ number_format($type->price_fc, 0, ',', '.') }} FC</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $type->duration_days }} jours</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $type->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $type->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('admin.subscription-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Modifier</a>
                                <form method="POST" action="{{ route('admin.subscription-types.destroy', $type) }}" class="inline" onsubmit="return confirm('Supprimer ce type d\'abonnement ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="8">Aucun type d'abonnement</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
