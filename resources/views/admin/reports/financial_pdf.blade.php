<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>États financiers Green Express</title>
    <style>
        @page { margin: 28px 30px 34px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 10px; line-height: 1.45; }
        .header { background: #073b2a; color: #fff; padding: 20px 22px 18px; margin-bottom: 22px; border-bottom: 4px solid #d4af37; }
        .header h1 { color: #fff; font-size: 21px; font-weight: 800; letter-spacing: .4px; margin-bottom: 5px; }
        .header .period { color: #bde8d0; font-size: 10px; }
        .section-title { color: #073b2a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; margin: 19px 0 9px; border-bottom: 1px solid #bddbc9; padding-bottom: 6px; }
        .summary { width: 100%; margin-bottom: 16px; border-collapse: separate; border-spacing: 6px 0; }
        .summary td { width: 33%; padding: 14px 12px; border: 1px solid #d8e5dd; border-top: 3px solid #168a58; background: #f7fbf8; text-align: center; }
        .summary .label { color: #607267; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
        .summary .value { font-size: 16px; font-weight: 800; }
        .summary .value.green { color: #087443; }
        .summary .value.red { color: #bd2e35; }
        .summary .value.blue { color: #1769aa; }
        .balance-sheet { width: 100%; border-collapse: collapse; border: 1px solid #d8e5dd; margin-bottom: 16px; }
        .balance-sheet th { background: #073b2a; color: #fff; padding: 8px 12px; font-size: 9px; text-align: left; }
        .balance-sheet td { padding: 8px 12px; border-bottom: 1px solid #e8f0eb; }
        .balance-sheet td.amount { text-align: right; font-weight: 600; }
        .balance-sheet tr:nth-child(even) td { background: #fbfdfb; }
        .balance-sheet tr.total td { background: #edf8f1; color: #073b2a; font-weight: 800; font-size: 11px; border-top: 2px solid #168a58; }
        .balance-sheet tr.total td.amount { color: #087443; }
        table.data { width: 100%; border-collapse: collapse; border: 1px solid #d8e5dd; margin-bottom: 16px; }
        table.data th { background: #073b2a; color: #fff; padding: 8px 10px; font-size: 8px; text-align: left; text-transform: uppercase; letter-spacing: .7px; }
        table.data td { padding: 7px 10px; border-bottom: 1px solid #e8f0eb; }
        table.data tr:nth-child(even) td { background: #fbfdfb; }
        table.data tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge.green { background: #d9f4e4; color: #086b3e; }
        .badge.red { background: #fde3e5; color: #9c2028; }
        .badge.amber { background: #fff0c7; color: #805d08; }
        .badge.blue { background: #dcecf9; color: #145685; }
        .footer { margin-top: 22px; text-align: center; font-size: 8px; color: #718277; border-top: 1px solid #d8e5dd; padding-top: 10px; }
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
                <div class="value green">{{ number_format($totalIncomeFc, 0, ',', '.') }} FC</div>
                <div style="font-size:8px;color:#718277;margin-top:3px;">$ {{ number_format($totalIncome, 2) }}</div>
            </td>
            <td>
                <div class="label">Dépenses totales</div>
                <div class="value red">{{ number_format($totalExpensesFc, 0, ',', '.') }} FC</div>
                <div style="font-size:8px;color:#718277;margin-top:3px;">$ {{ number_format($totalExpenses, 2) }}</div>
            </td>
            <td>
                <div class="label">Profit net</div>
                <div class="value {{ $netProfit >= 0 ? 'green' : 'red' }}">{{ number_format($netProfitFc, 0, ',', '.') }} FC</div>
                <div style="font-size:8px;color:#718277;margin-top:3px;">$ {{ number_format($netProfit, 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Compte de résultat</div>
    <table class="balance-sheet">
        <tr>
            <td>Revenus des commandes livrées</td>
            <td class="amount">{{ number_format($totalRevenueFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($totalRevenue, 2) }}</span></td>
        </tr>
        <tr>
            <td>Revenus des abonnements actifs</td>
            <td class="amount">{{ number_format($subscriptionsRevenueFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($subscriptionsRevenue, 2) }}</span></td>
        </tr>
        <tr class="total">
            <td>Total des revenus</td>
            <td class="amount">{{ number_format($totalIncomeFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($totalIncome, 2) }}</span></td>
        </tr>
        <tr>
            <td>Retraits de points payés</td>
            <td class="amount">{{ number_format($totalExpensesFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($withdrawalsPaid, 2) }}</span></td>
        </tr>
        <tr>
            <td>Retraits en attente</td>
            <td class="amount">{{ number_format($withdrawalsPending * ($totalExpenses > 0 ? $totalExpensesFc / $totalExpenses : 0), 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($withdrawalsPending, 2) }}</span></td>
        </tr>
        <tr class="total">
            <td>Total des dépenses</td>
            <td class="amount">{{ number_format($totalExpensesFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($totalExpenses, 2) }}</span></td>
        </tr>
        <tr class="total">
            <td>Résultat net</td>
            <td class="amount" style="color: {{ $netProfit >= 0 ? '#087443' : '#bd2e35' }};">{{ number_format($netProfitFc, 0, ',', '.') }} FC<br><span style="font-size:8px;color:#718277;">$ {{ number_format($netProfit, 2) }}</span></td>
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
