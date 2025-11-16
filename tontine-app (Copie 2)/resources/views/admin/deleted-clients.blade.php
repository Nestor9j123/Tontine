<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clients Supprimés') }}
            </h2>
            <a href="{{ route('system-settings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Retour aux paramètres
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($deletedClients->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Clients supprimés ({{ $deletedClients->total() }})
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Données liées</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supprimé le</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($deletedClients as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $client->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                                                    <div class="text-xs text-gray-400">Code: {{ $client->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($client->agent)
                                                <div class="text-sm text-gray-900">{{ $client->agent->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $client->agent->email }}</div>
                                            @else
                                                <span class="text-sm text-red-500">Agent supprimé</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div>{{ $client->tontines_count }} tontine(s)</div>
                                                <div>{{ $client->payments_count }} paiement(s)</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $client->deleted_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <form method="POST" action="{{ route('admin.clients.restore', $client->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir restaurer ce client et toutes ses données ?')"
                                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Restaurer
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.clients.force-delete', $client->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('ATTENTION: Cette action est irréversible ! Êtes-vous sûr de vouloir supprimer définitivement ce client ?')"
                                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Supprimer définitivement
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $deletedClients->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500 text-lg mb-4">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aucun client supprimé
                        </div>
                        <p class="text-gray-600">Tous les clients sont actifs dans le système.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
