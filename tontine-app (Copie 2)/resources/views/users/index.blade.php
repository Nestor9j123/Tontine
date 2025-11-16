<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Utilisateurs</h2>
            <a href="{{ route('users.create') }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                + Nouvel Utilisateur
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm text-gray-500 mb-2">Agents</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $users->where('roles.0.name', 'agent')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm text-gray-500 mb-2">Secrétaires</h3>
                <p class="text-3xl font-bold text-green-600">{{ $users->where('roles.0.name', 'secretary')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm text-gray-500 mb-2">Total</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $users->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->hasRole('super_admin'))
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Super Admin</span>
                            @elseif($user->hasRole('secretary'))
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Secrétaire</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Agent</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Actif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-3">
                                <a href="{{ route("users.edit", $user) }}" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($user->hasRole("agent"))
                                    <a href="{{ route("reports.agents.details", $user) }}" class="text-blue-600 hover:text-blue-900" title="Performances">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </a>
                                @endif
                                @if(!$user->hasRole("super_admin"))
                                <form method="POST" action="{{ route("users.destroy", $user) }}" id="delete-user-{{ $user->id }}">
                                    @csrf
                                    @method("DELETE")
                                    <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-900" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDeleteUser(userId, userName) {
            showConfirm(
                'Supprimer cet utilisateur',
                `Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ? Cette action est irréversible et supprimera toutes ses données.`,
                () => {
                    showInfo('Suppression en cours...', 'Suppression de l\'utilisateur...');
                    document.getElementById(`delete-user-${userId}`).submit();
                },
                'danger',
                'Supprimer définitivement',
                'Annuler'
            );
        }
    </script>
</x-app-layout>
