<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de preparation - {{ $order->code }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #333; line-height: 1.4; }
        .page { padding: 30px; }

        /* Header */
        .header { border-bottom: 3px solid #16a34a; padding-bottom: 15px; margin-bottom: 20px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 60px; }
        .logo-box {
            width: 50px; height: 50px; background: #16a34a; color: #fff;
            text-align: center; line-height: 50px; font-size: 28px;
            border-radius: 8px; font-weight: bold;
        }
        .brand-cell { padding-left: 12px; }
        .brand-name { font-size: 22px; font-weight: bold; color: #16a34a; margin: 0; }
        .brand-tagline { font-size: 11px; color: #666; margin: 2px 0 0; }
        .doc-info { text-align: right; }
        .doc-title { font-size: 14px; font-weight: bold; color: #111; margin: 0; }
        .doc-code { font-size: 12px; color: #666; margin: 3px 0 0; }

        /* Status badge */
        .status-row { margin: 10px 0 20px; text-align: right; }
        .badge {
            display: inline-block; padding: 3px 10px; border: 1px solid;
            font-size: 11px; font-weight: bold; text-transform: uppercase;
        }
        .badge-confirmed { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .badge-preparing { background: #eff6ff; border-color: #3b82f6; color: #1e40af; }
        .badge-delivering { background: #faf5ff; border-color: #a855f7; color: #7e22ce; }

        /* Info boxes using tables */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { width: 50%; vertical-align: top; padding: 0 8px 8px 0; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; }
        .info-label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 4px; }
        .info-value { font-size: 13px; color: #111; font-weight: bold; }
        .info-sub { font-size: 12px; color: #555; margin-top: 2px; }

        /* Items table */
        .items-title { font-size: 14px; font-weight: bold; color: #111; margin: 20px 0 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #666; border-bottom: 1px solid #ddd; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        .items-table td strong { color: #111; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }

        /* Total */
        .total-row { margin-top: 10px; text-align: right; padding: 10px 0; border-top: 2px solid #16a34a; }
        .total-label { font-size: 12px; color: #666; }
        .total-amount { font-size: 18px; font-weight: bold; color: #16a34a; }

        /* QR section */
        .qr-section { text-align: center; margin: 25px auto; padding: 15px; border: 2px solid #16a34a; max-width: 260px; background: #f0fdf4; }
        .qr-logo-text { font-size: 16px; font-weight: bold; color: #16a34a; margin-bottom: 3px; }
        .qr-subtitle { font-size: 10px; color: #166534; margin-bottom: 10px; text-transform: uppercase; }
        .qr-image { text-align: center; margin: 0 auto 8px; }
        .qr-code-text { font-size: 13px; font-weight: bold; color: #111; font-family: "Courier New", monospace; margin-top: 5px; }
        .qr-verify { font-size: 9px; color: #666; margin-top: 6px; }

        /* Footer */
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px dashed #bbb; text-align: center; font-size: 10px; color: #888; }
        .footer p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="page">

        <!-- Header with logo -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <div class="logo-box">G</div>
                    </td>
                    <td class="brand-cell">
                        <p class="brand-name">Green Express</p>
                        <p class="brand-tagline">Repas chaud, livraison rapide</p>
                    </td>
                    <td class="doc-info">
                        <p class="doc-title">Bon de preparation</p>
                        <p class="doc-code">{{ $order->code }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Status -->
        <div class="status-row">
            <span class="badge badge-{{ $order->status }}">
                @if($order->status === 'confirmed') A PREPARER @endif
                @if($order->status === 'preparing') EN PREPARATION @endif
                @if($order->status === 'delivering') PRETE @endif
            </span>
        </div>

        <!-- Info boxes -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Client</div>
                        <div class="info-value">{{ $order->client_name }}</div>
                        <div class="info-sub">{{ $order->client_phone }}</div>
                    </div>
                </td>
                <td>
                    <div class="info-box">
                        <div class="info-label">Adresse de livraison</div>
                        <div class="info-value">{{ $order->delivery_address }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Date de livraison</div>
                        <div class="info-value">{{ $order->delivery_date?->format('d/m/Y') ?? 'Non definie' }}</div>
                    </div>
                </td>
                <td>
                    <div class="info-box">
                        <div class="info-label">Agent</div>
                        <div class="info-value">{{ $order->agent->name ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <div class="items-title">Repas commandes</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Repas</th>
                    <th class="text-center">Qte</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td><strong>{{ $item->meal->name }}</strong></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">$ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right"><strong>$ {{ number_format($item->total_price, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-row">
            <span class="total-label">Total : </span>
            <span class="total-amount">$ {{ number_format($order->total_amount, 2) }}</span>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
            <div class="qr-logo-text">Green Express</div>
            <div class="qr-subtitle">Bon de preparation</div>
            <div class="qr-image">
                <img src="{{ $qrCodePng }}" alt="QR Code" width="128" height="128" />
            </div>
            <div class="qr-code-text">{{ $order->code }}</div>
            <div class="qr-verify">Scannez pour verifier l'authenticite — green-express.cd</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Commande passee le {{ $order->created_at->format('d/m/Y a H:i') }}</p>
            <p>Green Express — Repas chaud, livraison rapide</p>
        </div>

    </div>
</body>
</html>
