<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Tontines') }}
            </h2>
            @can('create_tontines')
            <a href="{{ route('tontines.create') }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvelle Tontine
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <form method="GET" action="{{ route('tontines.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminées</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendues</option>
                    </select>
                </div>
                <div>
                    <select name="product_uuid" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les produits</option>
                        @foreach(\App\Models\Product::all() as $product)
                            <option value="{{ $product->uuid }}" {{ request('product_uuid') == $product->uuid ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Filtrer
                    </button>
                    <a href="{{ route('tontines.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $tontines->total() }}</h3>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Actives</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1">{{ \App\Models\Tontine::active()->count() }}</h3>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Terminées</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ \App\Models\Tontine::completed()->count() }}</h3>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Montant Total</p>
                        <h3 class="text-xl font-bold text-yellow-600 mt-1">{{ number_format(\App\Models\Tontine::sum('total_amount'), 0, ',', ' ') }}</h3>
                        <p class="text-xs text-gray-500">FCFA</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des tontines --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tontines as $tontine)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $tontine->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($tontine->client)
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-xs mr-2">
                                            {{ substr($tontine->client->first_name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $tontine->client->full_name }}</div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs mr-2">
                                            ?
                                        </div>
                                        <div class="text-sm font-medium text-red-600">Client supprimé</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tontine->product ? $tontine->product->name : 'Produit supprimé' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($tontine->total_amount, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-gray-500">Payé: {{ number_format($tontine->paid_amount, 0, ',', ' ') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $tontine->progress_percentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $tontine->completed_payments }}/{{ $tontine->total_payments }} ({{ $tontine->progress_percentage }}%)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tontine->status === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Actif</span>
                                @elseif($tontine->status === 'completed')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Terminé</span>
                                @elseif($tontine->status === 'suspended')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">Suspendu</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Annulé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('tontines.show', $tontine) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @can('edit_tontines')
                                    <a href="{{ route('tontines.edit', $tontine) }}" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Aucune tontine trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-info :paginator="$tontines" />
        </div>
    </div>
</x-app-layout>
