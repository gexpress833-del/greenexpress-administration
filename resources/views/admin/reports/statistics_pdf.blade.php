<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Statistiques Green Express</title>
    <style>
        @page { margin: 28px 30px 34px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 10px; line-height: 1.45; }
        .header { background: #073b2a; color: #fff; padding: 20px 22px 18px; margin-bottom: 22px; border-bottom: 4px solid #d4af37; }
        .header h1 { color: #fff; font-size: 21px; font-weight: 800; letter-spacing: .4px; margin-bottom: 5px; }
        .header .period { color: #bde8d0; font-size: 10px; }
        .section-title { color: #073b2a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; margin: 19px 0 9px; border-bottom: 1px solid #bddbc9; padding-bottom: 6px; }
        .kpi-grid { width: 100%; margin-bottom: 16px; border-collapse: separate; border-spacing: 5px 0; }
        .kpi-grid td { width: 25%; padding: 12px 10px; border: 1px solid #d8e5dd; border-top: 3px solid #168a58; background: #f7fbf8; text-align: center; }
        .kpi-grid .label { color: #607267; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; margin-bottom: 4px; }
        .kpi-grid .value { font-size: 15px; font-weight: 800; color: #073b2a; }
        .kpi-grid .sub { font-size: 8px; color: #718277; margin-top: 3px; }
        table.data { width: 100%; border-collapse: collapse; border: 1px solid #d8e5dd; margin-bottom: 16px; }
        table.data th { background: #073b2a; color: #fff; padding: 8px 10px; font-size: 8px; text-align: left; text-transform: uppercase; letter-spacing: .7px; }
        table.data td { padding: 7px 10px; border-bottom: 1px solid #e8f0eb; }
        table.data tr:nth-child(even) td { background: #fbfdfb; }
        table.data tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; background: #d9f4e4; color: #086b3e; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge.red { background: #fde3e5; color: #9c2028; }
        .badge.amber { background: #fff0c7; color: #805d08; }
        .footer { margin-top: 22px; text-align: center; font-size: 8px; color: #718277; border-top: 1px solid #d8e5dd; padding-top: 10px; }
        .two-col { display: flex; gap: 14px; }
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
            <td><div class="label">Chiffre d'affaires</div><div class="value">{{ number_format($kpi['financial']['total_revenue_fc'], 0, ',', '.') }} FC</div><div class="sub">$ {{ number_format($kpi['financial']['total_revenue_usd'], 2) }}</div></td>
            <td><div class="label">Commandes validées</div><div class="value">{{ $kpi['orders']['validated'] }}</div><div class="sub">{{ $kpi['orders']['total'] }} total</div></td>
            <td><div class="label">Profit estimé</div><div class="value">{{ number_format($kpi['financial']['profit_estimate_fc'], 0, ',', '.') }} FC</div><div class="sub">$ {{ number_format($kpi['financial']['profit_estimate'], 2) }}</div></td>
            <td><div class="label">Taux annulation</div><div class="value">{{ $kpi['orders']['cancellation_rate'] }}%</div></td>
        </tr>
        <tr>
            <td><div class="label">Coût moyen livraison</div><div class="value">$ {{ number_format($kpi['financial']['avg_delivery_cost'], 2) }}</div></td>
            <td><div class="label">Retraits payés</div><div class="value">{{ number_format($kpi['financial']['withdrawals_paid_fc'], 0, ',', '.') }} FC</div><div class="sub">$ {{ number_format($kpi['financial']['withdrawals_paid'], 2) }}</div></td>
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
                <tr><td>{{ \Illuminate\Support\Str::limit($zone->delivery_address, 50) }}</td><td style="text-align:right;">{{ $zone->orders_count }}</td><td style="text-align:right;">{{ number_format($zone->total_revenue_fc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($zone->total_revenue, 2) }}</span></td></tr>
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
