<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">💰 Gestion des Paiements</h2>
                <p class="text-sm text-gray-600 mt-1">Gérez les paiements de tontines et carnets physiques</p>
            </div>
            @can('create_payments')
            <a href="{{ route('payments.create') }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition flex items-center shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouveau Paiement
            </a>
            @endcan
        </div>
    </x-slot>

    {{-- Statistiques compactes --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $tontinePayments = \App\Models\Payment::with(['client', 'tontine']);
            $notebookPayments = \App\Models\NotebookPayment::with(['client', 'user']);
            
            $stats = [
                'tontine_pending' => $tontinePayments->where('status', 'pending')->count(),
                'tontine_validated' => $tontinePayments->where('status', 'validated')->count(),
                'notebook_total' => $notebookPayments->count(),
                'total_amount' => $tontinePayments->where('status', 'validated')->sum('amount') + $notebookPayments->sum('amount')
            ];
        @endphp
        
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">En attente</p>
                    <p class="text-2xl font-bold">{{ $stats['tontine_pending'] }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Validés</p>
                    <p class="text-2xl font-bold">{{ $stats['tontine_validated'] }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-400 to-yellow-500 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Carnets</p>
                    <p class="text-2xl font-bold">{{ $stats['notebook_total'] }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Total</p>
                    <p class="text-lg font-bold">{{ number_format($stats['total_amount'], 0, ',', ' ') }}</p>
                    <p class="text-xs text-yellow-200">FCFA</p>
                </div>
                <div class="bg-white/20 rounded-lg p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('tontines')" id="tab-tontines" class="tab-button active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                    💎 Paiements Tontines
                    <span class="ml-2 bg-blue-100 text-blue-600 py-1 px-2 rounded-full text-xs">{{ $payments->total() }}</span>
                </button>
                <button onclick="showTab('notebooks')" id="tab-notebooks" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    📓 Carnets Physiques
                    <span class="ml-2 bg-gray-100 text-gray-600 py-1 px-2 rounded-full text-xs">{{ $stats['notebook_total'] }}</span>
                </button>
            </nav>
        </div>

        {{-- Contenu des onglets --}}
        <div class="p-6">
            {{-- Onglet Paiements Tontines --}}
            <div id="content-tontines" class="tab-content">
                {{-- Filtres compacts --}}
                <div class="mb-4">
                    <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap gap-3">
                        <input type="text" name="search" placeholder="🔍 Rechercher..." value="{{ request('search') }}" 
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Tous statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validés</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetés</option>
                        </select>
                        <input type="date" name="date" value="{{ request('date') }}" 
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                            Filtrer
                        </button>
                        <a href="{{ route('payments.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                            Reset
                        </a>
                    </form>
                </div>

                {{-- Liste compacte des paiements --}}
                <div class="space-y-3">
                    @forelse($payments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition {{ $payment->status === 'pending' ? 'border-l-4 border-l-yellow-500' : ($payment->status === 'validated' ? 'border-l-4 border-l-blue-500' : 'border-l-4 border-l-gray-400') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($payment->client)
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                    {{ substr($payment->client->first_name, 0, 1) }}{{ substr($payment->client->last_name, 0, 1) }}
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                    ?
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                @if($payment->client)
                                                    <a href="{{ route('clients.show', $payment->client) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 truncate hover:underline">
                                                        {{ $payment->client->full_name }}
                                                    </a>
                                                @else
                                                    <span class="text-sm font-medium text-red-600 truncate">
                                                        Client supprimé
                                                    </span>
                                                @endif
                                                
                                                @if($payment->tontine)
                                                    <a href="{{ route('tontines.show', $payment->tontine) }}" class="text-xs text-gray-500 hover:text-gray-700 hover:underline">
                                                        {{ $payment->tontine->code }}
                                                    </a>
                                                @else
                                                    <span class="text-xs text-red-500">
                                                        Tontine supprimée
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <p class="text-lg font-bold text-blue-600">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</p>
                                                <span class="text-xs text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-800' : ($payment->payment_method === 'mobile_money' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $payment->payment_method === 'cash' ? '💵 Espèces' : ($payment->payment_method === 'mobile_money' ? '📱 Mobile' : '🏦 Virement') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $payment->status === 'validated' ? 'bg-blue-100 text-blue-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $payment->status === 'validated' ? '✓ Validé' : ($payment->status === 'pending' ? '⏳ En attente' : '✗ Rejeté') }}
                                    </span>
                                    <div class="flex space-x-1">
                                        <a href="{{ route('payments.show', $payment) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Voir détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        @if($payment->status === 'pending' && $payment->amount <= 100000 && (auth()->user()->hasRole('agent') || auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin')))
                                        <form method="POST" action="{{ route('payments.validate', $payment) }}" class="inline" onsubmit="return confirm('{{ auth()->user()->hasRole('agent') ? 'Validation automatique' : 'Valider' }} ce paiement de {{ number_format($payment->amount, 0, ',', ' ') }} FCFA ?')">
                                            @csrf
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="{{ auth()->user()->hasRole('agent') ? 'Validation automatique agent (≤100k)' : 'Validation rapide (≤100k)' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-500">Aucun paiement de tontine trouvé</p>
                        </div>
                    @endforelse
                </div>

                @if($payments->hasPages())
                    <div class="mt-6">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>

            {{-- Onglet Carnets Physiques --}}
            <div id="content-notebooks" class="tab-content hidden">
                @php
                    $notebookPayments = \App\Models\NotebookPayment::with(['client', 'user'])->latest()->paginate(15);
                @endphp
                
                <div class="space-y-3">
                    @forelse($notebookPayments as $notebook)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition border-l-4 border-l-yellow-400">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-yellow-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                📓
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                @if($notebook->client)
                                                    <a href="{{ route('clients.show', $notebook->client) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 truncate hover:underline">
                                                        {{ $notebook->client->full_name }}
                                                    </a>
                                                    <a href="{{ route('notebooks.show', $notebook->client) }}" class="text-xs text-gray-500 hover:text-gray-700 hover:underline">
                                                        {{ $notebook->client->code }}
                                                    </a>
                                                @else
                                                    <span class="text-sm font-medium text-red-600 truncate">
                                                        Client supprimé
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <p class="text-lg font-bold text-yellow-600">{{ number_format($notebook->amount, 0, ',', ' ') }} FCFA</p>
                                                <span class="text-xs text-gray-500">{{ $notebook->payment_date->format('d/m/Y') }}</span>
                                                <span class="text-xs text-gray-500">Par: {{ $notebook->user->name }}</span>
                                            </div>
                                            @if($notebook->notes)
                                                <p class="text-xs text-gray-600 mt-1">{{ $notebook->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-yellow-100 to-blue-100 text-blue-800">
                                        📓 Carnet Physique
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="text-gray-500">Aucun paiement de carnet physique trouvé</p>
                        </div>
                    @endforelse
                </div>

                @if($notebookPayments->hasPages())
                    <div class="mt-6">
                        {{ $notebookPayments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Styles CSS personnalisés --}}
    <style>
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .payment-card {
            transition: all 0.2s ease-in-out;
        }
        
        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .quick-validate-btn {
            position: relative;
            overflow: hidden;
        }
        
        .quick-validate-btn:hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 0.6s ease-in-out;
        }
        
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>

    {{-- JavaScript pour les onglets --}}
    <script>
        function showTab(tabName) {
            // Cacher tous les contenus avec animation
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.opacity = '0';
                setTimeout(() => {
                    content.classList.add('hidden');
                }, 150);
            });
            
            // Désactiver tous les boutons d'onglets
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Afficher le contenu sélectionné avec animation
            setTimeout(() => {
                const targetContent = document.getElementById('content-' + tabName);
                targetContent.classList.remove('hidden');
                targetContent.style.opacity = '1';
            }, 150);
            
            // Activer le bouton d'onglet sélectionné
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
        }

        // Animation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter la classe payment-card aux éléments
            document.querySelectorAll('[class*="border-l-4"]').forEach(card => {
                card.classList.add('payment-card');
            });
            
            // Ajouter la classe quick-validate-btn aux boutons de validation
            document.querySelectorAll('button[title*="Validation rapide"]').forEach(btn => {
                btn.classList.add('quick-validate-btn');
            });
        });
    </script>
</x-app-layout>
