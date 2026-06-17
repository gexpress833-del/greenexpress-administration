<x-app-layout>
    <div class="mb-6 flex items-center gap-3">
        <x-back-button :href="route('agent.orders.index')" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvelle commande</h1>
    </div>

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.orders.store') }}" id="orderForm" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                    <input type="text" name="client_name" required value="{{ old('client_name') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone client</label>
                    <input type="text" name="client_phone" required value="{{ old('client_phone') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse de livraison</label>
                <textarea name="delivery_address" required rows="2" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">{{ old('delivery_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de livraison</label>
                    <input type="date" name="delivery_date" required value="{{ old('delivery_date', now()->format('Y-m-d')) }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" id="currency-select" required :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Repas</label>
                <div class="space-y-2" id="meals-container">
                    <template id="meal-row-template">
                        <div class="meal-row flex flex-col sm:flex-row gap-2 items-start sm:items-end bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" data-index="INDEX">
                            <select name="items[INDEX][meal_id]" required :disabled="loading" class="flex-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 meal-select disabled:opacity-60 disabled:cursor-not-allowed">
                                <option value="">Choisir un repas</option>
                                @foreach($meals as $meal)
                                    <option value="{{ $meal->id }}" data-price="{{ $meal->price }}" data-price-fc="{{ $meal->price_fc }}" data-label-usd="{{ $meal->name }} - ${{ number_format($meal->price, 2) }}" data-label-fc="{{ $meal->name }} - {{ number_format($meal->price_fc, 0, ',', '.') }} FC">{{ $meal->name }} - ${{ number_format($meal->price, 2) }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[INDEX][quantity]" min="1" value="1" required :disabled="loading" class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 qty-input disabled:opacity-60 disabled:cursor-not-allowed">
                            <button type="button" onclick="this.closest('.meal-row').remove(); updateTotal();" :disabled="loading" class="text-red-600 hover:text-red-800 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed">Supprimer</button>
                        </div>
                    </template>
                </div>
                <button type="button" onclick="addMealRow()" :disabled="loading" class="mt-2 text-sm text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 font-medium disabled:opacity-60 disabled:cursor-not-allowed">+ Ajouter un repas</button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 gap-4">
                <div>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Total : <span id="total-display">$ 0.00</span></p>
                </div>
                <button type="submit" :disabled="loading" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2.5 px-6 rounded-lg transition flex items-center gap-2 disabled:cursor-not-allowed">
                    <template x-if="loading">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Enregistrement...' : 'Enregistrer'">Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        let mealIndex = 0;
        function addMealRow() {
            const container = document.getElementById('meals-container');
            const template = document.getElementById('meal-row-template').innerHTML;
            const html = template.replace(/INDEX/g, mealIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            container.appendChild(wrapper.firstElementChild);
            mealIndex++;
            updateMealLabels();
            updateTotal();
        }

        function updateMealLabels() {
            const currency = document.getElementById('currency-select').value;
            document.querySelectorAll('.meal-select').forEach(select => {
                Array.from(select.options).forEach(option => {
                    if (!option.value) return;
                    option.textContent = currency === 'fc' ? option.dataset.labelFc : option.dataset.labelUsd;
                });
            });
        }

        function updateTotal() {
            let total = 0;
            const currency = document.getElementById('currency-select').value;
            document.querySelectorAll('.meal-row').forEach(row => {
                const select = row.querySelector('.meal-select');
                const qty = row.querySelector('.qty-input');
                if (select && qty && select.value) {
                    const price = parseFloat(currency === 'fc'
                        ? (select.options[select.selectedIndex].dataset.priceFc || 0)
                        : (select.options[select.selectedIndex].dataset.price || 0));
                    const quantity = parseInt(qty.value || 0);
                    total += price * quantity;
                }
            });
            const display = document.getElementById('total-display');
            if (currency === 'fc') {
                display.textContent = total.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' FC';
            } else {
                display.textContent = '$ ' + total.toFixed(2);
            }
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('meal-select') || e.target.classList.contains('qty-input')) {
                updateTotal();
            }
            if (e.target.id === 'currency-select') {
                updateMealLabels();
                updateTotal();
            }
        });

        // Ajouter une ligne par défaut
        addMealRow();
    </script>
</x-app-layout>
