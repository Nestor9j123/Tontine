<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails de l'Agent: {{ $agent->name }}</h2>
                <p class="text-sm text-gray-600">Inscrit depuis le {{ $agent->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('reports.agents') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">← Retour aux agents</a>
                <a href="{{ route('reports.agents.payments', $agent) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">Voir paiements et commissions</a>
                <a href="{{ route('payments.index', ['collector_uuid' => $agent->uuid]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Paiements de l'agent</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Carte de profil --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center">
                <div class="md:w-1/4 flex justify-center mb-4 md:mb-0">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white text-3xl font-bold">
                        {{ substr($agent->name, 0, 1) }}
                    </div>
                </div>
                <div class="md:w-3/4 flex flex-col space-y-2">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xl font-bold text-gray-800">{{ $agent->name }}</h3>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Agent</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $agent->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="font-medium">{{ $agent->phone ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date d'inscription</p>
                            <p class="font-medium">{{ $agent->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dernière connexion</p>
                            <p class="font-medium">{{ $agent->last_login_at ? $agent->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Clients</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['clients_count'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Tontines</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['tontines_count'] }}</p>
                        <div class="flex space-x-2 mt-1">
                            <span class="text-xs text-green-600">{{ $stats['tontines_active'] }} actives</span>
                            <span class="text-xs text-gray-600">{{ $stats['tontines_completed'] }} terminées</span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Paiements</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['payments_count'] }}</p>
                        <div class="flex space-x-2 mt-1">
                            <span class="text-xs text-orange-600">{{ $stats['payments_pending'] }} en attente</span>
                        </div>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Montant collecté</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['payments_sum'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Clients récents --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">Clients récents</h3>
                        <a href="{{ route('clients.index', ['agent_uuid' => $agent->uuid]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir tous</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentClients as $client)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                        {{ substr($client->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $client->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $client->phone ?? 'Pas de téléphone' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">
                            Aucun client trouvé pour cet agent.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tontines récentes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">Tontines récentes</h3>
                        <a href="{{ route('tontines.index', ['agent_uuid' => $agent->uuid]) }}" class="text-yellow-600 hover:text-yellow-800 text-sm">Voir toutes</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentTontines as $tontine)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $tontine->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $tontine->client->name }}</p>
                                    <div class="flex space-x-2 mt-1">
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $tontine->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $tontine->status === 'active' ? 'Active' : 'Terminée' }}
                                        </span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ number_format($tontine->product->price, 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('tontines.show', $tontine) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">
                            Aucune tontine trouvée pour cet agent.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Paiements récents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Paiements récents</h3>
                    <a href="{{ route('reports.agents.payments', $agent) }}" class="text-green-600 hover:text-green-800 text-sm">Voir tous et commissions</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tontine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentPayments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $payment->client->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->tontine->product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->status === 'validated')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Validé</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejeté</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun paiement trouvé pour cet agent.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
