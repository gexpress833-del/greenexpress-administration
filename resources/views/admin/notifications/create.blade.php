@extends('layouts.app')

@section('title', 'Notifier les utilisateurs')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Notifier les utilisateurs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Envoyez une notification à tous les utilisateurs ou à un groupe spécifique.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.notifications.send') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre</label>
                <input type="text" name="title" required value="{{ old('title') }}" maxlength="255"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                <textarea name="message" required rows="4" maxlength="1000"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('message') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                    <select name="category" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="information" {{ old('category') === 'information' ? 'selected' : '' }}>Information</option>
                        <option value="alert" {{ old('category') === 'alert' ? 'selected' : '' }}>Alerte</option>
                        <option value="success" {{ old('category') === 'success' ? 'selected' : '' }}>Succès</option>
                        <option value="order" {{ old('category') === 'order' ? 'selected' : '' }}>Commande</option>
                        <option value="delivery" {{ old('category') === 'delivery' ? 'selected' : '' }}>Livraison</option>
                        <option value="subscription" {{ old('category') === 'subscription' ? 'selected' : '' }}>Abonnement</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destinataires</label>
                    <select name="target" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>Tous les utilisateurs</option>
                        <option value="admin" {{ old('target') === 'admin' ? 'selected' : '' }}>Admins</option>
                        <option value="agent" {{ old('target') === 'agent' ? 'selected' : '' }}>Agents</option>
                        <option value="livreur" {{ old('target') === 'livreur' ? 'selected' : '' }}>Livreurs</option>
                        <option value="cuisinier" {{ old('target') === 'cuisinier' ? 'selected' : '' }}>Cuisiniers</option>
                        <option value="client" {{ old('target') === 'client' ? 'selected' : '' }}>Clients</option>
                    </select>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg transition">
                Envoyer la notification
            </button>
        </form>
    </div>
</div>
@endsection
