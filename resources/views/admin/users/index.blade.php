<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Utilisateurs</h1>
        <div class="flex gap-2">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone..."
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
                @endif
            </form>
            <a href="{{ route('admin.users.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                + Ajouter
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Avatar</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nom</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Email</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Téléphone</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Rôle</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-3">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center text-sm font-bold text-green-600 dark:text-green-400 border border-gray-200 dark:border-gray-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $user->email }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $user->phone }}</td>
                            <td class="px-6 py-3 uppercase text-gray-800 dark:text-gray-100">{{ $user->role }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="6">Aucun utilisateur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
