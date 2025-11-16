<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Classement des Agents
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <form method="GET" action="{{ route('agents.ranking') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Tout le temps</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                    <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="clients" {{ $sortBy == 'clients' ? 'selected' : '' }}>Nombre de clients</option>
                        <option value="tontines" {{ $sortBy == 'tontines' ? 'selected' : '' }}>Nombre de tontines</option>
                        <option value="payments" {{ $sortBy == 'payments' ? 'selected' : '' }}>Nombre de paiements</option>
                        <option value="amount" {{ $sortBy == 'amount' ? 'selected' : '' }}>Montant collecté</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Podium (Top 3) --}}
        @if($agents->currentPage() == 1 && $agents->count() >= 3)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            {{-- 2ème place --}}
            @if(isset($agents[1]))
            <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow-lg p-6 border-2 border-gray-300 order-2 md:order-1">
                <div class="text-center">
                    <div class="text-4xl mb-2">🥈</div>
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-2xl mb-3">
                        {{ substr($agents[1]->name, 0, 1) }}
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">{{ $agents[1]->name }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $agents[1]->email }}</p>
                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <span class="text-2xl">{{ $agents[1]->badge['icon'] }}</span>
                        <span class="px-3 py-1 bg-{{ $agents[1]->badge['color'] }}-100 text-{{ $agents[1]->badge['color'] }}-800 text-xs font-bold rounded-full">
                            {{ $agents[1]->badge['name'] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Clients</p>
                            <p class="font-bold text-gray-900">{{ $agents[1]->clients_count }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Paiements</p>
                            <p class="font-bold text-gray-900">{{ $agents[1]->payments_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 1ère place --}}
            @if(isset($agents[0]))
            <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl shadow-2xl p-6 border-4 border-yellow-400 transform md:scale-110 order-1 md:order-2">
                <div class="text-center">
                    <div class="text-5xl mb-2">👑</div>
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-r from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-3xl mb-3 ring-4 ring-yellow-300">
                        {{ substr($agents[0]->name, 0, 1) }}
                    </div>
                    <h3 class="font-bold text-xl text-gray-900">{{ $agents[0]->name }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $agents[0]->email }}</p>
                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <span class="text-3xl">{{ $agents[0]->badge['icon'] }}</span>
                        <span class="px-3 py-1 bg-{{ $agents[0]->badge['color'] }}-100 text-{{ $agents[0]->badge['color'] }}-800 text-xs font-bold rounded-full">
                            {{ $agents[0]->badge['name'] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Clients</p>
                            <p class="font-bold text-gray-900">{{ $agents[0]->clients_count }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Paiements</p>
                            <p class="font-bold text-gray-900">{{ $agents[0]->payments_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 3ème place --}}
            @if(isset($agents[2]))
            <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl shadow-lg p-6 border-2 border-orange-300 order-3">
                <div class="text-center">
                    <div class="text-4xl mb-2">🥉</div>
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-r from-orange-400 to-orange-500 flex items-center justify-center text-white font-bold text-2xl mb-3">
                        {{ substr($agents[2]->name, 0, 1) }}
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">{{ $agents[2]->name }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $agents[2]->email }}</p>
                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <span class="text-2xl">{{ $agents[2]->badge['icon'] }}</span>
                        <span class="px-3 py-1 bg-{{ $agents[2]->badge['color'] }}-100 text-{{ $agents[2]->badge['color'] }}-800 text-xs font-bold rounded-full">
                            {{ $agents[2]->badge['name'] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Clients</p>
                            <p class="font-bold text-gray-900">{{ $agents[2]->clients_count }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="text-gray-500 text-xs">Paiements</p>
                            <p class="font-bold text-gray-900">{{ $agents[2]->payments_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Classement complet --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Badge</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clients</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tontines</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paiements</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($agents as $agent)
                        <tr class="hover:bg-gray-50 {{ $agent->rank <= 3 ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold {{ $agent->rank == 1 ? 'text-yellow-600' : ($agent->rank == 2 ? 'text-gray-600' : ($agent->rank == 3 ? 'text-orange-600' : 'text-gray-900')) }}">
                                    #{{ $agent->rank }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($agent->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $agent->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $agent->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="text-2xl">{{ $agent->badge['icon'] }}</span>
                                    <span class="px-2 py-1 bg-{{ $agent->badge['color'] }}-100 text-{{ $agent->badge['color'] }}-800 text-xs font-semibold rounded-full">
                                        {{ $agent->badge['name'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $agent->clients_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $agent->tontines_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $agent->payments_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                {{ number_format($agent->total_amount ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2" style="width: 100px;">
                                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $agent->performance_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $agent->performance_score }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination-info :paginator="$agents" />
        </div>
    </div>
</x-app-layout>
