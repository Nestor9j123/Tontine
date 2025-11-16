@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rapports Mensuels') }}
            </h2>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" onclick="showGenerateModal()">
                <i class="fas fa-plus mr-2"></i> Générer Rapport
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        <!-- Liste des rapports -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Rapports Générés</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'Affaires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charges</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Résultat Net</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Généré par</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $report->report_period ?? \Carbon\Carbon::create($report->report_year, $report->report_month)->locale('fr')->isoFormat('MMMM Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    {{ number_format($report->total_revenue, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                    {{ number_format($report->total_expenses, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $report->net_result >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($report->net_result, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->generator->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->generated_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('monthly-reports.show', $report) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('monthly-reports.pdf', $report) }}" 
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('super_admin'))
                                        <form action="{{ route('monthly-reports.destroy', $report) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 relative group" 
                                                    onclick="confirmDelete(this)"
                                                    title="🔒 Supprimer le rapport (SUPER ADMIN uniquement)">
                                                <i class="fas fa-trash"></i>
                                                <!-- Badge Super Admin -->
                                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                    SA
                                                </span>
                                            </button>
                                        </form>
                                        @else
                                        <!-- Icône grisée pour les non-super admins -->
                                        <span class="text-gray-300 cursor-not-allowed" 
                                              title="🔒 Suppression réservée aux Super Administrateurs">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">Aucun rapport généré pour le moment</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>

                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>

<!-- Modal de génération de rapport -->
<div id="generateReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Générer un Rapport Mensuel</h3>
            <form action="{{ route('monthly-reports.generate') }}" method="POST" onsubmit="return handleFormSubmit(event)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Mois <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('month') border-red-500 @enderror" id="month" name="month" required onchange="checkExistingReport()">
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('fr')->isoFormat('MMMM') }}
                            </option>
                            @endfor
                        </select>
                        @error('month')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Année <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-500 @enderror" id="year" name="year" required onchange="checkExistingReport()">
                            @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('year')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Information :</strong> Le rapport sera généré avec toutes les données disponibles 
                                    pour la période sélectionnée (stock, ventes, paiements, charges, performance des agents).
                                </p>
                                <p class="text-xs text-blue-600 mt-2">
                                    <strong>Note :</strong> Si vous avez supprimé un rapport, actualisez la page pour pouvoir 
                                    régénérer la même période.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Avertissement pour les périodes existantes -->
                    <div id="existingReportWarning" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 hidden">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Attention :</strong> Un rapport existe déjà pour cette période. 
                                    Vous ne pouvez pas créer de doublon.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-chart-line mr-2"></i> Générer le Rapport
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showGenerateModal() {
        document.getElementById('generateReportModal').classList.remove('hidden');
        // Vérifier immédiatement si la période sélectionnée par défaut existe
        setTimeout(checkExistingReport, 100);
    }

    function closeGenerateModal() {
        document.getElementById('generateReportModal').classList.add('hidden');
    }

    function handleFormSubmit(event) {
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Si le bouton est désactivé (rapport existant), empêcher la soumission
        if (submitButton.disabled) {
            return false;
        }
        
        // Désactiver le bouton et afficher un état de chargement
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Génération en cours...';
        
        // Afficher un message de patience
        const infoDiv = document.createElement('div');
        infoDiv.className = 'mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md';
        infoDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Génération du rapport en cours... Cela peut prendre quelques secondes.
                    </p>
                </div>
            </div>
        `;
        form.appendChild(infoDiv);
        
        return true; // Permettre la soumission du formulaire
    }

    // Vérifier en temps réel si un rapport existe
    function checkExistingReport() {
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;
        const warningDiv = document.getElementById('existingReportWarning');
        const submitButton = document.querySelector('button[type="submit"]');
        
        // Liste des rapports existants (mise à jour à chaque chargement de page)
        const existingReports = @json($reports->map(function($report) {
            return ['month' => $report->report_month, 'year' => $report->report_year];
        })->toArray());
        
        // Vérifier si la combinaison mois/année existe
        const reportExists = existingReports.some(report => 
            report.month == month && report.year == year
        );
        
        if (reportExists) {
            warningDiv.classList.remove('hidden');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-ban mr-2"></i> Rapport existant';
        } else {
            warningDiv.classList.add('hidden');
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-chart-line mr-2"></i> Générer le Rapport';
        }
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('generateReportModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeGenerateModal();
        }
    });

    // Fonction de confirmation de suppression
    function confirmDelete(button) {
        console.log('confirmDelete appelé avec bouton:', button);
        
        const confirmed = confirm('⚠️ ATTENTION SUPER ADMIN ⚠️\n\nVous êtes sur le point de supprimer définitivement ce rapport mensuel.\n\nCette action est IRRÉVERSIBLE et supprimera :\n- Toutes les données financières\n- Les statistiques de performance\n- Les données de stock\n- L\'historique des paiements\n\nCette action sera enregistrée dans les logs de sécurité avec votre identité.\n\nÊtes-vous absolument certain de vouloir continuer ?');
        
        if (confirmed) {
            // Trouver le formulaire
            const form = button.closest('form');
            
            console.log('Formulaire trouvé:', form);
            console.log('Action du formulaire:', form ? form.action : 'Aucun formulaire');
            
            if (!form) {
                alert('Erreur: Formulaire non trouvé');
                return false;
            }
            
            // Afficher un état de chargement
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Soumettre le formulaire manuellement
            console.log('Soumission du formulaire...');
            
            // Créer un élément de soumission temporaire pour forcer la soumission
            const submitInput = document.createElement('input');
            submitInput.type = 'submit';
            submitInput.style.display = 'none';
            form.appendChild(submitInput);
            submitInput.click();
            form.removeChild(submitInput);
        }
        
        return false;
    }

    // Afficher les erreurs s'il y en a
    @if($errors->any())
        showGenerateModal();
    @endif
</script>
@endsection