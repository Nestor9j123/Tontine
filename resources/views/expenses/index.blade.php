<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-1">
                    {{ __('Charges Mensuelles') }}
                </h2>
                <p class="text-gray-600 text-sm mb-0">
                    {{ __('Gestion des charges et frais mensuels') }}
                </p>
            </div>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" onclick="showAddModal()">
                <i class="fas fa-plus mr-2"></i>
                {{ __('Nouvelle Charge') }}
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- Messages de succès/erreur --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Total ce Mois</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['total_month'] ?? 0) }} FCFA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase mb-1">Électricité</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['electricity'] ?? 0) }} FCFA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bolt text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase mb-1">Loyers</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['rent'] ?? 0) }} FCFA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase mb-1">Dépenses Agents</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['agent_expenses'] ?? 0) }} FCFA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des charges -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ __('Liste des Charges') }}
                </h3>
            </div>
            <div class="p-6">
                
                @if($expenses && $expenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $expense->expense_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($expense->type === 'electricity') bg-yellow-100 text-yellow-800
                                            @elseif($expense->type === 'rent') bg-green-100 text-green-800  
                                            @elseif($expense->type === 'agent_expense') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $expense->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($expense->amount) }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <button onclick="showExpenseDetails({{ $expense->id }})" 
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded transition-colors" 
                                                    title="Voir les détails">
                                                <i class="fas fa-eye mr-1"></i> Voir
                                            </button>
                                            <a href="{{ route('expenses.edit', $expense) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded transition-colors" 
                                               title="Modifier">
                                                <i class="fas fa-edit mr-1"></i> Modifier
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors" 
                                                        title="Supprimer">
                                                    <i class="fas fa-trash mr-1"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($expenses->hasPages())
                        <div class="mt-4">
                            {{ $expenses->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune charge trouvée') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par ajouter une nouvelle charge.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- Modal d'ajout de charge -->
    <div id="addExpenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Nouvelle Charge</h3>
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type de charge</label>
                            <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="toggleAgentField()">
                                <option value="">Sélectionner un type</option>
                                <option value="rent">Loyer</option>
                                <option value="electricity">Électricité</option>
                                <option value="agent_expense">Dépense Agent</option>
                                <option value="general">Autre / Général</option>
                            </select>
                        </div>
                        <div id="agentField" style="display: none;">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Agent concerné</label>
                            <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner un agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Obligatoire pour une dépense de type "Dépense Agent".</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" required></textarea>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Montant (FCFA)</label>
                            <input type="number" name="amount" id="amount" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" name="date" id="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('addExpenseModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('addExpenseModal').classList.add('hidden');
        }

        function toggleAgentField() {
            const typeSelect = document.getElementById('type');
            const agentField = document.getElementById('agentField');
            if (!typeSelect || !agentField) return;

            if (typeSelect.value === 'agent_expense') {
                agentField.style.display = '';
            } else {
                agentField.style.display = 'none';
            }
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('addExpenseModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Fonction pour afficher les détails d'une charge
        function showExpenseDetails(expenseId) {
            console.log('Tentative de chargement des détails pour la charge ID:', expenseId);
            
            // Vérifier si Axios est disponible
            if (typeof window.axios === 'undefined') {
                console.error('Axios n\'est pas chargé');
                alert('Erreur: JavaScript non chargé correctement. Veuillez rafraîchir la page.');
                return;
            }
            
            // Utiliser Axios qui gère mieux l'authentification
            window.axios.get(`/expenses/${expenseId}`)
                .then(response => {
                    console.log('Données reçues:', response.data);
                    const expense = response.data;
                    const modal = document.getElementById('expenseDetailsModal');
                    document.getElementById('detailDate').textContent = new Date(expense.expense_date).toLocaleDateString('fr-FR');
                    document.getElementById('detailType').textContent = expense.type_label || expense.type.replace('_', ' ');
                    document.getElementById('detailDescription').textContent = expense.description;
                    document.getElementById('detailAmount').textContent = new Intl.NumberFormat('fr-FR').format(expense.amount) + ' FCFA';
                    document.getElementById('detailNotes').textContent = expense.notes || 'Aucune note';
                    document.getElementById('detailCreatedBy').textContent = expense.creator ? expense.creator.name : 'N/A';
                    document.getElementById('detailCreatedAt').textContent = new Date(expense.created_at).toLocaleDateString('fr-FR');
                    
                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erreur Axios:', error);
                    if (error.response) {
                        // Le serveur a répondu avec un statut d'erreur
                        if (error.response.status === 404) {
                            alert('Charge non trouvée');
                        } else if (error.response.status === 401) {
                            alert('Vous devez être connecté pour voir les détails');
                        } else if (error.response.status === 403) {
                            alert('Accès interdit');
                        } else {
                            alert(`Erreur: ${error.response.status}`);
                        }
                    } else if (error.request) {
                        // La requête a été faite mais pas de réponse
                        alert('Erreur de connexion au serveur');
                    } else {
                        // Erreur lors de la configuration de la requête
                        alert('Erreur: ' + error.message);
                    }
                });
        }

        function closeDetailsModal() {
            document.getElementById('expenseDetailsModal').classList.add('hidden');
        }
    </script>

    <!-- Modal de détails de charge -->
    <div id="expenseDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Détails de la Charge</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <p id="detailDate" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <p id="detailType" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p id="detailDescription" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Montant</label>
                        <p id="detailAmount" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <p id="detailNotes" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Créé par</label>
                        <p id="detailCreatedBy" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de création</label>
                        <p id="detailCreatedAt" class="text-sm text-gray-900"></p>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeDetailsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>