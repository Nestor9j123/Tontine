<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mon Reçu Mensuel
                </h2>
                <p class="text-sm text-gray-600">{{ $receiptData['period']['period_name'] }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('agent-receipts.my-pdf', ['month' => $receiptData['period']['month'], 'year' => $receiptData['period']['year']]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-download mr-1"></i> Télécharger PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- Sélection de période --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route('agent-receipts.my-receipt') }}" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                    <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $receiptData['period']['month'] == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('fr')->isoFormat('MMMM') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $receiptData['period']['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Actualiser
                    </button>
                </div>
            </form>
        </div>

        {{-- Résumé de performance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ma Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $receiptData['summary']['validated_payments'] }}</p>
                    <p class="text-sm text-blue-700">Paiements Collectés</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($receiptData['summary']['total_amount_collected'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-green-700">Montant Total (FCFA)</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ $receiptData['summary']['new_clients'] }}</p>
                    <p class="text-sm text-purple-700">Nouveaux Clients</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ number_format($receiptData['summary']['total_expenses'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-red-700">Mes Dépenses (FCFA)</p>
                </div>
            </div>

            {{-- Section Bénéfices/Commissions --}}
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Mes Bénéfices & Commissions
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <p class="text-xl font-bold text-yellow-600">{{ number_format($receiptData['summary']['commission_rate'] * 100, 1) }}%</p>
                        <p class="text-sm text-gray-600">Taux de Commission</p>
                    </div>
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                        <p class="text-xl font-bold text-orange-600">{{ number_format($receiptData['summary']['gross_commission'], 0, ',', ' ') }}</p>
                        <p class="text-sm text-gray-600">Commission Brute (FCFA)</p>
                    </div>
                    <div class="text-center p-4 bg-white rounded-lg shadow-sm border-2 {{ $receiptData['summary']['net_commission'] >= 0 ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <p class="text-xl font-bold {{ $receiptData['summary']['net_commission'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($receiptData['summary']['net_commission'], 0, ',', ' ') }}
                        </p>
                        <p class="text-sm text-gray-600">Bénéfice Net (FCFA)</p>
                        <p class="text-xs text-gray-500 mt-1">(Commission - Dépenses)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mes paiements collectés --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mes Paiements Collectés</h3>
            </div>
            <div class="p-6">
                @if($receiptData['payments']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($receiptData['payments'] as $date => $dailyPayments)
                                    @foreach($dailyPayments as $payment)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $payment->client->name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $payment->tontine->product->name }}</td>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="px-4 py-2">
                                                @if($payment->status === 'validated')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Validé</span>
                                                @elseif($payment->status === 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">En attente</span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejeté</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucun paiement collecté pour cette période.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mes dépenses --}}
        @if($receiptData['expenses']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mes Dépenses</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($receiptData['expenses'] as $expense)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm font-medium text-red-600">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $expense->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <p class="text-lg font-semibold text-red-600">
                        Total de mes dépenses: {{ number_format($receiptData['expenses']->sum('amount'), 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Mes statistiques --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes Statistiques</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['active_tontines'] }}</p>
                    <p class="text-sm text-gray-600">Mes Tontines Actives</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['completed_tontines'] }}</p>
                    <p class="text-sm text-gray-600">Tontines Complétées</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['pending_payments'] }}</p>
                    <p class="text-sm text-gray-600">Paiements en Attente</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
