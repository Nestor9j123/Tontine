{{-- Dashboard Super Admin --}}

{{-- Graphiques d'évolution --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Graphique des paiements quotidiens --}}
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg p-6 border border-blue-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-blue-900">📈 Paiements Quotidiens</h3>
            <span class="text-sm text-blue-600 bg-blue-200 px-3 py-1 rounded-full">30 derniers jours</span>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="paymentsChart"></canvas>
        </div>
    </div>

    {{-- Graphique des montants validés vs en attente --}}
    <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-xl shadow-lg p-6 border border-yellow-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-yellow-900"> Montants par Statut</h3>
            <span class="text-sm text-yellow-600 bg-yellow-200 px-3 py-1 rounded-full">30 derniers jours</span>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="amountsChart"></canvas>
        </div>
    </div>
</div>

{{-- Nouveaux graphiques --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Graphique des tontines --}}
    <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-lg p-6 border border-green-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-green-900"> Répartition des Tontines</h3>
            <span class="text-sm text-green-600 bg-green-200 px-3 py-1 rounded-full">État actuel</span>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="tontinesChart"></canvas>
        </div>
    </div>

    {{-- Performance des agents --}}
    <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl shadow-lg p-6 border border-purple-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-purple-900">👥 Top Agents</h3>
            <span class="text-sm text-purple-600 bg-purple-200 px-3 py-1 rounded-full">Par montant collecté</span>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="agentsChart"></canvas>
        </div>
    </div>
</div>

{{-- Statistiques principales --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Total Clients --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Clients</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['total_clients'] }}</h3>
                <p class="text-blue-100 text-xs mt-1">{{ $stats['active_clients'] }} actifs</p>
            </div>
            <div class="bg-white bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Total Tontines --}}
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Tontines</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['total_tontines'] }}</h3>
                <p class="text-green-100 text-xs mt-1">{{ $stats['active_tontines'] }} actives</p>
            </div>
            <div class="bg-white bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Paiements du jour --}}
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Paiements Aujourd'hui</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['today_payments'] }}</h3>
                <p class="text-yellow-100 text-xs mt-1">{{ $stats['pending_payments'] }} en attente</p>
            </div>
            <div class="bg-white bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Montant Total Collecté --}}
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Montant Collecté</p>
                <h3 class="text-2xl font-bold mt-2">{{ number_format($stats['total_amount_collected'], 0, ',', ' ') }}</h3>
                <p class="text-yellow-100 text-xs mt-1">FCFA</p>
            </div>
            <div class="bg-white bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Statistiques secondaires --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-gray-700 font-semibold mb-4">Équipe</h4>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Agents</span>
                <span class="font-bold text-blue-600">{{ $stats['total_agents'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Secrétaires</span>
                <span class="font-bold text-green-600">{{ $stats['total_secretaries'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Produits</span>
                <span class="font-bold text-yellow-600">{{ $stats['total_products'] }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-gray-700 font-semibold mb-4">Paiements</h4>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Validés</span>
                <span class="font-bold text-green-600">{{ $stats['validated_payments'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">En attente</span>
                <span class="font-bold text-yellow-600">{{ $stats['pending_payments'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total</span>
                <span class="font-bold text-blue-600">{{ $stats['total_payments'] }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-gray-700 font-semibold mb-4">Tontines</h4>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Actives</span>
                <span class="font-bold text-green-600">{{ $stats['active_tontines'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Terminées</span>
                <span class="font-bold text-blue-600">{{ $stats['completed_tontines'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total</span>
                <span class="font-bold text-yellow-600">{{ $stats['total_tontines'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Paiements récents --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Paiements Récents</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->collector->name }}</td>
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
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun paiement récent</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<script>
// Attendre que tout soit chargé
setTimeout(function() {
    console.log('Initialisation des graphiques...');
    
    // Vérifier Chart.js
    if (typeof Chart === 'undefined') {
        console.error('Chart.js non disponible');
        return;
    }
    
    console.log('Chart.js disponible, version:', Chart.version);
    
    // Données réelles PHP vers JavaScript
    const paymentsData = @json($stats['payments_chart_data']);
    const amountsData = @json($stats['amounts_chart_data']);
    const agentsData = @json($stats['agents_performance']);
    
    console.log('Données paiements:', paymentsData);
    console.log('Données montants:', amountsData);
    console.log('Données agents:', agentsData);
    
    // 1. Graphique Paiements (30 derniers jours)
    const ctx1 = document.getElementById('paymentsChart');
    if (ctx1 && paymentsData) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: paymentsData.labels,
                datasets: [{
                    label: 'Paiements par jour',
                    data: paymentsData.data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        console.log('Graphique paiements créé avec', paymentsData.data.length, 'points');
    }
    
    // 2. Graphique Montants (validés vs en attente)
    const ctx2 = document.getElementById('amountsChart');
    if (ctx2 && amountsData) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: amountsData.labels,
                datasets: [{
                    label: 'Montants Validés',
                    data: amountsData.validated,
                    backgroundColor: '#10B981',
                    borderRadius: 4
                }, {
                    label: 'Montants En Attente',
                    data: amountsData.pending,
                    backgroundColor: '#F59E0B',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR', {
                                    notation: 'compact'
                                }).format(value) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
        console.log('Graphique montants créé');
    }
    
    // 3. Graphique Tontines (répartition réelle)
    const ctx3 = document.getElementById('tontinesChart');
    if (ctx3) {
        const activeTontines = {{ $stats['active_tontines'] }};
        const completedTontines = {{ $stats['completed_tontines'] }};
        const totalTontines = {{ $stats['total_tontines'] }};
        const pendingTontines = totalTontines - activeTontines - completedTontines;
        
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: ['Actives', 'Terminées', 'En attente'],
                datasets: [{
                    data: [activeTontines, completedTontines, pendingTontines],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        console.log('Graphique tontines créé:', activeTontines, completedTontines, pendingTontines);
    }
    
    // 4. Graphique Agents (performance réelle)
    const ctx4 = document.getElementById('agentsChart');
    if (ctx4 && agentsData && agentsData.length > 0) {
        const agentNames = agentsData.map(agent => agent.name);
        const agentAmounts = agentsData.map(agent => agent.total_amount);
        
        new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: agentNames,
                datasets: [{
                    label: 'Montant Collecté',
                    data: agentAmounts,
                    backgroundColor: [
                        '#8B5CF6', '#A855F7', '#C084FC', '#D8B4FE', '#E9D5FF',
                        '#F3E8FF', '#8B5CF6', '#A855F7', '#C084FC', '#D8B4FE'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Collecté: ' + 
                                       new Intl.NumberFormat('fr-FR').format(context.parsed.x) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR', {
                                    notation: 'compact'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
        console.log('Graphique agents créé avec', agentsData.length, 'agents');
    }
    
}, 1000); // Attendre 1 seconde
</script>
