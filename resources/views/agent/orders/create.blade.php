<x-app-layout>
    <div class="mb-6 flex items-center gap-3">
        <x-back-button :href="route('agent.orders.index')" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvelle commande</h1>
    </div>

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.orders.store') }}" id="orderForm" x-data="mealOrderForm()" x-init="init()" @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                    <input type="text" name="client_name" required value="{{ old('client_name') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone client</label>
                    <input type="text" name="client_phone" required value="{{ old('client_phone') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse de livraison</label>
                <textarea name="delivery_address" required rows="2" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">{{ old('delivery_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de livraison</label>
                    <input type="date" name="delivery_date" required value="{{ old('delivery_date', now()->format('Y-m-d')) }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" x-model="currency" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Repas</label>
                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-end bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                            <select :name="'items[' + index + '][meal_id]'" x-model="item.meal_id" required class="flex-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                                <option value="">Choisir un repas</option>
                                @foreach($meals as $meal)
                                    <option value="{{ $meal->id }}">{{ $meal->name }} - ${{ number_format($meal->price, 2) }} / {{ number_format($meal->price_fc, 0, ',', '.') }} FC</option>
                                @endforeach
                            </select>
                            <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" min="1" required :readonly="loading" class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                            <button type="button" @click="removeItem(index)" :disabled="loading" class="text-red-600 hover:text-red-800 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed">Supprimer</button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addItem()" :disabled="loading" class="mt-2 text-sm text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 font-medium disabled:opacity-60 disabled:cursor-not-allowed">+ Ajouter un repas</button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 gap-4">
                <div>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Total : <span x-text="totalText"></span></p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('agent.orders.index') }}" :disabled="loading" class="w-full sm:w-auto text-center bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 disabled:opacity-60 disabled:cursor-not-allowed text-gray-800 dark:text-gray-200 font-semibold py-2.5 px-6 rounded-lg transition">
                        Annuler
                    </a>
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
            </div>
        </form>
    </div>

    <script>
        function mealOrderForm() {
            return {
                loading: false,
                currency: '{{ old('currency', 'usd') }}',
                items: [],
                meals: @json($meals->map(fn($m) => ['id' => $m->id, 'price' => (float) $m->price, 'price_fc' => (float) $m->price_fc])),

                init() {
                    const oldItems = @json(old('items', []));
                    if (oldItems.length > 0) {
                        this.items = oldItems.map(item => ({ meal_id: item.meal_id ? item.meal_id.toString() : '', quantity: parseInt(item.quantity) || 1 }));
                    } else {
                        this.addItem();
                    }
                },

                addItem() {
                    this.items.push({ meal_id: '', quantity: 1 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    if (this.items.length === 0) {
                        this.addItem();
                    }
                },

                get totalText() {
                    const total = this.items.reduce((sum, item) => {
                        if (!item.meal_id) return sum;
                        const meal = this.meals.find(m => m.id == item.meal_id);
                        if (!meal) return sum;
                        const price = this.currency === 'fc' ? meal.price_fc : meal.price;
                        return sum + (price * item.quantity);
                    }, 0);

                    if (this.currency === 'fc') {
                        return total.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' FC';
                    }
                    return '$ ' + total.toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
