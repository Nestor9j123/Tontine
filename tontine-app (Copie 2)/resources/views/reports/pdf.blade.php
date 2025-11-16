<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Avancé - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .report-date {
            font-size: 12px;
            color: #999;
        }
        .kpis {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .kpi-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            width: 22%;
            margin-bottom: 15px;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .kpi-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .kpi-growth {
            font-size: 10px;
            margin-top: 5px;
        }
        .kpi-growth.positive { color: #10b981; }
        .kpi-growth.negative { color: #ef4444; }
        
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .products-table th,
        .products-table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        .products-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 12px;
        }
        .products-table td {
            font-size: 11px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Tontine App') }}</div>
        <div class="report-title">Rapport d'Activité Avancé</div>
        <div class="report-date">Généré le {{ $date }}</div>
    </div>

    <!-- KPIs -->
    <div class="kpis">
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($kpis['total_clients']['current']) }}</div>
            <div class="kpi-label">Total Clients</div>
            @php
                $growth = $kpis['total_clients']['current'] - $kpis['total_clients']['previous'];
                $percentage = $kpis['total_clients']['previous'] > 0 ? round(($growth / $kpis['total_clients']['previous']) * 100, 1) : 0;
            @endphp
            <div class="kpi-growth {{ $growth >= 0 ? 'positive' : 'negative' }}">
                {{ $growth >= 0 ? '+' : '' }}{{ $growth }} ({{ $percentage >= 0 ? '+' : '' }}{{ $percentage }}%)
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($kpis['active_tontines']['current']) }}</div>
            <div class="kpi-label">Tontines Actives</div>
            @php
                $growth = $kpis['active_tontines']['current'] - $kpis['active_tontines']['previous'];
                $percentage = $kpis['active_tontines']['previous'] > 0 ? round(($growth / $kpis['active_tontines']['previous']) * 100, 1) : 0;
            @endphp
            <div class="kpi-growth {{ $growth >= 0 ? 'positive' : 'negative' }}">
                {{ $growth >= 0 ? '+' : '' }}{{ $growth }} ({{ $percentage >= 0 ? '+' : '' }}{{ $percentage }}%)
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($kpis['monthly_revenue']['current']) }}</div>
            <div class="kpi-label">Revenus du Mois (FCFA)</div>
            @php
                $growth = $kpis['monthly_revenue']['current'] - $kpis['monthly_revenue']['previous'];
                $percentage = $kpis['monthly_revenue']['previous'] > 0 ? round(($growth / $kpis['monthly_revenue']['previous']) * 100, 1) : 0;
            @endphp
            <div class="kpi-growth {{ $growth >= 0 ? 'positive' : 'negative' }}">
                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth) }} ({{ $percentage >= 0 ? '+' : '' }}{{ $percentage }}%)
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $kpis['completion_rate']['current'] }}%</div>
            <div class="kpi-label">Taux de Completion</div>
            @php
                $growth = $kpis['completion_rate']['current'] - $kpis['completion_rate']['previous'];
            @endphp
            <div class="kpi-growth {{ $growth >= 0 ? 'positive' : 'negative' }}">
                {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
            </div>
        </div>
    </div>

    <!-- Top Produits -->
    <div class="section">
        <div class="section-title">Top Produits par Performance</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Produit</th>
                    <th>Prix (FCFA)</th>
                    <th>Nb. Tontines</th>
                    <th>Revenus Totaux (FCFA)</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price) }}</td>
                    <td>{{ number_format($product->tontines_count) }}</td>
                    <td>{{ number_format($product->total_revenue) }}</td>
                    <td>{{ $product->is_active ? 'Actif' : 'Inactif' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Résumé Exécutif -->
    <div class="section">
        <div class="section-title">Résumé Exécutif</div>
        <p style="line-height: 1.6; font-size: 12px;">
            <strong>Performance Globale :</strong> 
            Au {{ $date }}, l'activité de l'entreprise montre 
            {{ $kpis['total_clients']['current'] }} clients actifs avec 
            {{ $kpis['active_tontines']['current'] }} tontines en cours.
        </p>
        
        <p style="line-height: 1.6; font-size: 12px;">
            <strong>Revenus :</strong> 
            Les revenus du mois s'élèvent à {{ number_format($kpis['monthly_revenue']['current']) }} FCFA, 
            @php
                $growth = $kpis['monthly_revenue']['current'] - $kpis['monthly_revenue']['previous'];
                $percentage = $kpis['monthly_revenue']['previous'] > 0 ? round(($growth / $kpis['monthly_revenue']['previous']) * 100, 1) : 0;
            @endphp
            représentant une {{ $growth >= 0 ? 'augmentation' : 'diminution' }} de {{ abs($percentage) }}% 
            par rapport au mois précédent.
        </p>

        <p style="line-height: 1.6; font-size: 12px;">
            <strong>Taux de Completion :</strong> 
            Le taux de completion des tontines est de {{ $kpis['completion_rate']['current'] }}%, 
            ce qui {{ $kpis['completion_rate']['current'] >= 70 ? 'indique une bonne performance' : 'nécessite une attention particulière' }}.
        </p>

        <p style="line-height: 1.6; font-size: 12px;">
            <strong>Produits Performants :</strong> 
            @if($topProducts->count() > 0)
                Le produit "{{ $topProducts->first()->name }}" reste le plus populaire avec 
                {{ $topProducts->first()->tontines_count }} tontines actives.
            @else
                Aucune donnée de produit disponible.
            @endif
        </p>
    </div>

    <!-- Recommandations -->
    <div class="section">
        <div class="section-title">Recommandations</div>
        <ul style="font-size: 12px; line-height: 1.6;">
            @if($kpis['completion_rate']['current'] < 70)
                <li><strong>Améliorer le taux de completion :</strong> Mettre en place des rappels automatiques et un suivi personnalisé des clients.</li>
            @endif
            
            @if($kpis['monthly_revenue']['current'] < $kpis['monthly_revenue']['previous'])
                <li><strong>Stimuler les revenus :</strong> Analyser les causes de la baisse et développer des stratégies de relance commerciale.</li>
            @endif
            
            <li><strong>Diversification :</strong> Considérer l'ajout de nouveaux produits basés sur l'analyse des préférences clients.</li>
            <li><strong>Fidélisation :</strong> Développer un programme de fidélité pour les clients les plus actifs.</li>
            <li><strong>Digitalisation :</strong> Continuer l'amélioration de l'expérience utilisateur numérique.</li>
        </ul>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>Ce rapport a été généré automatiquement par {{ config('app.name', 'Tontine App') }}</p>
        <p>Pour plus d'informations, contactez l'équipe d'administration</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'Tontine App') }}. Tous droits réservés.</p>
    </div>
</body>
</html>
