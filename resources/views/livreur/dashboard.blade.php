<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Dashboard Livreur</h1>
        <span class="text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Actions principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-700 bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="text-base sm:text-lg font-semibold mb-1">Scanner un QR Code</h2>
                    <p class="text-green-100 text-xs sm:text-sm mb-4">Scannez le QR du reçu pour valider instantanément la livraison.</p>
                    <button onclick="openQrScanner()" class="inline-flex items-center gap-2 bg-white text-green-700 font-semibold px-4 py-2 rounded-lg hover:bg-green-50 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="hidden sm:inline">Ouvrir le scanner</span>
                        <span class="sm:hidden">Scanner</span>
                    </button>
                </div>
                <div class="bg-white/10 rounded-lg p-3 shrink-0 hidden sm:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-amber-600 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="w-full">
                    <h2 class="text-base sm:text-lg font-semibold mb-1">Valider par code client</h2>
                    <p class="text-amber-100 text-xs sm:text-sm mb-4">Saisissez le code de validation à 6 caractères fourni par le client.</p>
                    <form action="{{ route('livreur.deliveries.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="search" maxlength="6" placeholder="Ex: A3B9K7"
                               class="flex-1 border-0 rounded-lg px-3 py-2 text-gray-900 font-mono uppercase tracking-wider placeholder-gray-400 focus:ring-2 focus:ring-white"
                               required>
                        <button type="submit" class="w-full sm:w-auto bg-white text-amber-600 font-semibold px-4 py-2 rounded-lg hover:bg-amber-50 transition shadow-sm">
                            <span class="hidden sm:inline">Rechercher</span>
                            <span class="sm:hidden">OK</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Livraisons auj.</p>
            <p class="text-xl sm:text-2xl font-bold text-green-700">{{ $todayDeliveries }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Livrées auj.</p>
            <p class="text-xl sm:text-2xl font-bold text-white">{{ $deliveredToday }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">En attente</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-700">{{ $pendingDeliveries }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Réussite</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-700">{{ $performanceRate }}%</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Note moy.</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600">{{ number_format($averageRating, 1) }}</p>
        </div>
    </div>

    {{-- Livraisons du jour (priorité absolue) --}}
    @php
        $activeDeliveries = $recentDeliveries->filter(fn($d) => !in_array($d->status, ['delivered', 'cancelled']));
        $hasTodayDeliveries = $availableTodayDeliveries->count() > 0 || $myTodayDeliveries->count() > 0;
    @endphp
    @if($hasTodayDeliveries)
        <div class="overflow-hidden rounded-[2rem] border border-yellow-400/30 bg-gradient-to-br from-yellow-500/20 to-amber-600/10 shadow-2xl shadow-yellow-900/20 backdrop-blur-2xl mb-6">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-yellow-400/20 flex items-center justify-between bg-yellow-500/10">
                <h2 class="text-base sm:text-lg font-semibold text-white flex items-center gap-2">
                    <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                    Livraisons du jour
                    <span class="ml-2 text-xs font-normal text-yellow-200">{{ now()->format('d/m/Y') }}</span>
                </h2>
                <a href="{{ route('livreur.deliveries.index') }}" class="text-xs sm:text-sm text-yellow-400 hover:text-yellow-300 font-medium">Tout voir</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45">
                        <tr>
                            <th class="px-4 py-2 text-left text-slate-400">Client</th>
                            <th class="px-4 py-2 text-left text-slate-400">Adresse</th>
                            <th class="px-4 py-2 text-left text-slate-400">Menu</th>
                            <th class="px-4 py-2 text-left text-slate-400">Statut</th>
                            <th class="px-4 py-2 text-left text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($availableTodayDeliveries as $delivery)
                            @php $meal = $delivery->order->items->first()?->meal?->name ?? 'Non défini'; @endphp
                            <tr class="hover:bg-yellow-500/10">
                                <td class="px-4 py-3 font-medium text-white">{{ $delivery->order->client_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $delivery->order->delivery_address ?? '-' }}</td>
                                <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">{{ $meal }}</span></td>
                                <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100/10 text-yellow-300">Disponible</span></td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('livreur.deliveries.assign', $delivery) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-yellow-900 font-semibold px-3 py-1.5 rounded-lg text-xs transition">Prendre en charge</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($myTodayDeliveries as $delivery)
                            @php $meal = $delivery->order->items->first()?->meal?->name ?? 'Non défini'; @endphp
                            <tr class="hover:bg-yellow-500/10 bg-green-500/5">
                                <td class="px-4 py-3 font-medium text-white">{{ $delivery->order->client_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $delivery->order->delivery_address ?? '-' }}</td>
                                <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">{{ $meal }}</span></td>
                                <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">{{ $delivery->status }}</span></td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('livreur.deliveries.show', $delivery) }}" class="bg-green-600 hover:bg-green-500 text-white font-semibold px-3 py-1.5 rounded-lg text-xs transition inline-block">Livrer</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Livraisons en cours (prioritaires) --}}
    @if($activeDeliveries->count() > 0)
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl mb-6">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-white/10 flex items-center justify-between bg-orange-50">
                <h2 class="text-base sm:text-lg font-semibold text-white flex items-center gap-2">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    Livraisons en cours
                </h2>
                <a href="{{ route('livreur.deliveries.index') }}" class="text-xs sm:text-sm text-green-600 hover:underline font-medium">Tout voir</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45">
                        <tr>
                            <th class="px-4 py-2 text-left text-slate-400">Code</th>
                            <th class="px-4 py-2 text-left text-slate-400">Client</th>
                            <th class="px-4 py-2 text-left text-slate-400">Adresse</th>
                            <th class="px-4 py-2 text-left text-slate-400">Statut</th>
                            <th class="px-4 py-2 text-left text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($activeDeliveries as $delivery)
                            <tr class="hover:bg-slate-950/45">
                                <td class="px-4 py-3 font-medium">{{ $delivery->delivery_code }}</td>
                                <td class="px-4 py-3">{{ $delivery->order->client_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $delivery->order->delivery_address ?? '-' }}</td>
                                <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">{{ $delivery->status }}</span></td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('livreur.deliveries.show', $delivery) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Valider</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Graphique hebdomadaire --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 border border-white/10 mb-6">
        <h2 class="text-base sm:text-lg font-semibold text-white mb-3 sm:mb-4">Livraisons effectuées (7 derniers jours)</h2>
        @php $maxWeekly = max(!empty($weeklyDeliveries) ? max($weeklyDeliveries) : 0, 1); @endphp
        <div class="flex items-end gap-3 h-40">
            @foreach($weeklyDeliveries as $day => $count)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full bg-purple-100 rounded-t" style="height: {{ $maxWeekly > 0 ? ($count / $maxWeekly) * 100 : 0 }}%;">
                        <div class="w-full h-full bg-purple-500 rounded-t opacity-80"></div>
                    </div>
                    <span class="text-xs text-slate-400">{{ $day }}</span>
                    <span class="text-[10px] text-gray-400">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Livraisons récentes --}}
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-semibold text-white">Livraisons récentes</h2>
            <a href="{{ route('livreur.deliveries.index') }}" class="text-xs sm:text-sm text-green-600 hover:underline">Tout voir</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Code</th><th class="px-4 py-2 text-left text-slate-400">Client</th><th class="px-4 py-2 text-left text-slate-400">Adresse</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($recentDeliveries as $delivery)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $delivery->delivery_code }}</td>
                            <td class="px-4 py-2">{{ $delivery->order->client_name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $delivery->order->delivery_address ?? '-' }}</td>
                            <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">{{ $delivery->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-2 text-slate-400" colspan="4">Aucune livraison</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($recentReviews->count() > 0)
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl mt-6">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Avis récents</h2>
            </div>
            <div class="divide-y divide-white/10">
                @foreach($recentReviews as $review)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-white">{{ $review->client->name ?? 'Client' }}</span>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-slate-300 italic">"{{ $review->comment }}"</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Commande {{ $review->order->code ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Modal Scanner QR --}}
    <div id="qrModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                <h3 class="text-lg font-semibold text-white">Scanner un QR Code</h3>
                <button type="button" onclick="closeQrScanner(event)" class="text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div id="qr-reader" class="rounded-lg overflow-hidden bg-gray-900"></div>
                <p class="text-sm text-slate-400 text-center mt-3">Placez le QR code du reçu dans le cadre</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let isClosing = false;

        function openQrScanner() {
            document.getElementById('qrModal').classList.remove('hidden');
            isClosing = false;
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    if (isClosing) return;
                    isClosing = true;
                    html5QrCode.stop().then(() => {
                        closeQrScanner();
                        if (decodedText.startsWith('http')) {
                            window.location.href = decodedText;
                        } else {
                            alert('QR code invalide : ' + decodedText);
                        }
                    }).catch(() => {
                        closeQrScanner();
                    });
                },
                () => {}
            ).catch(err => {
                console.error(err);
                alert('Impossible d\'accéder à la caméra. Vérifiez les permissions.');
            });
        }

        function closeQrScanner(e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            if (isClosing) return;
            isClosing = true;

            const modal = document.getElementById('qrModal');
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                    modal.classList.add('hidden');
                    isClosing = false;
                }).catch(() => {
                    html5QrCode = null;
                    modal.classList.add('hidden');
                    isClosing = false;
                });
            } else {
                modal.classList.add('hidden');
                isClosing = false;
            }
        }

        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) closeQrScanner();
        });
    </script>
            </div>
        </div>
    </div>
</x-app-layout>

