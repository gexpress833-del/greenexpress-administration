<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-4xl font-bold">
                500
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Erreur interne</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                Une erreur inattendue s'est produite. Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.
            </p>
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                Retour à l'accueil
            </a>
        </div>
    </div>
</x-guest-layout>
