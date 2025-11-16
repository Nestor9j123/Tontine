<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Statistiques Détaillées') }}
            </h2>
            <a href="{{ route('system-settings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Retour aux paramètres
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistiques mensuelles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques du mois</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['monthly_stats']['clients_created'] }}</div>
                            <div class="text-sm text-blue-800">Nouveaux clients</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['monthly_stats']['tontines_created'] }}</div>
                            <div class="text-sm text-green-800">Nouvelles tontines</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['monthly_stats']['payments_made'] }}</div>
                            <div class="text-sm text-yellow-800">Paiements effectués</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations base de données -->
            @if($stats['database_size'])
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Base de données</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600">Taille de la base de données</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $stats['database_size']->size_mb ?? 'N/A' }} MB</div>
                        <div class="text-xs text-gray-500">Table principale: {{ $stats['database_size']->table ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Logs récents -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activités récentes</h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-sm text-blue-800">{{ $stats['recent_logs'] }}</div>
                        <div class="text-xs text-blue-600 mt-2">
                            Consultez les fichiers de logs dans <code>storage/logs/</code> pour voir les activités détaillées.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.deleted-clients') }}" 
                           class="bg-red-100 hover:bg-red-200 p-4 rounded-lg border border-red-200 transition-colors">
                            <div class="font-semibold text-red-800">Clients supprimés</div>
                            <div class="text-sm text-red-600">Gérer les clients supprimés</div>
                        </a>
                        
                        <a href="{{ route('system-settings.index') }}" 
                           class="bg-purple-100 hover:bg-purple-200 p-4 rounded-lg border border-purple-200 transition-colors">
                            <div class="font-semibold text-purple-800">Paramètres système</div>
                            <div class="text-sm text-purple-600">Configurer l'application</div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
