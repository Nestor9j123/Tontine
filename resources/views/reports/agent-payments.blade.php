<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Paiements de l'agent: {{ $agent->name }}</h2>
                <p class="text-sm text-gray-600">Suivi des paiements et calcul des commissions</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('reports.agents') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">← Retour aux agents</a>
                <a href="{{ route('reports.agents.details', $agent) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Profil de l'agent</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Résumé des commissions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé des commissions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-xl p-6">
                    <p class="text-sm text-blue-600 mb-1">Total collecté</p>
                    <p class="text-2xl font-bold text-blue-800">{{ number_format($totalCollected, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-green-50 rounded-xl p-6">
                    <p class="text-sm text-green-600 mb-1">Commission totale (10%)</p>
                    <p class="text-2xl font-bold text-green-800">{{ number_format($totalCommission, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-6">
                    <p class="text-sm text-yellow-600 mb-1">Nombre de mois</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ count($monthlyStats) }}</p>
                </div>
            </div>
        </div>

        {{-- Détails par mois --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Détails par mois</h3>
            </div>
            
            <div class="accordion" id="monthlyAccordion">
                @forelse($monthlyStats as $month => $stats)
                    <div class="border-b border-gray-200">
                        <div class="p-4 flex justify-between items-center cursor-pointer" data-toggle="collapse" data-target="#month-{{ $month }}">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $stats['month_name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $stats['payments_count'] }} paiements</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">{{ number_format($stats['total_amount'], 0, ',', ' ') }} FCFA</p>
                                <p class="text-sm text-green-600">Commission: {{ number_format($stats['commission'], 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                        
                        <div id="month-{{ $month }}" class="collapse">
                            <div class="p-4 bg-gray-50">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tontine</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($stats['payments'] as $payment)
                                            <tr class="hover:bg-gray-100">
                                                <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $payment->client->name }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $payment->tontine->product->name }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium {{ $payment->status === 'validated' ? 'text-green-600' : 'text-gray-600' }}">
                                                    {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm">
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Aucun paiement trouvé pour cet agent.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple accordion functionality
            const accordionHeaders = document.querySelectorAll('[data-toggle="collapse"]');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-target'));
                    if (target.classList.contains('show')) {
                        target.classList.remove('show');
                    } else {
                        // Close all other panels
                        document.querySelectorAll('.collapse.show').forEach(panel => {
                            if (panel !== target) panel.classList.remove('show');
                        });
                        target.classList.add('show');
                    }
                });
            });
        });
    </script>
    @endpush

    <style>
        .collapse {
            display: none;
        }
        .collapse.show {
            display: block;
        }
    </style>
</x-app-layout>
