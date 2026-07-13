<x-app-layout>
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-6">
            <!-- Header -->
            <div class="bg-green-700 px-6 py-5 text-center">
                <img src="/logo.png" alt="Green Express" class="h-16 mx-auto mb-2" onerror="this.style.display='none'; document.getElementById('fallback-title').style.display='block';">
                <h2 id="fallback-title" class="text-3xl font-extrabold text-white tracking-wide hidden">Green Express</h2>
                <p class="text-green-100 text-sm mt-1 uppercase tracking-wider font-medium">Reçu de commande</p>
            </div>

            <!-- Info section -->
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4 text-sm mb-6">
                    <div class="text-gray-500">N° Reçu</div>
                    <div class="font-bold text-gray-800 text-right">{{ $order->code }}</div>

                    <div class="text-gray-500">Date</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->created_at?->format('d/m/Y H:i') }}</div>

                    @if($order->delivery_date)
                    <div class="text-gray-500">Date de livraison</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->delivery_date?->format('d/m/Y') }}</div>
                    @endif

                    <div class="text-gray-500">Client</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->client_name }}</div>

                    <div class="text-gray-500">Téléphone</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->client_phone }}</div>

                    <div class="text-gray-500">Adresse</div>
                    <div class="font-medium text-gray-800 text-right text-xs leading-relaxed">{{ $order->delivery_address }}</div>

                    <div class="text-gray-500">Agent</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->agent->name }}</div>

                    @if($order->subscription_id)
                    <div class="text-gray-500">Abonnement</div>
                    <div class="font-medium text-gray-800 text-right">{{ $order->subscription?->subscriptionType?->name ?? $order->subscription?->type_label ?? 'N/A' }}</div>
                    @endif

                    <div class="text-gray-500">Code validation</div>
                    <div class="font-extrabold text-amber-600 text-right text-lg tracking-[0.2em]">{{ $order->client_validation_code }}</div>
                </div>

                <!-- Divider -->
                <div class="border-t-2 border-dashed border-gray-200 my-4"></div>

                <!-- Items -->
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Détails de la commande</h3>
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <div>
                            <span class="text-gray-800 font-medium">{{ $item->meal->name }}</span>
                            <span class="text-gray-400 text-sm ml-1">x{{ $item->quantity }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-800">$ {{ number_format($item->total_price, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($item->total_price_fc, 0, ',', '.') }} FC</div>
                        </div>
                    </div>
                @endforeach

                <!-- Total -->
                <div class="mt-4 pt-3 border-t-2 border-green-600">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total payé</span>
                        <div class="text-right">
                            <div class="text-xl font-extrabold text-green-700">$ {{ number_format($order->total_amount, 2) }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</div>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="mt-6 flex flex-col items-center">
                    <div class="bg-white p-3 rounded-xl border-2 border-green-100 shadow-sm">
                        {!! $qrCode !!}
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Scannez pour valider la livraison</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
                <p class="text-xs text-gray-500">Merci pour votre confiance</p>
                <p class="text-xs text-green-700 font-semibold mt-0.5">Green Express - Livraison de repas à Kolwezi</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('agent.receipt.pdf', $order) }}" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white text-center font-semibold py-3 px-4 rounded-lg transition shadow-sm">
                Télécharger PDF
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->client_phone) }}?text={{ urlencode("Bonjour " . $order->client_name . " 👋\nVotre commande Green Express a été enregistrée avec succès.\n\nCode de livraison : " . $order->code . "\n\nMerci pour votre confiance.") }}" target="_blank" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition shadow-sm">
                Envoyer via WhatsApp
            </a>
        </div>
    </div>
</x-app-layout>
