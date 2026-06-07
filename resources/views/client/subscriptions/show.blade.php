<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Abonnement #{{ $subscription->id }}</h1>
        <x-back-button :href="route('client.subscriptions.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Détails</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Type</span><span class="font-medium">{{ $subscription->type === 'weekly' ? 'Hebdomadaire' : 'Mensuel' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Date début</span><span class="font-medium">{{ $subscription->start_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Date fin</span><span class="font-medium">{{ $subscription->end_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Jours restants</span><span class="font-medium">{{ $subscription->remaining_days }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Statut</span><span class="font-medium">{{ ucfirst($subscription->status) }}</span></div>
            </div>

            @if($subscription->status === 'active' || $subscription->status === 'expired')
                <div class="mt-6 space-y-3">
                    <form method="POST" action="{{ route('client.subscriptions.renew', $subscription) }}">
                        @csrf
                        <select name="type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <option value="weekly">Renouveler Hebdomadaire</option>
                            <option value="monthly">Renouveler Mensuel</option>
                        </select>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Renouveler
                        </button>
                    </form>

                    @if($subscription->status === 'active')
                        <form method="POST" action="{{ route('client.subscriptions.suspend', $subscription) }}">
                            @csrf
                            <input type="text" name="reason" required placeholder="Motif de suspension" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <input type="number" name="duration_days" required min="1" placeholder="Durée (jours)" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Demander suspension
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @if($subscription->status === 'suspended')
                <form method="POST" action="{{ route('client.subscriptions.reactivate', $subscription) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Réactiver
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Suspensions</h2>
            @forelse($subscription->suspensions as $sus)
                <div class="border border-gray-100 rounded-lg p-3 mb-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Motif</span><span>{{ $sus->reason }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Durée</span><span>{{ $sus->duration_days }} jours</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Statut</span><span>{{ ucfirst($sus->status) }}</span></div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Aucune suspension</p>
            @endforelse
        </div>
    </div>
</x-app-layout>

