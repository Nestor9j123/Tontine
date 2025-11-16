<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rapports • Tontines</h2>
            <div class="space-x-2">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-lg">Résumé</a>
                <a href="{{ route('reports.export.tontines') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg">Exporter (Excel)</a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tontines as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $t->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($t->client)->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($t->product)->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($t->agent)->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($t->start_date)?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($t->end_date)?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($t->total_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($t->paid_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($t->remaining_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $t->status === 'active' ? 'bg-blue-100 text-blue-800' : ($t->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$tontines" />
    </div>
</x-app-layout>
