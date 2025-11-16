<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu Mensuel - {{ $agent->name }} - {{ $period['period_name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #666;
            margin-top: 10px;
        }
        .agent-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .summary-card .label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin: 25px 0 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .amount {
            font-weight: bold;
        }
        .status-validated {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
        }
        .signature-box {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Tontine Management') }}</div>
        <div class="document-title">REÇU MENSUEL AGENT</div>
        <div style="margin-top: 10px; font-size: 14px;">{{ $period['period_name'] }}</div>
    </div>

    <div class="agent-info">
        <strong>Agent:</strong> {{ $agent->name }}<br>
        <strong>Email:</strong> {{ $agent->email }}<br>
        <strong>Période:</strong> {{ $period['start_date']->format('d/m/Y') }} au {{ $period['end_date']->format('d/m/Y') }}
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="value">{{ $summary['validated_payments'] }}</div>
            <div class="label">Paiements Collectés</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ number_format($summary['total_amount_collected'], 0, ',', ' ') }} FCFA</div>
            <div class="label">Montant Total Collecté</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $summary['new_clients'] }}</div>
            <div class="label">Nouveaux Clients</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ number_format($summary['total_expenses'], 0, ',', ' ') }} FCFA</div>
            <div class="label">Total Dépenses</div>
        </div>
    </div>

    @if($payments->count() > 0)
    <div class="section-title">Détail des Paiements Collectés</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Produit</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $date => $dailyPayments)
                @foreach($dailyPayments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td>{{ $payment->client->name }}</td>
                    <td>{{ $payment->tontine->product->name }}</td>
                    <td class="text-right amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">
                        @if($payment->status === 'validated')
                            <span class="status-validated">Validé</span>
                        @elseif($payment->status === 'pending')
                            <span class="status-pending">En attente</span>
                        @else
                            <span class="status-rejected">Rejeté</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    @endif

    @if($expenses->count() > 0)
    <div class="section-title">Dépenses de l'Agent</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                <td>{{ $expense->description }}</td>
                <td class="text-right amount">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                <td>{{ $expense->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="2" class="text-right">TOTAL DÉPENSES:</td>
                <td class="text-right amount">{{ number_format($expenses->sum('amount'), 0, ',', ' ') }} FCFA</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="section-title">Statistiques Additionnelles</div>
    <table>
        <tr>
            <td><strong>Tontines Actives:</strong></td>
            <td class="text-center">{{ $summary['active_tontines'] }}</td>
            <td><strong>Tontines Complétées:</strong></td>
            <td class="text-center">{{ $summary['completed_tontines'] }}</td>
        </tr>
        <tr>
            <td><strong>Paiements en Attente:</strong></td>
            <td class="text-center">{{ $summary['pending_payments'] }}</td>
            <td><strong>Montant en Attente:</strong></td>
            <td class="text-center amount">{{ number_format($summary['pending_amount'], 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <div class="signature-section">
        <div>
            <div class="signature-box">
                Signature de l'Agent
            </div>
        </div>
        <div>
            <div class="signature-box">
                Signature du Responsable
            </div>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}<br>
        {{ config('app.name', 'Tontine Management System') }} - Système de Gestion des Tontines
    </div>
</body>
</html>
