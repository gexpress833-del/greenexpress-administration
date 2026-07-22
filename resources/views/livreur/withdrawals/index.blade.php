<x-app-layout>
    @php
        $canWithdraw = $available >= 5;
        $totalWithdrawn = $withdrawals->whereIn('status', ['approved', 'paid'])->sum('amount_usd');
    @endphp
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-600">Espace livreur</p>
            <h1 class="mt-2 text-3xl font-black text-gray-900 dark:text-white">Retrait de mes points</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Vous gagnez 13 points par commande validée. Convertissez-les sur n’importe quel opérateur Mobile Money.</p>
        </div>
        <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
            <section class="rounded-3xl bg-green-900 p-6 text-white shadow-xl">
                <p class="text-sm font-bold uppercase tracking-wider text-green-200">Solde disponible</p>
                <p class="mt-3 text-5xl font-black">$ {{ number_format($available, 2) }}</p>
                <p class="mt-3 text-green-100">{{ number_format($availablePoints) }} points · 1 point = $ 0,025</p>
            </section>
            <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
                @if($canWithdraw)
                    <form method="POST" action="{{ route('livreur.withdrawals.store') }}" class="grid gap-4">
                        @csrf
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-200">Points à convertir</label>
                        <input type="number" name="points" min="200" max="{{ $availablePoints }}" required class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Minimum 200 points">
                        <input type="text" name="mobile_money_operator" required class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Opérateur Mobile Money">
                        <input type="text" name="mobile_money_number" required class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Numéro Mobile Money">
                        <button class="rounded-xl bg-emerald-600 px-4 py-3 font-bold text-white hover:bg-emerald-500">Convertir et demander</button>
                    </form>
                @else
                    <p class="font-bold text-gray-900 dark:text-white">Solde insuffisant</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Le retrait est disponible dès 200 points, soit $ 5.00.</p>
                @endif
            </section>
        </div>
        <section class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700"><h2 class="font-black text-gray-900 dark:text-white">Historique des retraits</h2></div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($withdrawals as $withdrawal)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm">
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($withdrawal->points) }} points · $ {{ number_format($withdrawal->amount_usd, 2) }}</span>
                        <span class="text-gray-500">{{ $withdrawal->mobile_money_operator }} · {{ $withdrawal->mobile_money_number }} · {{ $withdrawal->status }}</span>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-gray-500">Aucun retrait pour le moment.</p>
                @endforelse
            </div>
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $withdrawals->links() }}</div>
        </section>
    </div>
</x-app-layout>
