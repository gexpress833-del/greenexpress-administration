<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Mon Espace</h1>
        <span class="text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($activeSubscription && $activeSubscription->remaining_days <= 3 && $activeSubscription->status === 'active')
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
            <span class="font-medium">Votre abonnement expire dans {{ $activeSubscription->remaining_days }} jour(s). Pensez à le renouveler.</span>
            <a href="{{ route('client.subscriptions.index') }}" class="text-sm font-semibold underline">Voir</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Abonnement</p>
            <p class="text-2xl font-bold text-green-700 dark:text-green-400">
                {{ $activeSubscription ? ucfirst($activeSubscription->status) : 'Aucun' }}
            </p>
            @if($activeSubscription)
                <p class="text-xs text-slate-400 mt-1">{{ $activeSubscription->remaining_days }} jours restants</p>
            @endif
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Total commandes</p>
            <p class="text-2xl font-bold text-white">$ {{ number_format($totalSpent, 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ number_format($totalSpentFc, 0, ',', '.') }} FC</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Commandes</p>
            <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $totalOrders }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $pendingOrders }} en cours / {{ $deliveredOrders }} livrées</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Livraisons à venir</p>
            <p class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $upcomingDeliveries->count() }}</p>
        </div>
    </div>

    <style>
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(251, 191, 36, 0); }
        }
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes today-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.5), 0 0 20px rgba(251, 191, 36, 0.2); }
            50% { box-shadow: 0 0 0 12px rgba(251, 191, 36, 0), 0 0 30px rgba(251, 191, 36, 0.4); }
        }
        .today-card {
            animation: today-glow 2.5s ease-in-out infinite;
        }
        .subscription-active-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 25%, #f59e0b 50%, #10b981 75%, #059669 100%);
            background-size: 300% 300%;
            animation: gradient-shift 8s ease infinite;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .gold-pulse {
            animation: pulse-gold 2s infinite;
        }
        .live-indicator {
            animation: pulse-gold 1.5s infinite;
        }
        .float-card {
            animation: float 3s ease-in-out infinite;
        }
        .shimmer-text {
            background: linear-gradient(90deg, #fff 0%, #fcd34d 25%, #fff 50%, #fcd34d 75%, #fff 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }
        .gold-border {
            border: 2px solid rgba(251, 191, 36, 0.5);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.2), inset 0 0 20px rgba(251, 191, 36, 0.1);
        }
        .gold-glow {
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.4), 0 0 60px rgba(251, 191, 36, 0.2);
        }
    </style>

    {{-- Détails de l'abonnement actif --}}
    @if($activeSubscription)
        <div class="subscription-active-card rounded-2xl p-6 mb-6 overflow-hidden gold-border gold-glow relative">
            {{-- En-tête avec icône et badge --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mr-4 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Mon abonnement en cours</h2>
                        <p class="text-green-100 text-sm">Plan actif et validé</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-gradient-to-r from-amber-500 to-yellow-400 text-white border-2 border-yellow-300 shadow-lg shadow-amber-500/50 gold-pulse ml-4 shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 mr-2 live-indicator"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-yellow-100" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="shimmer-text">{{ ucfirst($activeSubscription->status) }}</span>
                </span>
            </div>

            {{-- Cartes d'information avec couleurs distinctes et animations --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                {{-- Type d'abonnement --}}
                <div class="float-card bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl p-4 shadow-lg backdrop-blur-sm border border-blue-400/30 hover:scale-105 transition-transform duration-300" style="animation-delay: 0s;">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-yellow-600 flex items-center justify-center mr-3 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <p class="text-xs text-blue-200 uppercase tracking-wide font-medium">Type</p>
                    </div>
                    <p class="text-2xl font-bold text-white shimmer-text">{{ $activeSubscription->type_label }}</p>
                </div>

                {{-- Prix payé --}}
                <div class="float-card bg-gradient-to-br from-amber-500 to-yellow-600 rounded-xl p-4 shadow-lg backdrop-blur-sm border border-yellow-400/50 hover:scale-105 transition-transform duration-300" style="animation-delay: 0.5s;">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-300 to-amber-500 flex items-center justify-center mr-3 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs text-amber-100 uppercase tracking-wide font-medium">Prix payé</p>
                    </div>
                    <p class="text-3xl font-bold text-white">$ {{ number_format($activeSubscription->price, 2) }}</p>
                    <p class="text-sm text-yellow-200 font-medium">{{ number_format($activeSubscription->price_fc, 0, ',', '.') }} FC</p>
                </div>

                {{-- Date de début --}}
                <div class="float-card bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-4 shadow-lg backdrop-blur-sm border border-indigo-400/30 hover:scale-105 transition-transform duration-300" style="animation-delay: 1s;">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center mr-3 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-xs text-indigo-200 uppercase tracking-wide font-medium">Début</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ $activeSubscription->start_date?->format('d/m/Y') ?? 'Non définie' }}</p>
                </div>

                {{-- Date d'expiration --}}
                <div class="float-card bg-gradient-to-br from-rose-600 to-red-700 rounded-xl p-4 shadow-lg backdrop-blur-sm border border-rose-400/30 hover:scale-105 transition-transform duration-300" style="animation-delay: 1.5s;">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-400 to-rose-600 flex items-center justify-center mr-3 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs text-rose-200 uppercase tracking-wide font-medium">Expiration</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ $activeSubscription->end_date?->format('d/m/Y') ?? 'Non définie' }}</p>
                    <p class="text-sm text-yellow-300 font-bold mt-1">{{ $activeSubscription->remaining_days }} jours restants</p>
                </div>
            </div>

            {{-- Footer avec informations supplémentaires --}}
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 flex items-center justify-between">
                <div class="text-sm text-white">
                    <span class="font-medium">Durée totale:</span> <span class="text-green-200 font-semibold">{{ $activeSubscription->total_days }} jours</span>
                    @if($activeSubscription->admin_validated_at)
                        <span class="mx-2 text-green-300">|</span>
                        <span class="font-medium">Validé le:</span> <span class="text-green-200">{{ $activeSubscription->admin_validated_at->format('d/m/Y') }}</span>
                    @endif
                </div>
                <a href="{{ route('client.subscriptions.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition-all backdrop-blur-sm">
                    Voir tous mes abonnements
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- Code de validation du jour --}}
            @if($todayOrder && !in_array($todayOrder->status, ['delivered', 'cancelled']))
                <div class="mt-6 mb-6 bg-gradient-to-r from-amber-500/20 to-yellow-600/10 border border-yellow-400/40 rounded-2xl p-5 backdrop-blur-sm">
                    <div>
                        <h3 class="text-lg font-semibold text-white flex items-center mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Votre repas du jour
                        </h3>
                        <p class="text-sm text-yellow-100">
                            <span class="font-medium">{{ $todayOrder->items->first()?->meal?->name ?? 'Non défini' }}</span>
                            <span class="mx-2">|</span>
                            Code : <span class="font-mono font-bold text-yellow-300 text-lg tracking-wider">{{ $todayOrder->client_validation_code }}</span>
                        </p>
                        <p class="text-xs text-yellow-200/80 mt-1">Remettez ce code au livreur uniquement après réception de votre repas.</p>
                    </div>
                </div>
            @endif

            {{-- Menu hebdomadaire du forfait --}}
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Menu de la semaine
                    @if($currentWeekLabel)
                        <span class="ml-2 text-sm font-normal text-yellow-300">({{ $currentWeekLabel }})</span>
                    @endif
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach($weeklyMenu as $day)
                        <div class="rounded-xl p-4 border transition {{ $day['isToday'] ? 'bg-white/25 border-yellow-400/60 shadow-lg shadow-yellow-400/20 ring-1 ring-yellow-400/40 today-card' : 'bg-white/10 border-white/10' }}">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs text-yellow-300 font-medium uppercase">{{ $day['label'] }}</p>
                                @if($day['isToday'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-400 text-yellow-900 uppercase tracking-wide">Aujourd'hui</span>
                                @endif
                            </div>
                            <p class="text-sm text-white font-semibold">{{ $day['meal'] }}</p>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-white/70">Livraison du lundi au vendredi. Pas de livraison le weekend.</p>
            </div>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl shadow-sm border border-amber-200 dark:border-amber-800 p-6 mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-amber-800 dark:text-amber-200">Vous n'avez pas d'abonnement actif</p>
                    <p class="text-sm text-amber-700 dark:text-amber-300">Contactez un agent pour souscrire à un abonnement</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Total payé pour tous les abonnements --}}
    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-400">Total payé pour les abonnements</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-400">$ {{ number_format($totalSubscriptionSpent, 2) }}</p>
                <p class="text-sm text-slate-400">{{ number_format($totalSubscriptionSpentFc, 0, ',', '.') }} FC</p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>

    @if($upcomingDeliveries->count() > 0)
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl mb-6">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Prochaines livraisons</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Code</th><th class="px-4 py-2 text-left text-slate-400">Date</th><th class="px-4 py-2 text-left text-slate-400">Adresse</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($upcomingDeliveries as $order)
                            <tr>
                                <td class="px-4 py-2 font-medium text-white">{{ $order->code }}</td>
                                <td class="px-4 py-2 text-slate-300">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-slate-300">{{ $order->delivery_address }}</td>
                                <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Commandes récentes</h2>
            <a href="{{ route('client.subscriptions.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">Mes abonnements</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Code</th><th class="px-4 py-2 text-left text-slate-400">Date livraison</th><th class="px-4 py-2 text-left text-slate-400">Total</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="px-4 py-2 font-medium text-white">{{ $order->code }}</td>
                            <td class="px-4 py-2 text-slate-300">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-slate-300">$ {{ number_format($order->total_amount, 2) }}<br><span class="text-xs text-slate-500">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</span></td>
                            <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-2 text-slate-400" colspan="4">Aucune commande</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Historique des abonnements --}}
    @if($subscriptionHistory->count() > 0)
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historique des abonnements
                </h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-800 text-gray-300 border border-gray-700">
                    {{ $subscriptionHistory->count() }} abonnement(s)
                </span>
            </div>

            <div class="space-y-3">
                @foreach($subscriptionHistory as $subscription)
                    <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 hover:border-gray-600 transition-all duration-300 {{ $subscription->id === $activeSubscription?->id ? 'ring-2 ring-green-500/50 bg-gray-800/90' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg {{ $subscription->id === $activeSubscription?->id ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-gray-600 to-gray-700' }} flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">{{ $subscription->type_label }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $subscription->start_date?->format('d/m/Y') ?? '-' }} → {{ $subscription->end_date?->format('d/m/Y') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $subscription->getStatusColorClassAttribute() }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-3 border-t border-gray-700">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Prix payé</p>
                                <p class="text-lg font-bold text-white">$ {{ number_format($subscription->price, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($subscription->price_fc, 0, ',', '.') }} FC</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Durée</p>
                                <p class="text-lg font-bold text-white">{{ $subscription->total_days }} jours</p>
                                @if($subscription->remaining_days > 0)
                                    <p class="text-xs text-green-400">{{ $subscription->remaining_days }} restants</p>
                                @endif
                            </div>
                            <div class="text-right">
                                @if($subscription->id === $activeSubscription?->id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">
                                        <span class="w-2 h-2 rounded-full bg-green-300 mr-1.5 animate-pulse"></span>
                                        Actuellement actif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">Abonnement terminé</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
            </div>
        </div>
    </div>
</x-app-layout>

