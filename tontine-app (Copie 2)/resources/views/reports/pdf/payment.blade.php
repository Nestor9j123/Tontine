<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu paiement {{ $payment->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .brand { font-weight: bold; font-size: 18px; }
        .muted { color: #666; }
        .box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
        .row { display: flex; gap: 12px; }
        .col { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f7f7f7; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">Tontine App</div>
            <div class="muted">Reçu de paiement</div>
        </div>
        <div>
            <strong>Réf:</strong> {{ $payment->reference }}<br>
            <span class="muted">Émis le {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col">
                <strong>Client</strong><br>
                {{ optional($payment->client)->full_name }}<br>
                {{ optional($payment->client)->phone }}<br>
                {{ optional($payment->client)->city }}
            </div>
            <div class="col">
                <strong>Tontine</strong><br>
                Code: {{ optional($payment->tontine)->code }}<br>
                Produit: {{ optional(optional($payment->tontine)->product)->name }}<br>
                Agent: {{ optional(optional($payment->tontine)->agent)->name }}
            </div>
            <div class="col">
                <strong>Paiement</strong><br>
                Montant: {{ number_format($payment->amount, 0, ',', ' ') }} FCFA<br>
                Date: {{ optional($payment->payment_date)?->format('d/m/Y') }}<br>
                Méthode: {{ strtoupper($payment->payment_method ?? '-') }}
            </div>
        </div>
    </div>

    <table>
        <tr>
            <th>Collecté par</th>
            <th>Statut</th>
            <th>Validé par</th>
            <th>Validé le</th>
        </tr>
        <tr>
            <td>{{ optional($payment->collector)->name }}</td>
            <td>{{ ucfirst($payment->status) }}</td>
            <td>{{ optional($payment->validator)->name }}</td>
            <td>{{ optional($payment->validated_at)?->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <p class="muted" style="margin-top: 12px;">Ce reçu est généré automatiquement. Merci.</p>
</body>
</html>
