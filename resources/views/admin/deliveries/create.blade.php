<x-app-layout>
    <div class="mb-6 flex items-center gap-3">
        <x-back-button :href="route('admin.deliveries.index')" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Assigner une livraison</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.deliveries.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Commande</label>
                <select name="order_id" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Choisir une commande</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}">{{ $order->code }} - {{ $order->client_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Livreur</label>
                <select name="livreur_id" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Choisir un livreur</option>
                    @foreach($livreurs as $liv)
                        <option value="{{ $liv->id }}">{{ $liv->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Assigner</button>
        </form>
    </div>
</x-app-layout>
