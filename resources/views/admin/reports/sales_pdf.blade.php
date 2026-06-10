<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport de ventes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; }
        .header { text-align: center; margin-bottom: 20px }
        .summary { margin-bottom: 20px }
        .summary td { padding: 6px 12px }
        .footer { position: fixed; bottom: 0; font-size: 11px; color: #666 }
    </style>
</head>
<body>
    <div class="header">
        <h1>Green Express — Rapport de ventes</h1>
        <div>{{ $label }}: {{ $start->toDayDateTimeString() }} — {{ $end->toDayDateTimeString() }}</div>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total ventes</strong></td>
            <td>{{ number_format($totalSales, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Nombre de commandes</strong></td>
            <td>{{ $ordersCount }}</td>
        </tr>
    </table>

    <div>
        <h3>Détails</h3>
        <table width="100%" border="1" cellspacing="0" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach(App\Models\Order::whereBetween('created_at', [$start, $end])->get() as $o)
                    <tr>
                        <td>{{ $o->id }}</td>
                        <td>{{ $o->created_at->toDateTimeString() }}</td>
                        <td>{{ number_format($o->total ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">Généré le {{ $generatedAt->toDayDateTimeString() }}</div>
</body>
</html>
