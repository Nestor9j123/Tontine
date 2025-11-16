<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Client') }}
            </h2>
            <div class="flex space-x-2">
                @can('edit_clients')
                <a href="{{ route('clients.edit', $client) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                @endcan
                <a href="{{ route('clients.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Informations du client --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-3xl mr-6">
                        {{ substr($client->first_name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $client->full_name }}</h3>
                        <p class="text-gray-600 mt-1">Code: <span class="font-mono font-semibold">{{ $client->code }}</span></p>
                        <div class="mt-2">
                            @if($client->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Actif</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">Inactif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Téléphone Principal</h4>
                        <p class="text-lg text-gray-900">{{ $client->phone }}</p>
                    </div>
                    @if($client->phone_secondary)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Téléphone Secondaire</h4>
                        <p class="text-lg text-gray-900">{{ $client->phone_secondary }}</p>
                    </div>
                    @endif
                    @if($client->email)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                        <p class="text-lg text-gray-900">{{ $client->email }}</p>
                    </div>
                    @endif
                    @if($client->city)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Ville</h4>
                        <p class="text-lg text-gray-900">{{ $client->city }}</p>
                    </div>
                    @endif
                    @if($client->id_card_number)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Numéro CNI</h4>
                        <p class="text-lg text-gray-900">{{ $client->id_card_number }}</p>
                    </div>
                    @endif
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Agent Responsable</h4>
                        <p class="text-lg text-gray-900">{{ $client->agent->name }}</p>
                    </div>
                    @if($client->address)
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Adresse</h4>
                        <p class="text-lg text-gray-900">{{ $client->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Tontines</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $client->tontines->count() }}</h3>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Paiements</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $client->payments->count() }}</h3>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Montant Total</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($client->payments->sum('amount'), 0, ',', ' ') }}</h3>
                        <p class="text-xs text-gray-500">FCFA</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tontines du client --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Tontines</h3>
                @can('create_tontines')
                <a href="{{ route('tontines.create', ['client_uuid' => $client->uuid]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                    + Nouvelle Tontine
                </a>
                @endcan
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($client->tontines as $tontine)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $tontine->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tontine->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ number_format($tontine->total_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $tontine->progress_percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $tontine->progress_percentage }}%</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tontine->status === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Actif</span>
                                @elseif($tontine->status === 'completed')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Terminé</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">{{ ucfirst($tontine->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('tontines.show', $tontine) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune tontine</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
