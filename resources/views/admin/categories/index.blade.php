<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Catégories</h1>
        <a href="{{ route('admin.categories.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Ajouter
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nom</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Slug</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($categories as $cat)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $cat->name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $cat->slug }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $cat->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="4">Aucune catégorie</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
