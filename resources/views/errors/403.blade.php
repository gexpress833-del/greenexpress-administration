<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-4xl font-bold">
                403
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Accès refusé</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                {{ $exception->getMessage() ?: 'Vous n\'avez pas la permission d\'accéder à cette ressource.' }}
            </p>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                Retour à l'accueil
            </a>
        </div>
    </div>
</x-guest-layout>
