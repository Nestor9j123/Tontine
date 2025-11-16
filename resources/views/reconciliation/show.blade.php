<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🔍 Analyse Anti-Fraude: {{ $agent->name }}
            </h2>
            <a href="{{ route('reconciliation.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Retour
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <form method="GET" action="{{ route('reconciliation.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_payments'] }}</p>
                <p class="text-sm text-gray-600">Total paiements</p>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow-sm p-6 border border-yellow-200">
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_payments'] }}</p>
                <p class="text-sm text-gray-600">En attente</p>
            </div>
            <div class="bg-red-50 rounded-xl shadow-sm p-6 border border-red-200">
                <p class="text-3xl font-bold text-red-600">{{ $stats['rejected_payments'] }}</p>
                <p class="text-sm text-gray-600">Rejetés ({{ $stats['rejection_rate'] }}%)</p>
            </div>
            <div class="bg-green-50 rounded-xl shadow-sm p-6 border border-green-200">
                <p class="text-3xl font-bold text-green-600">{{ $stats['validated_payments'] }}</p>
                <p class="text-sm text-gray-600">Validés</p>
            </div>
        </div>

        {{-- Paiements suspects --}}
        @if($suspiciousPayments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-bold text-red-900">
                    🚨 Paiements Suspects ({{ $suspiciousPayments->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raison</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gravité</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suspiciousPayments as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payments.show', $item['payment']) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $item['payment']->reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item['payment']->client->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ number_format($item['payment']->amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item['payment']->payment_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm">{{ $item['reason'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item['severity'] == 'high')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Élevée</span>
                                @elseif($item['severity'] == 'medium')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">Moyenne</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Faible</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
            <p class="text-green-800 font-semibold">✅ Aucun paiement suspect détecté</p>
        </div>
        @endif

        {{-- Historique des modifications --}}
        @if($modifications->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">📝 Historique des Modifications</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($modifications as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->action == 'update')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">Modifié</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $payment = \App\Models\Payment::find($log->model_id);
                                @endphp
                                @if($payment)
                                    {{ $payment->reference }}
                                @else
                                    <span class="text-gray-400">Supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->user->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
