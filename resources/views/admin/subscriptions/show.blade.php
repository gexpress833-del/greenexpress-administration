<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Abonnement #{{ $subscription->id }}</h1>
        <x-back-button :href="route('admin.subscriptions.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Détails</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->client->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Agent</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->agent->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Type</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->type === 'weekly' ? 'Hebdomadaire' : 'Mensuel' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date début</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->start_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date fin</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->end_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Jours restants</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->remaining_days }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Prix</span><span class="font-medium text-gray-800 dark:text-gray-100">$ {{ number_format($subscription->price, 2) }}<br><span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($subscription->price_fc, 0, ',', '.') }} FC</span></span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ ucfirst($subscription->status) }}</span></div>
            </div>

            @if($subscription->status === 'pending')
                <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Valider l'abonnement
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Suspensions</h2>
            @forelse($subscription->suspensions as $sus)
                <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3 mb-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Motif</span><span class="text-gray-800 dark:text-gray-100">{{ $sus->reason }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Durée</span><span class="text-gray-800 dark:text-gray-100">{{ $sus->duration_days }} jours</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="text-gray-800 dark:text-gray-100">{{ ucfirst($sus->status) }}</span></div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune suspension</p>
            @endforelse
        </div>
    </div>
</x-app-layout>

