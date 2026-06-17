<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Demandes de suspension</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Motif</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Durée</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($suspensions as $sus)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sus->subscription->client->name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sus->reason }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $sus->duration_days }} jours</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sus->status_color_class }}">
                                    {{ $sus->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($sus->status === 'pending')
                                    <form method="POST" action="{{ route('admin.suspensions.accept', $sus) }}" class="inline" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        <button type="submit" :disabled="loading" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm mr-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                            <span x-text="loading ? '...' : 'Accepter'">Accepter</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.suspensions.reject', $sus) }}" class="inline" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        <button type="submit" :disabled="loading" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                                            <span x-text="loading ? '...' : 'Rejeter'">Rejeter</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="5">Aucune demande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $suspensions->links() }}
        </div>
    </div>
</x-app-layout>
