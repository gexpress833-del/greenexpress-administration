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
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-3">
                                @if($sub->client)
                                    {{ $sub->client->name }}
                                @else
                                    {{ $sub->client_name }}
                                    <span class="block text-xs text-gray-400">{{ $sub->client_email }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $sub->type_label }}</td>
                            <td class="px-6 py-3">{{ $sub->start_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">{{ $sub->end_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">$ {{ number_format($sub->price, 2) }}</td>
                            <td class="px-6 py-3">{{ number_format($sub->price_fc, 0, ',', '.') }} FC</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3" x-data="{ editing: false }">
                                @if($sub->status === 'active' && !$sub->hasCredentialsGenerated())
                                    <div x-show="!editing" class="space-y-1">
                                        <form method="POST" action="{{ route('agent.subscriptions.generate-credentials', $sub) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium" onclick="return confirm('Générer les identifiants pour ce client ?')">Générer identifiants</button>
                                        </form>
                                        <button @click="editing = true" type="button" class="block text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">Modifier email/tél.</button>
                                    </div>
                                    <form x-show="editing" method="POST" action="{{ route('agent.subscriptions.update-client-info', $sub) }}" class="space-y-2" @click.outside="editing = false">
                                        @csrf
                                        <input type="email" name="client_email" value="{{ $sub->client_email }}" required placeholder="Email" class="block w-full text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <input type="text" name="client_phone" value="{{ $sub->client_phone }}" required placeholder="Téléphone" class="block w-full text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <div class="flex gap-2">
                                            <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">Enregistrer</button>
                                            <button @click="editing = false" type="button" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Annuler</button>
                                        </div>
                                    </form>
                                @elseif($sub->hasCredentialsGenerated())
                                    <span class="text-xs text-gray-400">Identifiants envoyés</span>
                                @else
                                    <span class="text-xs text-gray-400">En attente de validation</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="8">Aucun abonnement</td>
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
