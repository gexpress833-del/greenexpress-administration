<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu {{ $order->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.4; }

        .receipt { max-width: 500px; margin: 0 auto; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; }

        .header {
            background: #15803d;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .header img { height: 50px; margin-bottom: 6px; }
        .header h1 { font-size: 26px; font-weight: 800; letter-spacing: 1px; margin-bottom: 4px; }
        .header p { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; }

        .body { padding: 20px; background: #fff; }

        .info-table { width: 100%; margin-bottom: 16px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table td:first-child { color: #6b7280; width: 40%; }
        .info-table td:last-child { font-weight: 600; text-align: right; color: #111827; }
        .info-table .validation td:last-child {
            font-size: 16px; letter-spacing: 3px; color: #d97706; font-weight: 800;
        }

        .divider { border: none; border-top: 2px dashed #e5e7eb; margin: 14px 0; }

        .section-title {
            font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px;
            color: #9ca3af; font-weight: 700; margin-bottom: 8px;
        }

        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .item-table tr { border-bottom: 1px solid #f3f4f6; }
        .item-table td { padding: 6px 0; vertical-align: top; }
        .item-table td:last-child { text-align: right; }
        .item-name { font-weight: 600; color: #1f2937; }
        .item-qty { color: #9ca3af; font-size: 11px; }
        .item-price { font-weight: 700; color: #1f2937; }
        .item-fc { font-size: 10px; color: #6b7280; }

        .total-box {
            border-top: 2px solid #15803d;
            padding-top: 10px;
            margin-top: 10px;
        }
        .total-table { width: 100%; }
        .total-table td { padding: 2px 0; }
        .total-table td:first-child { font-size: 14px; font-weight: 700; color: #1f2937; }
        .total-table td:last-child { text-align: right; }
        .total-usd { font-size: 18px; font-weight: 800; color: #15803d; }
        .total-fc { font-size: 11px; color: #4b5563; font-weight: 600; }

        .qr-box {
            text-align: center;
            margin-top: 18px;
            padding: 12px;
            border: 2px solid #dcfce7;
            border-radius: 8px;
            display: inline-block;
            width: 100%;
        }
        .qr-box img { display: block; margin: 0 auto; }
        .qr-label { font-size: 10px; color: #9ca3af; margin-top: 6px; }

        .footer {
            background: #f9fafb;
            text-align: center;
            padding: 12px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p { font-size: 10px; color: #6b7280; }
        .footer .brand { color: #15803d; font-weight: 700; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if(!empty($logoData))
                <img src="{{ $logoData }}" alt="Green Express">
            @else
                <h1>Green Express</h1>
            @endif
            <p>Reçu de commande</p>
        </div>

        <div class="body">
            <table class="info-table">
                <tr><td>N° Reçu</td><td>{{ $order->code }}</td></tr>
                <tr><td>Date</td><td>{{ $order->created_at?->format('d/m/Y H:i') }}</td></tr>
                @if($order->delivery_date)
                <tr><td>Date de livraison</td><td>{{ $order->delivery_date?->format('d/m/Y') }}</td></tr>
                @endif
                <tr><td>Client</td><td>{{ $order->client_name }}</td></tr>
                <tr><td>Téléphone</td><td>{{ $order->client_phone }}</td></tr>
                <tr><td>Adresse</td><td style="font-size:11px;">{{ $order->delivery_address }}</td></tr>
                <tr><td>Agent</td><td>{{ $order->agent->name }}</td></tr>
                @if($order->subscription_id)
                <tr><td>Abonnement</td><td>{{ $order->subscription?->subscriptionType?->name ?? $order->subscription?->type_label ?? 'N/A' }}</td></tr>
                @endif
                <tr class="validation"><td>Code validation</td><td>{{ $order->client_validation_code }}</td></tr>
            </table>

            <hr class="divider">

            <div class="section-title">Détails de la commande</div>
            <table class="item-table">
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ $item->meal->name }}</span>
                        <span class="item-qty">x{{ $item->quantity }}</span>
                    </td>
                    <td>
                        <div class="item-price">$ {{ number_format($item->total_price, 2) }}</div>
                        <div class="item-fc">{{ number_format($item->total_price_fc, 0, ',', '.') }} FC</div>
                    </td>
                </tr>
                @endforeach
            </table>

            <div class="total-box">
                <table class="total-table">
                    <tr>
                        <td>Total payé</td>
                        <td>
                            <div class="total-usd">$ {{ number_format($order->total_amount, 2) }}</div>
                            <div class="total-fc">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="text-align:center;">
                <div class="qr-box">
                    <img src="{{ $qrCode }}" alt="QR Code" width="110" height="110">
                    <div class="qr-label">Scannez pour valider la livraison</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Merci pour votre confiance</p>
            <p class="brand">Green Express - Livraison de repas à Kolwezi</p>
        </div>
    </div>
</body>
</html>
