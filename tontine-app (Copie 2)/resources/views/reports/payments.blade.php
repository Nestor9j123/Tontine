<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rapports • Paiements</h2>
            <div class="space-x-2">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-lg">Résumé</a>
                <a href="{{ route('reports.export.payments') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">Exporter (Excel)</a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tontine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collecteur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $p->reference }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($p->client)->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($p->tontine)->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($p->amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($p->payment_date)?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ optional($p->collector)->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $p->status === 'validated' ? 'bg-green-100 text-green-800' : ($p->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('reports.export.payment.pdf', $p) }}" class="text-blue-600 hover:text-blue-800">Télécharger reçu (PDF)</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$payments" />
    </div>
</x-app-layout>
