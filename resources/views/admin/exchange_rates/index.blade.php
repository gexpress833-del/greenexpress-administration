<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Taux de change</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Nouveau taux</h2>
            <p class="text-sm text-gray-500 mb-4">Le taux actuel est <strong class="text-green-700">1 USD = {{ number_format($currentRate, 0, ',', '.') }} FC</strong></p>

            <form action="{{ route('admin.exchange-rates.store') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label for="rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">1 USD =</label>
                    <input type="number" step="0.01" min="1" name="rate" id="rate" value="{{ old('rate', $currentRate) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="flex items-center pb-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">FC</span>
                </div>
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Enregistrer
                </button>
            </form>
            @error('rate')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 px-6 pt-5 pb-2">Historique des taux</h2>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3 font-medium">Date</th>
                        <th class="px-6 py-3 font-medium">Taux</th>
                        <th class="px-6 py-3 font-medium">Par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rates as $rate)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $rate->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 font-semibold text-green-700 dark:text-green-400">1 USD = {{ number_format($rate->rate, 0, ',', '.') }} FC</td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $rate->currency_from }} &rarr; {{ $rate->currency_to }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">Aucun historique</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $rates->links() }}</div>
        </div>
    </div>
</x-app-layout>
