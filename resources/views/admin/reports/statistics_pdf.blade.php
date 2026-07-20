<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Statistiques Green Express</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #052e16; font-size: 22px; margin-bottom: 4px; }
        .header .period { color: #64748b; font-size: 11px; }
        .section-title { color: #14532d; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin: 18px 0 8px; border-bottom: 2px solid #dcfce7; padding-bottom: 4px; }
        .kpi-grid { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .kpi-grid td { width: 25%; padding: 12px 10px; border: 1px solid #dbe7df; background: #f8faf9; text-align: center; }
        .kpi-grid .label { color: #64748b; font-size: 8px; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 3px; }
        .kpi-grid .value { font-size: 15px; font-weight: 800; color: #14532d; }
        .kpi-grid .sub { font-size: 9px; color: #94a3b8; margin-top: 2px; }
        table.data { width: 100%; border-collapse: collapse; border: 1px solid #dbe7df; margin-bottom: 14px; }
        table.data th { background: #f0fdf4; color: #14532d; padding: 7px 10px; font-size: 9px; text-align: left; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #dbe7df; }
        table.data td { padding: 6px 10px; border-bottom: 1px solid #edf4ef; }
        table.data tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; background: #dcfce7; color: #166534; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge.red { background: #fef2f2; color: #991b1b; }
        .badge.amber { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #64748b; border-top: 1px solid #dbe7df; padding-top: 10px; }
        .two-col { display: flex; gap: 16px; }
        .two-col > div { flex: 1; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Green Express — Rapport Statistiques</h1>
        <div class="period">Période : {{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}</div>
    </div>

    <div class="section-title">Indicateurs clés</div>
    <table class="kpi-grid">
        <tr>
            <td><div class="label">Chiffre d'affaires</div><div class="value">$ {{ number_format($kpi['financial']['total_revenue_usd'], 2) }}</div></td>
            <td><div class="label">Commandes validées</div><div class="value">{{ $kpi['orders']['validated'] }}</div><div class="sub">{{ $kpi['orders']['total'] }} total</div></td>
            <td><div class="label">Profit estimé</div><div class="value">$ {{ number_format($kpi['financial']['profit_estimate'], 2) }}</div></td>
            <td><div class="label">Taux annulation</div><div class="value">{{ $kpi['orders']['cancellation_rate'] }}%</div></td>
        </tr>
        <tr>
            <td><div class="label">Coût moyen livraison</div><div class="value">$ {{ number_format($kpi['financial']['avg_delivery_cost'], 2) }}</div></td>
            <td><div class="label">Retraits payés</div><div class="value">$ {{ number_format($kpi['financial']['withdrawals_paid'], 2) }}</div></td>
            <td><div class="label">Clients</div><div class="value">{{ $totalClients }}</div></td>
            <td><div class="label">Agents / Livreurs</div><div class="value">{{ $totalAgents }} / {{ $totalLivreurs }}</div></td>
        </tr>
    </table>

    <div class="section-title">Abonnements</div>
    <table class="kpi-grid">
        <tr>
            <td><div class="label">Abonnements actifs</div><div class="value">{{ $kpi['subscriptions']['active'] }}</div></td>
            <td><div class="label">Expirés</div><div class="value">{{ $kpi['subscriptions']['expired'] }}</div></td>
            <td><div class="label">Renouvellements (mois)</div><div class="value">{{ $kpi['subscriptions']['renewals_this_month'] }}</div></td>
            <td><div class="label">Taux renouvellement</div><div class="value">{{ $kpi['subscriptions']['renewal_rate'] }}%</div></td>
        </tr>
        <tr>
            <td><div class="label">Revenus hebdo</div><div class="value">$ {{ number_format($kpi['subscriptions']['weekly_revenue'], 2) }}</div></td>
            <td><div class="label">Revenus mensuels</div><div class="value">$ {{ number_format($kpi['subscriptions']['monthly_revenue'], 2) }}</div></td>
            <td><div class="label">Repas livrés</div><div class="value">{{ $kpi['subscriptions']['meals_delivered'] }}</div></td>
            <td><div class="label">Nouveaux abonnés</div><div class="value">{{ $kpi['subscriptions']['new_subscribers'] }}</div></td>
        </tr>
    </table>

    <div class="two-col">
        <div>
            <div class="section-title">Commandes par statut</div>
            <table class="data">
                <thead><tr><th>Statut</th><th style="text-align:right;">Nombre</th></tr></thead>
                <tbody>
                    @php $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'preparing' => 'En préparation', 'delivering' => 'En livraison', 'delivered' => 'Livrée', 'cancelled' => 'Annulée']; @endphp
                    @foreach ($statusLabels as $status => $label)
                        <tr><td>{{ $label }}</td><td style="text-align:right;">{{ $ordersByStatus[$status] ?? 0 }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <div class="section-title">Top Agents</div>
            <table class="data">
                <thead><tr><th>Agent</th><th style="text-align:right;">Commandes</th></tr></thead>
                <tbody>
                    @forelse ($kpi['top_agents'] as $agent)
                        <tr><td>{{ $agent->name }}</td><td style="text-align:right;">{{ $agent->orders_as_agent_count }}</td></tr>
                    @empty
                        <tr><td colspan="2" style="color:#94a3b8;">Aucune donnée</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-title">Zones les plus rentables</div>
    <table class="data">
        <thead><tr><th>Adresse</th><th style="text-align:right;">Commandes</th><th style="text-align:right;">Revenu</th></tr></thead>
        <tbody>
            @forelse ($kpi['profitable_zones'] as $zone)
                <tr><td>{{ \Illuminate\Support\Str::limit($zone->delivery_address, 50) }}</td><td style="text-align:right;">{{ $zone->orders_count }}</td><td style="text-align:right;">$ {{ number_format($zone->total_revenue, 2) }}</td></tr>
            @empty
                <tr><td colspan="3" style="color:#94a3b8;">Aucune donnée</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Green Express — Rapport généré le {{ $generatedAt->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
