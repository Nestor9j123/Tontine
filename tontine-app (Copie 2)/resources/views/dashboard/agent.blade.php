{{-- Dashboard Agent --}}

{{-- Widget de performance et classement --}}
@include('dashboard.agent-performance-widget', ['stats' => $stats])

{{-- Statistiques principales --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Mes Clients --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Mes Clients</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_clients'] }}</h3>
                <p class="text-green-600 text-xs mt-1 font-medium">{{ $stats['active_clients'] }} actifs</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Mes Tontines --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Mes Tontines</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_tontines'] }}</h3>
                <p class="text-green-600 text-xs mt-1 font-medium">{{ $stats['active_tontines'] }} actives</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Paiements du jour --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Paiements Aujourd'hui</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['today_payments'] }}</h3>
                <p class="text-yellow-600 text-xs mt-1 font-medium">{{ $stats['pending_payments'] }} en attente</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Montant Collecté --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Montant Collecté</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_collected'], 0, ',', ' ') }}</h3>
                <p class="text-gray-600 text-xs mt-1">FCFA</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Actions rapides --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <a href="{{ route('clients.create') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white hover:from-blue-600 hover:to-blue-700 transition transform hover:scale-105">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-semibold mb-1">Nouveau Client</h4>
                <p class="text-blue-100 text-sm">Enregistrer un client</p>
            </div>
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
        </div>
    </a>

    <a href="{{ route('tontines.create') }}" class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white hover:from-green-600 hover:to-green-700 transition transform hover:scale-105">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-semibold mb-1">Nouvelle Tontine</h4>
                <p class="text-green-100 text-sm">Créer une tontine</p>
            </div>
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </div>
    </a>

    <a href="{{ route('payments.create') }}" class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white hover:from-yellow-600 hover:to-yellow-700 transition transform hover:scale-105">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-semibold mb-1">Nouveau Paiement</h4>
                <p class="text-yellow-100 text-sm">Enregistrer un paiement</p>
            </div>
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </a>
</div>

{{-- Mes clients récents et Tontines actives --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Clients récents --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Clients Récents</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($stats['recent_clients'] as $client)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold">
                            {{ substr($client->first_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $client->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $client->phone }}</p>
                        </div>
                    </div>
                    <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Aucun client récent</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tontines actives --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Tontines Actives</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($stats['active_tontines_list'] as $tontine)
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-900">{{ $tontine->client->full_name }}</p>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Actif</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $tontine->product->name }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $tontine->progress_percentage }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span>{{ $tontine->completed_payments }}/{{ $tontine->total_payments }} paiements</span>
                        <span>{{ $tontine->progress_percentage }}%</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Aucune tontine active</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Paiements récents --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Mes Paiements Récents</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stats['recent_payments'] as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->reference }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->client->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->tontine->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($payment->status === 'validated')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Validé</span>
                        @elseif($payment->status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejeté</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun paiement récent</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
