<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Administration Avancée') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques générales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Clients -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Clients</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['clients']['active'] }}</div>
                                @if($stats['clients']['deleted'] > 0)
                                    <div class="text-xs text-red-500">{{ $stats['clients']['deleted'] }} supprimés</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tontines -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Tontines</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['tontines']['active'] }}</div>
                                @if($stats['tontines']['deleted'] > 0)
                                    <div class="text-xs text-red-500">{{ $stats['tontines']['deleted'] }} supprimées</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paiements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Paiements</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['payments']['active'] }}</div>
                                @if($stats['payments']['deleted'] > 0)
                                    <div class="text-xs text-red-500">{{ $stats['payments']['deleted'] }} supprimés</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produits -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Produits</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['products']['active'] }}</div>
                                @if($stats['products']['inactive'] > 0)
                                    <div class="text-xs text-gray-500">{{ $stats['products']['inactive'] }} inactifs</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertes données orphelines -->
            @if(array_sum($orphanedData) > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-red-800">Données orphelines détectées</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    @if($orphanedData['tontines_without_client'] > 0)
                        <div class="text-sm text-red-700">
                            <strong>{{ $orphanedData['tontines_without_client'] }}</strong> tontines sans client
                        </div>
                    @endif
                    @if($orphanedData['tontines_without_product'] > 0)
                        <div class="text-sm text-red-700">
                            <strong>{{ $orphanedData['tontines_without_product'] }}</strong> tontines sans produit
                        </div>
                    @endif
                    @if($orphanedData['payments_without_client'] > 0)
                        <div class="text-sm text-red-700">
                            <strong>{{ $orphanedData['payments_without_client'] }}</strong> paiements sans client
                        </div>
                    @endif
                    @if($orphanedData['payments_without_tontine'] > 0)
                        <div class="text-sm text-red-700">
                            <strong>{{ $orphanedData['payments_without_tontine'] }}</strong> paiements sans tontine
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.clean-orphaned-data') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer toutes les données orphelines ? Cette action est irréversible.')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Nettoyer les données orphelines
                    </button>
                </form>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Clients supprimés -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestion des suppressions</h3>
                        <p class="text-sm text-gray-600 mb-4">Gérer les clients supprimés et leurs données associées.</p>
                        <a href="{{ route('admin.deleted-clients') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Voir les clients supprimés
                        </a>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques détaillées</h3>
                        <p class="text-sm text-gray-600 mb-4">Voir les statistiques complètes et l'utilisation de la base de données.</p>
                        <a href="{{ route('admin.statistics') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Voir les statistiques
                        </a>
                    </div>
                </div>

                <!-- Paramètres système -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuration</h3>
                        <p class="text-sm text-gray-600 mb-4">Configurer les thèmes, couleurs et paramètres système.</p>
                        <a href="{{ route('system-settings.index') }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Paramètres système
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
