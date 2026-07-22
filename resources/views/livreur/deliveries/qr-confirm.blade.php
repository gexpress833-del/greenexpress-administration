@extends('layouts.app')

@section('title', 'Confirmation QR - Livraison')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Confirmation de validation QR</h1>

        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Commande</span>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $order->code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Client</span>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $order->client_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Téléphone</span>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $order->client_phone }}</span>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                Vérifiez que cette commande correspond bien à celle que vous livrez.
                La validation attribuera 13 points à votre compte.
            </p>
        </div>

        <form method="POST" action="{{ route('livreur.deliveries.validate-qr') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="code" value="{{ $code }}">

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    Confirmer la validation
                </button>
                <a href="{{ route('livreur.deliveries.index') }}" class="flex-1 text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-2.5 px-4 rounded-lg transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
