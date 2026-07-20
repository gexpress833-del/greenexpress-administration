<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Retour à la liste</a>
    </div>

    <div class="max-w-4xl mx-auto">
        {{-- Profile header card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-28"></div>
            <div class="px-6 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-16">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        @if ($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-2xl object-cover border-4 border-white dark:border-gray-800 shadow-lg">
                        @else
                            <div class="h-32 w-32 rounded-2xl bg-green-100 dark:bg-green-900 flex items-center justify-center text-4xl font-bold text-green-600 dark:text-green-400 border-4 border-white dark:border-gray-800 shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="pb-2">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $user->role }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:pb-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed info grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email</p>
                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $user->email }}</p>
                @if ($user->email_verified_at)
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">Vérifié le {{ $user->email_verified_at->format('d/m/Y') }}</p>
                @else
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Non vérifié</p>
                @endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Téléphone</p>
                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $user->phone ?: '—' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Adresse</p>
                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $user->address ?: '—' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Inscrit le</p>
                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Mot de passe modifié</p>
                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $user->password_changed_at ? $user->password_changed_at->format('d/m/Y à H:i') : 'Jamais' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">ID</p>
                <p class="text-sm text-gray-800 dark:text-gray-100 font-mono">#{{ $user->id }}</p>
            </div>
        </div>

        {{-- Role-specific stats --}}
        @if (!empty($stats))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Statistiques ({{ ucfirst($user->role) }})</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($stats as $label => $value)
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $value }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ str_replace('_', ' ', ucfirst($label)) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recent activity tables --}}
        @if ($user->role === 'agent' && $user->ordersAsAgent->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Commandes récentes (Agent)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                                <th class="px-5 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($user->ordersAsAgent as $order)
                                <tr>
                                    <td class="px-5 py-2 text-gray-800 dark:text-gray-100">{{ $order->code }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($order->status) }}</span></td>
                                    <td class="px-5 py-2 text-right text-gray-800 dark:text-gray-100">$ {{ number_format((float) $order->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($user->role === 'livreur' && $user->deliveries->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Livraisons récentes</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($user->deliveries as $delivery)
                                <tr>
                                    <td class="px-5 py-2 text-gray-800 dark:text-gray-100">{{ $delivery->order?->code ?? '—' }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $delivery->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($delivery->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($user->role === 'client' && $user->ordersAsClient->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Commandes récentes (Client)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                                <th class="px-5 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($user->ordersAsClient as $order)
                                <tr>
                                    <td class="px-5 py-2 text-gray-800 dark:text-gray-100">{{ $order->code }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($order->status) }}</span></td>
                                    <td class="px-5 py-2 text-right text-gray-800 dark:text-gray-100">$ {{ number_format((float) $order->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if (in_array($user->role, ['agent', 'client']) && $user->subscriptions->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Abonnements récents</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Début</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Fin</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                                <th class="px-5 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Prix</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($user->subscriptions as $subscription)
                                <tr>
                                    <td class="px-5 py-2 text-gray-800 dark:text-gray-100">{{ $subscription->type_label }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $subscription->start_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $subscription->end_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-5 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($subscription->status) }}</span></td>
                                    <td class="px-5 py-2 text-right text-gray-800 dark:text-gray-100">$ {{ number_format((float) $subscription->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($user->role === 'agent' && $user->withdrawals->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Retraits récents</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-5 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                                <th class="px-5 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($user->withdrawals as $withdrawal)
                                <tr>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-300">{{ $withdrawal->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($withdrawal->status) }}</span></td>
                                    <td class="px-5 py-2 text-right text-gray-800 dark:text-gray-100">$ {{ number_format((float) $withdrawal->amount_usd, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
