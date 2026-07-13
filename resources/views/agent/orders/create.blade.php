<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvelle commande</h1>
    </div>

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.orders.store') }}" id="orderForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                    <input type="text" name="client_name" required value="{{ old('client_name') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone client</label>
                    <input type="text" name="client_phone" required value="{{ old('client_phone') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse de livraison</label>
                <textarea name="delivery_address" required rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('delivery_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de livraison</label>
                    <input type="date" name="delivery_date" required value="{{ old('delivery_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" id="currency-select" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Repas</label>
                <div class="space-y-2" id="meals-container">
                    <template id="meal-row-template">
                        <div class="meal-row flex flex-col sm:flex-row gap-2 items-start sm:items-end bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" data-index="INDEX">
                            <select name="items[INDEX][meal_id]" required class="flex-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 meal-select">
                                <option value="">Choisir un repas</option>
                                @foreach($meals as $meal)
                                    <option value="{{ $meal->id }}" data-price="{{ $meal->price }}" data-price-fc="{{ $meal->price_fc }}" data-label-usd="{{ $meal->name }} - ${{ number_format($meal->price, 2) }}" data-label-fc="{{ $meal->name }} - {{ number_format($meal->price_fc, 0, ',', '.') }} FC">{{ $meal->name }} - ${{ number_format($meal->price, 2) }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[INDEX][quantity]" min="1" value="1" required class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 qty-input">
                            <button type="button" onclick="this.closest('.meal-row').remove(); updateTotal();" class="text-red-600 hover:text-red-800 text-sm font-medium">Supprimer</button>
                        </div>
                    </template>
                </div>
                <button type="button" onclick="addMealRow()" class="mt-2 text-sm text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 font-medium">+ Ajouter un repas</button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 gap-4">
                <div>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Total : <span id="total-display">$ 0.00</span></p>
                </div>
                <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg transition">
                    Enregistrer
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
