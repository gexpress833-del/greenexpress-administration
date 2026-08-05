<x-app-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('notifications.history') }}" class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200" aria-label="Retour">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Détail de la notification</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consultez le contenu complet de votre notification.</p>
            </div>
        </div>

        <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start gap-4 border-b border-gray-100 p-6 dark:border-gray-700">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl text-2xl {{ $typeColor }}">
                    {{ $icon }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeColor }}">{{ $category }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $createdAt->format('d/m/Y à H:i') }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $title }}</h2>
                </div>
            </div>

            <div class="p-6">
                <p class="whitespace-pre-line text-sm leading-7 text-gray-600 dark:text-gray-300">{{ $message }}</p>

                @if($entity)
                    <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900/50">
                        <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Détails associés</h3>

                        @if($entity instanceof \App\Models\Order)
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Code commande</p>
                                    <p class="font-mono font-semibold text-gray-800 dark:text-gray-100">{{ $entity->code }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Statut</p>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $entity->status_color_class }}">{{ $entity->status }}</span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Client</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->client_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Téléphone</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->client_phone }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Adresse de livraison</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->delivery_address }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Date de livraison</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->delivery_date?->format('d/m/Y') }} {{ $entity->delivery_time }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                                    <p class="font-semibold text-green-700 dark:text-green-400">{{ number_format($entity->total_amount_fc, 0, ',', '.') }} FC</p>
                                    <p class="text-xs text-gray-400">$ {{ number_format($entity->total_amount, 2) }}</p>
                                </div>
                                @if($entity->agent)
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Agent</p>
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->agent->name }}</p>
                                    </div>
                                @endif
                                @if($entity->delivery?->livreur)
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Livreur</p>
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $entity->delivery->livreur->name }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($entity->items->isNotEmpty())
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Repas commandés</p>
                                    <ul class="divide-y divide-gray-200 rounded-lg border border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                                        @foreach($entity->items as $item)
                                            <li class="flex items-center justify-between px-3 py-2 text-sm">
                                                <span class="text-gray-800 dark:text-gray-100">{{ $item->meal?->name ?? 'Repas' }} x{{ $item->quantity }}</span>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ number_format($item->total_price_fc, 0, ',', '.') }} FC</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Informations complémentaires disponibles via le bouton ci-dessous.</p>
                        @endif
                    </div>
                @endif

                @if($url)
                    <div class="mt-6">
                        <a href="{{ $url }}" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                            Ouvrir la page concernée
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                @endif

                @if(!empty($whatsappLink))
                    <div class="mt-3">
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
                           class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Ouvrir WhatsApp
                        </a>
                    </div>
                @endif
            </div>
        </article>
    </div>
</x-app-layout>
