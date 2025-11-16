<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔍 Réconciliation et Détection de Fraude
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <form method="GET" action="{{ route('reconciliation.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agent</label>
                    <select name="agent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Tous les agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
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
                        Analyser
                    </button>
                </div>
            </form>
        </div>

        {{-- Vue d'ensemble --}}
        <div class="grid grid-cols-1 gap-6">
            @foreach($overview as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($item['agent']->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $item['agent']->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $item['agent']->email }}</p>
                            </div>
                        </div>
                        
                        {{-- Niveau de risque --}}
                        @if($item['risk_level'] >= 5)
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                🚨 Risque Élevé
                            </span>
                        @elseif($item['risk_level'] >= 3)
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-semibold rounded-full">
                                ⚠️ Risque Moyen
                            </span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                ✅ Risque Faible
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $item['stats']['total_payments'] }}</p>
                            <p class="text-xs text-gray-600">Paiements</p>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <p class="text-2xl font-bold text-yellow-600">{{ $item['stats']['pending_payments'] }}</p>
                            <p class="text-xs text-gray-600">En attente</p>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <p class="text-2xl font-bold text-red-600">{{ $item['stats']['rejected_payments'] }}</p>
                            <p class="text-xs text-gray-600">Rejetés</p>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <p class="text-2xl font-bold text-orange-600">{{ $item['suspicious_count'] }}</p>
                            <p class="text-xs text-gray-600">Suspects</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Taux de rejet: <span class="font-semibold {{ $item['stats']['rejection_rate'] > 10 ? 'text-red-600' : 'text-gray-900' }}">{{ $item['stats']['rejection_rate'] }}%</span>
                        </div>
                        <a href="{{ route('reconciliation.index', ['agent_id' => $item['agent']->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Voir détails →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
