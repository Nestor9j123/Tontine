<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Paiements Partiels & Manquants
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Statistiques -->
        <div class="max-w-7xl mx-auto px-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Paiements Partiels</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_partial_payments'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Montant Manquant</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_missing_amount']) }} FCFA</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">En Attente</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_missing_payments'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Complétés</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_missing_payments'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="max-w-7xl mx-auto px-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="GET" action="{{ route('partial-payments.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="payment_type" id="payment_type" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                <option value="">Tous les types</option>
                                <option value="partial" {{ ($filters['payment_type'] ?? '') === 'partial' ? 'selected' : '' }}>Partiels seulement</option>
                                <option value="missing" {{ ($filters['payment_type'] ?? '') === 'missing' ? 'selected' : '' }}>Avec montant manquant</option>
                                <option value="completed" {{ ($filters['payment_type'] ?? '') === 'completed' ? 'selected' : '' }}>Complétés</option>
                            </select>
                        </div>

                        <div>
                            <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-2">Agent</label>
                            <select name="agent_id" id="agent_id" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                <option value="">Tous les agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ ($filters['agent_id'] ?? '') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                            <select name="client_id" id="client_id" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                <option value="">Tous les clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ ($filters['client_id'] ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                            <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}" 
                                   class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                            <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}" 
                                   class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('partial-payments.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des paiements -->
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6">
                    @if($payments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manquant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $payment->reference }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->client->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($payment->collector && $payment->collector->uuid && auth()->user()->hasAnyRole(['secretary', 'super_admin']))
                                                <a href="/agents/{{ $payment->collector->uuid }}/profile" 
                                                   class="text-blue-600 hover:text-blue-900 hover:underline">
                                                    {{ $payment->collector->name }}
                                                </a>
                                            @else
                                                {{ $payment->collector->name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->formatted_amount }}
                                            @if($payment->expected_amount)
                                                <br><span class="text-xs text-gray-500">Attendu: {{ $payment->formatted_expected_amount }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                            {{ $payment->formatted_remaining_missing_amount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {!! $payment->payment_status_badge !!}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('partial-payments.show', $payment) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                            @if($payment->remaining_missing_amount > 0)
                                                <a href="{{ route('partial-payments.add-missing-form', $payment) }}" 
                                                   class="text-green-600 hover:text-green-900">Ajouter</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun paiement partiel</h3>
                            <p class="mt-1 text-sm text-gray-500">Tous les paiements sont complets.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
