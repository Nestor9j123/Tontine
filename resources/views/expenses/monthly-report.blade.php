<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rapport des Charges - {{ \Carbon\Carbon::create($year, $month, 1)->locale('fr')->isoFormat('MMMM Y') }}
                </h2>
                <p class="text-sm text-gray-600">Détail des charges mensuelles et dépenses par agent</p>
            </div>
            <a href="{{ route('expenses.index', ['month' => $month, 'year' => $year]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                Retour aux charges
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- Résumé global --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total du mois</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalMonth, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Électricité</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalByType['electricity'] ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Loyer</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalByType['rent'] ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase">Dépenses Agents</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalByType['agent_expense'] ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        {{-- Détail par type de charge --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Détail des charges</h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($expense->type === 'electricity') bg-yellow-100 text-yellow-800
                                        @elseif($expense->type === 'rent') bg-green-100 text-green-800
                                        @elseif($expense->type === 'agent_expense') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $expense->type_human ?? ucfirst(str_replace('_', ' ', $expense->type)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $expense->description }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-gray-900">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $expense->user?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    Aucune charge enregistrée pour cette période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Détail des dépenses par agent --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Dépenses par Agent</h3>
            </div>
            <div class="p-6">
                @if($agentExpenses->count() > 0)
                    <div class="space-y-4">
                        @foreach($agentExpenses as $agentId => $data)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $data['agent']->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $data['agent']->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 uppercase">Total dépenses</p>
                                        <p class="text-lg font-bold text-red-600">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>
                                @if($data['expenses']->count() > 0)
                                    <div class="mt-2 border-t border-gray-100 pt-2">
                                        <ul class="divide-y divide-gray-100 text-sm">
                                            @foreach($data['expenses'] as $expense)
                                                <li class="py-1 flex justify-between">
                                                    <span>{{ $expense->expense_date->format('d/m') }} - {{ $expense->description }}</span>
                                                    <span class="font-medium">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Aucune dépense agent pour cette période.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
