<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvel utilisateur</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse</label>
                <textarea name="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('address') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rôle</label>
                <select name="role" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="admin">Admin</option>
                    <option value="agent">Agent</option>
                    <option value="livreur">Livreur</option>
                    <option value="cuisinier">Cuisinier</option>
                    <option value="client">Client</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe</label>
                <input type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Créer</button>
        </form>
    </div>
</x-app-layout>
