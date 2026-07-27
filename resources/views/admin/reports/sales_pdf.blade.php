<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport de ventes</title>
    <style>
        @page { margin: 28px 30px 34px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 10px; line-height: 1.45; }
        .header { background: #073b2a; color: #fff; padding: 20px 22px 18px; margin-bottom: 22px; border-bottom: 4px solid #d4af37; }
        .header h1 { color: #fff; font-size: 21px; font-weight: 800; letter-spacing: .4px; margin-bottom: 5px; }
        .header .period { color: #bde8d0; font-size: 10px; }
        .summary { width: 100%; margin-bottom: 22px; border-collapse: separate; border-spacing: 6px 0; }
        .summary td { width: 33%; padding: 14px 12px; border: 1px solid #d8e5dd; border-top: 3px solid #168a58; background: #f7fbf8; text-align: center; }
        .summary .label { color: #607267; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
        .summary .value { font-size: 16px; font-weight: 800; color: #073b2a; }
        .details { width: 100%; border-collapse: collapse; border: 1px solid #d8e5dd; }
        .details th { background: #073b2a; color: #fff; padding: 8px 10px; font-size: 8px; text-align: left; text-transform: uppercase; letter-spacing: .7px; }
        .details td { padding: 8px 10px; border-bottom: 1px solid #e8f0eb; }
        .details tr:nth-child(even) td { background: #fbfdfb; }
        .details tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; background: #d9f4e4; color: #086b3e; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .footer { margin-top: 22px; text-align: center; font-size: 8px; color: #718277; border-top: 1px solid #d8e5dd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Green Express — Rapport de ventes</h1>
        <div class="period">{{ $label }} : {{ $start->format('d/m/Y H:i') }} — {{ $end->format('d/m/Y H:i') }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total ventes</div>
                <div class="value">{{ number_format((float) $totalSalesFc, 0, ',', '.') }} FC</div>
                <div style="font-size:8px;color:#718277;margin-top:3px;">$ {{ number_format((float) $totalSales, 2) }}</div>
            </td>
            <td>
                <div class="label">Nombre de commandes</div>
                <div class="value">{{ $ordersCount }}</div>
            </td>
            <td>
                <div class="label">Panier moyen</div>
                <div class="value">{{ $ordersCount > 0 ? number_format((float) $totalSalesFc / $ordersCount, 0, ',', '.') : '0' }} FC</div>
                <div style="font-size:8px;color:#718277;margin-top:3px;">$ {{ $ordersCount > 0 ? number_format((float) $totalSales / $ordersCount, 2) : '0.00' }}</div>
            </td>
        </tr>
    </table>

    <div>
        <h3 style="color: #14532d; font-size: 12px; text-transform: uppercase; letter-spacing: .9px; margin-bottom: 10px;">Détails des commandes</h3>
        <table class="details">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Agent</th>
                    <th>Statut</th>
                    <th>Montant USD</th>
                    <th>Montant FC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                    <tr>
                        <td>{{ $o->code ?? $o->id }}</td>
                        <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $o->client_name }}</td>
                        <td>{{ $o->agent?->name ?? 'N/A' }}</td>
                        <td><span class="badge">{{ ucfirst($o->status) }}</span></td>
                        <td>$ {{ number_format((float) $o->total_amount, 2) }}</td>
                        <td>{{ number_format((float) $o->total_amount_fc, 0, ',', '.') }} FC</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Green Express — Rapport généré le {{ $generatedAt->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
