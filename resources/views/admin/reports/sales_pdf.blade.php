<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport de ventes</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #052e16; font-size: 22px; margin-bottom: 4px; }
        .header .period { color: #64748b; font-size: 11px; }
        .summary { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary td { width: 33%; padding: 14px 12px; border: 1px solid #dbe7df; background: #f8faf9; text-align: center; }
        .summary .label { color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
        .summary .value { font-size: 16px; font-weight: 800; color: #14532d; }
        .details { width: 100%; border-collapse: collapse; border: 1px solid #dbe7df; }
        .details th { background: #f0fdf4; color: #14532d; padding: 8px 10px; font-size: 10px; text-align: left; text-transform: uppercase; letter-spacing: .7px; border-bottom: 1px solid #dbe7df; }
        .details td { padding: 8px 10px; border-bottom: 1px solid #edf4ef; }
        .details tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; background: #dcfce7; color: #166534; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #64748b; border-top: 1px solid #dbe7df; padding-top: 10px; }
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
                <div class="label">Total ventes USD</div>
                <div class="value">$ {{ number_format((float) $totalSales, 2) }}</div>
            </td>
            <td>
                <div class="label">Nombre de commandes</div>
                <div class="value">{{ $ordersCount }}</div>
            </td>
            <td>
                <div class="label">Panier moyen</div>
                <div class="value">$ {{ $ordersCount > 0 ? number_format((float) $totalSales / $ordersCount, 2) : '0.00' }}</div>
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
