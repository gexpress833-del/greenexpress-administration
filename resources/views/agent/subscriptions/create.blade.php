<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvel abonnement client</h1>
        <x-back-button :href="route('agent.subscriptions.index')" />
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.subscriptions.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                <input type="text" name="client_name" required value="{{ old('client_name') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                <input type="text" name="client_phone" required value="{{ old('client_phone') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="client_email" required value="{{ old('client_email') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'abonnement</label>
                    <select name="subscription_type_id" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @forelse($subscriptionTypes as $type)
                            <option value="{{ $type->id }}" data-price="{{ $type->price }}" data-currency="{{ $type->currency ?? 'usd' }}" {{ old('subscription_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->duration_days }} jours) - ${{ number_format($type->price, 2) }}
                            </option>
                        @empty
                            <option disabled>Aucun type disponible</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date début</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-3">
                    Paiement requis avant soumission
                </p>
                <p class="text-xs text-yellow-700 dark:text-yellow-300 mb-3">
                    Confirmez que le client a payé cet abonnement avant de soumettre la demande. L'administrateur vérifiera le paiement avant validation.
                </p>

                @if($paymentUrlTemplate)
                    <button type="button" id="payButton" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition mb-3">
                        Payer maintenant
                    </button>
                @else
                    <button type="button" id="payButton" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition mb-3">
                        Confirmer le paiement
                    </button>
                @endif

                <div class="flex items-start">
                    <input type="checkbox" id="payment_confirmed" name="payment_confirmed" value="1" {{ old('payment_confirmed') ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" required>
                    <label for="payment_confirmed" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        J'ai effectué le paiement de cette commande.
                    </label>
                </div>
                @error('payment_confirmed')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                Enregistrer l'abonnement
            </button>
        </form>
    </div>

    @if(!$paymentUrlTemplate)
    <script>
        (function () {
            const payButton = document.getElementById('payButton');
            const paymentConfirmed = document.getElementById('payment_confirmed');
            payButton.addEventListener('click', function () {
                paymentConfirmed.checked = true;
            });
        })();
    </script>
    @endif

    @if($paymentUrlTemplate)
    <!-- Payment modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Paiement de l'abonnement</h3>
                <button type="button" id="closePaymentModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <iframe id="paymentIframe" class="w-full h-96 border-0" src=""></iframe>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <button type="button" id="openPaymentInApp" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                    Ouvrir dans l'application
                </button>
                <button type="button" id="markPaymentDone" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                    J'ai payé
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const subscriptionTypeSelect = document.querySelector('select[name="subscription_type_id"]');
            const priceInput = document.querySelector('input[name="price"]');
            const currencySelect = document.querySelector('select[name="currency"]');

            function updatePriceFromType() {
                const selected = subscriptionTypeSelect.options[subscriptionTypeSelect.selectedIndex];
                if (!selected || !selected.value) return;
                const price = selected.getAttribute('data-price');
                const currency = selected.getAttribute('data-currency');
                if (price) {
                    priceInput.value = price;
                }
                if (currency) {
                    currencySelect.value = currency;
                }
            }

            subscriptionTypeSelect.addEventListener('change', updatePriceFromType);
            updatePriceFromType();

            const paymentUrlTemplate = @json($paymentUrlTemplate);
            const payButton = document.getElementById('payButton');
            const paymentModal = document.getElementById('paymentModal');
            const paymentIframe = document.getElementById('paymentIframe');
            const closePaymentModal = document.getElementById('closePaymentModal');
            const openPaymentInApp = document.getElementById('openPaymentInApp');
            const markPaymentDone = document.getElementById('markPaymentDone');
            const paymentConfirmed = document.getElementById('payment_confirmed');

            function buildPaymentUrl() {
                const amount = document.querySelector('input[name="price"]').value || '0';
                const currency = document.querySelector('select[name="currency"]').value;
                const clientPhone = document.querySelector('input[name="client_phone"]').value || '';
                const clientEmail = document.querySelector('input[name="client_email"]').value || '';
                const reference = 'SUB-' + Date.now();

                return paymentUrlTemplate
                    .replace(/\{amount\}/g, encodeURIComponent(amount))
                    .replace(/\{currency\}/g, encodeURIComponent(currency))
                    .replace(/\{reference\}/g, encodeURIComponent(reference))
                    .replace(/\{client_phone\}/g, encodeURIComponent(clientPhone))
                    .replace(/\{client_email\}/g, encodeURIComponent(clientEmail));
            }

            payButton.addEventListener('click', function () {
                const url = buildPaymentUrl();
                paymentIframe.src = url;
                paymentModal.classList.remove('hidden');
            });

            openPaymentInApp.addEventListener('click', function () {
                const url = buildPaymentUrl();
                window.open(url, '_blank');
            });

            closePaymentModal.addEventListener('click', function () {
                paymentModal.classList.add('hidden');
                paymentIframe.src = '';
            });

            markPaymentDone.addEventListener('click', function () {
                paymentConfirmed.checked = true;
                paymentModal.classList.add('hidden');
                paymentIframe.src = '';
            });
        })();
    </script>
    @endif
</x-app-layout>
