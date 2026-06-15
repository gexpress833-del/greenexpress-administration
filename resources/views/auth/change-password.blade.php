<x-app-layout>
    <div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Changer votre mot de passe</h2>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-6">Vous devez changer votre mot de passe temporaire avant de continuer.</p>

        <form method="POST" action="{{ route('password.change.store') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau mot de passe</label>
                <div x-data="{ show: false }" class="relative mt-1">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 pr-10 text-sm">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.059 10.059 0 013.999-5.325m3.999-2.325A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.057 10.057 0 01-3.358 5.038M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">Confirmer le mot de passe</label>
                <div x-data="{ show: false }" class="relative mt-1">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 pr-10 text-sm">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.059 10.059 0 013.999-5.325m3.999-2.325A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.057 10.057 0 01-3.358 5.038M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg transition text-sm">
                Enregistrer
            </button>
        </form>
    </div>
</x-app-layout>
