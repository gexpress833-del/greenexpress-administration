<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>États financiers Green Express</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #052e16; font-size: 22px; margin-bottom: 4px; }
        .header .period { color: #64748b; font-size: 11px; }
        .section-title { color: #14532d; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin: 18px 0 8px; border-bottom: 2px solid #dcfce7; padding-bottom: 4px; }

        .summary { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .summary td { width: 33%; padding: 14px 12px; border: 1px solid #dbe7df; background: #f8faf9; text-align: center; }
        .summary .label { color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px; }
        .summary .value { font-size: 16px; font-weight: 800; }
        .summary .value.green { color: #15803d; }
        .summary .value.red { color: #dc2626; }
        .summary .value.blue { color: #2563eb; }

        .balance-sheet { width: 100%; border-collapse: collapse; border: 1px solid #dbe7df; margin-bottom: 14px; }
        .balance-sheet th { background: #f0fdf4; color: #14532d; padding: 8px 12px; font-size: 10px; text-align: left; }
        .balance-sheet td { padding: 8px 12px; border-bottom: 1px solid #edf4ef; }
        .balance-sheet td.amount { text-align: right; font-weight: 600; }
        .balance-sheet tr.total td { background: #f0fdf4; font-weight: 800; font-size: 12px; border-top: 2px solid #15803d; }
        .balance-sheet tr.total td.amount { color: #15803d; }

        table.data { width: 100%; border-collapse: collapse; border: 1px solid #dbe7df; margin-bottom: 14px; }
        table.data th { background: #f0fdf4; color: #14532d; padding: 7px 10px; font-size: 9px; text-align: left; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #dbe7df; }
        table.data td { padding: 6px 10px; border-bottom: 1px solid #edf4ef; }
        table.data tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge.green { background: #dcfce7; color: #166534; }
        .badge.red { background: #fef2f2; color: #991b1b; }
        .badge.amber { background: #fef3c7; color: #92400e; }
        .badge.blue { background: #dbeafe; color: #1e40af; }

        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #64748b; border-top: 1px solid #dbe7df; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Green Express — États Financiers</h1>
        <div class="period">Période : {{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}</div>
    </div>

    <div class="section-title">Synthèse</div>
    <table class="summary">
        <tr>
            <td>
                <div class="label">Revenus totaux</div>
                <div class="value green">$ {{ number_format($totalIncome, 2) }}</div>
            </td>
            <td>
                <div class="label">Dépenses totales</div>
                <div class="value red">$ {{ number_format($totalExpenses, 2) }}</div>
            </td>
            <td>
                <div class="label">Profit net</div>
                <div class="value {{ $netProfit >= 0 ? 'green' : 'red' }}">$ {{ number_format($netProfit, 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Compte de résultat</div>
    <table class="balance-sheet">
        <tr>
            <td>Revenus des commandes livrées</td>
            <td class="amount">$ {{ number_format($totalRevenue, 2) }}</td>
        </tr>
        <tr>
            <td>Revenus des abonnements actifs</td>
            <td class="amount">$ {{ number_format($subscriptionsRevenue, 2) }}</td>
        </tr>
        <tr class="total">
            <td>Total des revenus</td>
            <td class="amount">$ {{ number_format($totalIncome, 2) }}</td>
        </tr>
        <tr>
            <td>Retraits de points payés</td>
            <td class="amount">$ {{ number_format($withdrawalsPaid, 2) }}</td>
        </tr>
        <tr>
            <td>Retraits en attente</td>
            <td class="amount">$ {{ number_format($withdrawalsPending, 2) }}</td>
        </tr>
        <tr class="total">
            <td>Total des dépenses</td>
            <td class="amount">$ {{ number_format($totalExpenses, 2) }}</td>
        </tr>
        <tr class="total">
            <td>Résultat net</td>
            <td class="amount" style="color: {{ $netProfit >= 0 ? '#15803d' : '#dc2626' }};">$ {{ number_format($netProfit, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Commandes livrées et validées</div>
    <table class="data">
        <thead>
            <tr>
                <th>N°</th>
                <th>Date</th>
                <th>Client</th>
                <th>Agent</th>
                <th style="text-align:right;">USD</th>
                <th style="text-align:right;">FC</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($validatedOrders as $o)
                <tr>
                    <td>{{ $o->code }}</td>
                    <td>{{ $o->client_validated_at?->format('d/m/Y') }}</td>
                    <td>{{ $o->client_name }}</td>
                    <td>{{ $o->agent?->name ?? 'N/A' }}</td>
                    <td style="text-align:right;">$ {{ number_format((float) $o->total_amount, 2) }}</td>
                    <td style="text-align:right;">{{ number_format((float) $o->total_amount_fc, 0, ',', '.') }} FC</td>
                </tr>
            @empty
                <tr><td colspan="6" style="color:#94a3b8;">Aucune commande livrée sur cette période</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Retraits de points</div>
    <table class="data">
        <thead>
            <tr>
                <th>Date</th>
                <th>Demandeur</th>
                <th>Opérateur</th>
                <th>Statut</th>
                <th style="text-align:right;">Points</th>
                <th style="text-align:right;">Montant USD</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($withdrawals as $w)
                <tr>
                    <td>{{ $w->created_at->format('d/m/Y') }}</td>
                    <td>{{ $w->user?->name ?? $w->agent?->name ?? 'N/A' }}</td>
                    <td>{{ $w->mobile_money_operator ?? '—' }}</td>
                    <td>
                        @php $cls = match($w->status) { 'paid' => 'green', 'approved' => 'blue', 'rejected' => 'red', default => 'amber' }; @endphp
                        <span class="badge {{ $cls }}">{{ ucfirst($w->status) }}</span>
                    </td>
                    <td style="text-align:right;">{{ $w->points }}</td>
                    <td style="text-align:right;">$ {{ number_format((float) $w->amount_usd, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="color:#94a3b8;">Aucun retrait sur cette période</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Green Express — États financiers générés le {{ $generatedAt->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
