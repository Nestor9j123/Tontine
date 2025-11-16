@extends('layouts.app')

@section('title', 'Rapport Mensuel - ' . $monthlyReport->report_period)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête avec actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Rapport Mensuel - {{ $monthlyReport->report_period }}</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('monthly-reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la liste
                    </a>
                    <a href="{{ route('monthly-reports.pdf', $monthlyReport) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé Financier -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">
                    {{ number_format($monthlyReport->total_revenue, 0, ',', ' ') }}
                </div>
                <div class="text-sm text-gray-500">Chiffre d'Affaires</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-red-600 mb-2">
                    {{ number_format($monthlyReport->total_expenses, 0, ',', ' ') }}
                </div>
                <div class="text-sm text-gray-500">Total Charges</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-3xl font-bold {{ $monthlyReport->net_result >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                    {{ number_format($monthlyReport->net_result, 0, ',', ' ') }}
                </div>
                <div class="text-sm text-gray-500">Résultat Net</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ $monthlyReport->payment_stats['validated_payments'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">Paiements Validés</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Stock -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Évolution du Stock</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Initial</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Final</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Variation</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($monthlyReport->initial_stock as $productId => $initialData)
                            @php
                                $finalData = $monthlyReport->final_stock[$productId] ?? ['quantity' => 0];
                                $variation = $finalData['quantity'] - $initialData['quantity'];
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $initialData['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $initialData['quantity'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $finalData['quantity'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $variation >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $variation > 0 ? '+' : '' }}{{ $variation }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Produits vendus -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Produits Vendus</h3>
            </div>
            <div class="p-6">
                @if(count($monthlyReport->products_sold) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'Affaires</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($monthlyReport->products_sold as $productId => $salesData)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $salesData['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $salesData['quantity'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($salesData['revenue'], 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-info-circle text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">Aucun produit vendu ce mois</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques des paiements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Statistiques des Paiements</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $monthlyReport->payment_stats['total_payments'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $monthlyReport->payment_stats['validated_payments'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Validés</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center mt-4">
                        <div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $monthlyReport->payment_stats['pending_payments'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">En Attente</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $monthlyReport->payment_stats['rejected_payments'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Rejetés</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance des agents -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Performance des Agents</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Paiements</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Collecté</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dépenses</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($monthlyReport->agent_performance as $agentId => $performance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $performance['payments_count'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($performance['payments_amount'], 0, ',', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($performance['expenses'], 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes du rapport -->
    @if($monthlyReport->notes)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
        </div>
        <div class="p-6">
            {!! nl2br(e($monthlyReport->notes)) !!}
        </div>
    </div>
    @endif

    <!-- Informations de génération -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="text-sm text-gray-500">
                <strong>Rapport généré par :</strong> {{ $monthlyReport->generator->name }} 
                le {{ $monthlyReport->generated_at->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>
</div>
@endsection