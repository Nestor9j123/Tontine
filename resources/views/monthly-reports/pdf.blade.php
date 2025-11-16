<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - {{ $monthlyReport->report_period }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 20px;
        }
        .summary-card.success h3 { color: #16a34a; }
        .summary-card.danger h3 { color: #dc2626; }
        .summary-card.info h3 { color: #2563eb; }
        .summary-card p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        td.text-center {
            text-align: center;
        }
        td.text-right {
            text-align: right;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .stat-item strong {
            display: block;
            font-size: 16px;
            margin-bottom: 3px;
        }
        .stat-item span {
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 10px; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>Rapport Mensuel</h1>
        <p>Période : {{ $monthlyReport->report_period }}</p>
        <p>Généré le : {{ $monthlyReport->generated_at->format('d/m/Y H:i') }} par {{ $monthlyReport->generator->name }}</p>
    </div>

    <!-- Résumé Financier -->
    <div class="section">
        <h2>Résumé Financier</h2>
        <div class="summary-grid">
            <div class="summary-card success">
                <h3>{{ number_format($monthlyReport->total_revenue, 0, ',', ' ') }}</h3>
                <p>Chiffre d'Affaires (FCFA)</p>
            </div>
            <div class="summary-card danger">
                <h3>{{ number_format($monthlyReport->total_expenses, 0, ',', ' ') }}</h3>
                <p>Total Charges (FCFA)</p>
            </div>
            <div class="summary-card {{ $monthlyReport->net_result >= 0 ? 'success' : 'danger' }}">
                <h3>{{ number_format($monthlyReport->net_result, 0, ',', ' ') }}</h3>
                <p>Résultat Net (FCFA)</p>
            </div>
            <div class="summary-card info">
                <h3>{{ $monthlyReport->payment_stats['validated_payments'] ?? 0 }}</h3>
                <p>Paiements Validés</p>
            </div>
        </div>
    </div>

    <!-- Évolution du Stock -->
    <div class="section">
        <h2>Évolution du Stock</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-center">Stock Initial</th>
                    <th class="text-center">Stock Final</th>
                    <th class="text-center">Variation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyReport->initial_stock as $productId => $initialData)
                @php
                    $finalData = $monthlyReport->final_stock[$productId] ?? ['quantity' => 0];
                    $variation = $finalData['quantity'] - $initialData['quantity'];
                @endphp
                <tr>
                    <td>{{ $initialData['name'] }}</td>
                    <td class="text-center">{{ $initialData['quantity'] }}</td>
                    <td class="text-center">{{ $finalData['quantity'] }}</td>
                    <td class="text-center">
                        <span class="{{ $variation >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $variation > 0 ? '+' : '' }}{{ $variation }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Produits Vendus -->
    @if(count($monthlyReport->products_sold) > 0)
    <div class="section">
        <h2>Produits Vendus</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Chiffre d'Affaires (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyReport->products_sold as $productId => $salesData)
                <tr>
                    <td>{{ $salesData['name'] }}</td>
                    <td class="text-center">{{ $salesData['quantity'] }}</td>
                    <td class="text-right">{{ number_format($salesData['revenue'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Statistiques des Paiements -->
    <div class="section">
        <h2>Statistiques des Paiements</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <strong style="color: #2563eb;">{{ $monthlyReport->payment_stats['total_payments'] ?? 0 }}</strong>
                <span>Total Paiements</span>
            </div>
            <div class="stat-item">
                <strong style="color: #16a34a;">{{ $monthlyReport->payment_stats['validated_payments'] ?? 0 }}</strong>
                <span>Paiements Validés</span>
            </div>
            <div class="stat-item">
                <strong style="color: #ca8a04;">{{ $monthlyReport->payment_stats['pending_payments'] ?? 0 }}</strong>
                <span>En Attente</span>
            </div>
            <div class="stat-item">
                <strong style="color: #dc2626;">{{ $monthlyReport->payment_stats['rejected_payments'] ?? 0 }}</strong>
                <span>Rejetés</span>
            </div>
        </div>
    </div>

    <!-- Performance des Agents -->
    @if(count($monthlyReport->agent_performance) > 0)
    <div class="section">
        <h2>Performance des Agents</h2>
        <table>
            <thead>
                <tr>
                    <th>Agent</th>
                    <th class="text-center">Paiements</th>
                    <th class="text-right">Montant Collecté (FCFA)</th>
                    <th class="text-right">Dépenses (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyReport->agent_performance as $agentId => $performance)
                <tr>
                    <td>{{ $performance['name'] }}</td>
                    <td class="text-center">{{ $performance['payments_count'] }}</td>
                    <td class="text-right">{{ number_format($performance['payments_amount'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($performance['expenses'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Notes -->
    @if($monthlyReport->notes)
    <div class="section">
        <h2>Notes</h2>
        <p>{!! nl2br(e($monthlyReport->notes)) !!}</p>
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        <p>Rapport généré automatiquement par Tontine App</p>
        <p>Date de génération : {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
