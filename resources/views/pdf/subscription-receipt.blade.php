<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu d'abonnement</title>
    <style>
        @page { margin: 24px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; line-height: 1.45; background: #ffffff; }
        .page { border: 1px solid #dbe7df; padding: 0; min-height: 100%; position: relative; }
        .hero { background: #052e16; color: #ffffff; padding: 24px 28px 22px; position: relative; }
        .hero-table { width: 100%; border-collapse: collapse; }
        .hero-table td { vertical-align: top; }
        .brand { font-size: 25px; font-weight: 800; letter-spacing: .4px; color: #ffffff; margin-bottom: 4px; }
        .brand-accent { color: #86efac; }
        .tagline { color: #bbf7d0; font-size: 10px; text-transform: uppercase; letter-spacing: 1.6px; }
        .receipt-box { text-align: right; }
        .receipt-label { color: #bbf7d0; font-size: 9px; text-transform: uppercase; letter-spacing: 1.2px; }
        .receipt-number { font-size: 19px; font-weight: 800; margin-top: 3px; }
        .receipt-date { margin-top: 6px; color: #dcfce7; font-size: 10px; }
        .summary { width: 100%; border-collapse: collapse; margin: 18px 0; }
        .summary td { width: 25%; padding: 14px 12px; border-right: 1px solid #dbe7df; background: #f8faf9; vertical-align: top; }
        .summary td:last-child { border-right: none; }
        .summary-label { color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
        .summary-value { color: #0f172a; font-size: 13px; font-weight: 800; }
        .content { padding: 0 28px 24px; }
        .section { margin-top: 16px; }
        .section-title { font-size: 12px; font-weight: 800; color: #14532d; text-transform: uppercase; letter-spacing: .9px; padding-bottom: 7px; border-bottom: 1px solid #dbe7df; margin-bottom: 10px; }
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col > tbody > tr > td { width: 50%; vertical-align: top; }
        .two-col > tbody > tr > td:first-child { padding-right: 8px; }
        .two-col > tbody > tr > td:last-child { padding-left: 8px; }
        .card { border: 1px solid #dbe7df; background: #ffffff; padding: 14px; min-height: 110px; }
        .row { width: 100%; border-collapse: collapse; }
        .row td { padding: 5px 0; vertical-align: top; }
        .row .label { width: 40%; color: #64748b; font-size: 10px; }
        .row .value { width: 60%; color: #0f172a; font-weight: 700; text-align: right; }
        .details { width: 100%; border-collapse: collapse; border: 1px solid #dbe7df; }
        .details th { background: #f0fdf4; color: #14532d; padding: 9px 10px; font-size: 10px; text-align: left; text-transform: uppercase; letter-spacing: .7px; border-bottom: 1px solid #dbe7df; }
        .details td { padding: 10px; border-bottom: 1px solid #edf4ef; }
        .details tr:last-child td { border-bottom: none; }
        .amount { font-size: 20px; color: #14532d; font-weight: 900; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; background: #dcfce7; color: #166534; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .credentials { border: 1px solid #16a34a; background: #f0fdf4; padding: 16px; margin-top: 16px; }
        .credentials-title { font-size: 13px; font-weight: 900; color: #14532d; margin-bottom: 9px; text-transform: uppercase; letter-spacing: .8px; }
        .credential-table { width: 100%; border-collapse: collapse; }
        .credential-table td { padding: 10px; border: 1px solid #bbf7d0; background: #ffffff; }
        .credential-table .label { width: 32%; color: #64748b; font-weight: 700; }
        .credential-table .value { font-size: 13px; font-weight: 900; color: #052e16; }
        .warning { border-left: 4px solid #f59e0b; background: #fffbeb; padding: 11px 13px; margin-top: 12px; color: #92400e; font-size: 10px; }
        .security { border-left: 4px solid #16a34a; background: #f0fdf4; padding: 11px 13px; margin-top: 12px; color: #14532d; font-size: 10px; }
        .signature { width: 100%; border-collapse: collapse; margin-top: 28px; }
        .signature td { width: 50%; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #94a3b8; padding-top: 6px; width: 180px; color: #64748b; font-size: 10px; }
        .right { text-align: right; }
        .right .signature-line { margin-left: auto; }
        .footer { background: #f8faf9; border-top: 1px solid #dbe7df; padding: 12px 28px; color: #64748b; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="page">
        <div class="hero">
            <table class="hero-table">
                <tr>
                    <td>
                        <div class="brand">Green <span class="brand-accent">Express</span></div>
                        <div class="tagline">Service premium de repas et abonnements</div>
                    </td>
                    <td class="receipt-box">
                        <div class="receipt-label">Reçu officiel</div>
                        <div class="receipt-number">ABO-{{ str_pad((string) $subscription->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="receipt-date">Émis le {{ now()->format('d/m/Y à H:i') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="summary-label">Montant USD</div>
                    <div class="summary-value">$ {{ number_format((float) $subscription->price, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Montant FC</div>
                    <div class="summary-value">{{ number_format((float) $subscription->price_fc, 0, ',', '.') }} FC</div>
                </td>
                <td>
                    <div class="summary-label">Période</div>
                    <div class="summary-value">{{ $subscription->total_days }} jours</div>
                </td>
                <td>
                    <div class="summary-label">Statut</div>
                    <div class="summary-value"><span class="badge">{{ ucfirst($subscription->status) }}</span></div>
                </td>
            </tr>
        </table>

        <div class="content">
            <div class="section">
                <div class="section-title">Détails de l'abonnement</div>
                <table class="details">
                    <thead>
                        <tr>
                            <th>Type d'abonnement</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Total payé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ $subscription->subscriptionType?->name ?? $subscription->type_label }}</strong></td>
                            <td>{{ $subscription->start_date?->format('d/m/Y') }}</td>
                            <td>{{ $subscription->end_date?->format('d/m/Y') }}</td>
                            <td><span class="amount">$ {{ number_format((float) $subscription->price, 2) }}</span><br>{{ number_format((float) $subscription->price_fc, 0, ',', '.') }} FC</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <table class="two-col">
                    <tr>
                        <td>
                            <div class="section-title">Client</div>
                            <div class="card">
                                <table class="row">
                                    <tr><td class="label">Nom complet</td><td class="value">{{ $client->name }}</td></tr>
                                    <tr><td class="label">Téléphone</td><td class="value">{{ $client->phone }}</td></tr>
                                    <tr><td class="label">Email</td><td class="value">{{ $client->email }}</td></tr>
                                </table>
                            </div>
                        </td>
                        <td>
                            <div class="section-title">Agent commercial</div>
                            <div class="card">
                                <table class="row">
                                    <tr><td class="label">Nom</td><td class="value">{{ $subscription->agent?->name }}</td></tr>
                                    <tr><td class="label">Email</td><td class="value">{{ $subscription->agent?->email }}</td></tr>
                                    <tr><td class="label">Référence</td><td class="value">AG-{{ str_pad((string) $subscription->agent_id, 4, '0', STR_PAD_LEFT) }}</td></tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            @if($temporaryPassword)
                <div class="credentials">
                    <div class="credentials-title">Accès client sécurisé</div>
                    <table class="credential-table">
                        <tr>
                            <td class="label">Identifiant de connexion</td>
                            <td class="value">{{ $client->email }}</td>
                        </tr>
                        <tr>
                            <td class="label">Mot de passe temporaire</td>
                            <td class="value">{{ $temporaryPassword }}</td>
                        </tr>
                    </table>
                </div>

                <div class="warning">
                    Ce mot de passe est temporaire et confidentiel. Le client doit le modifier dès sa première connexion avant toute utilisation complète du compte.
                </div>
            @else
                <div class="security">
                    Les identifiants ont déjà été générés. Pour des raisons de sécurité, ce duplicata ne réaffiche pas le mot de passe temporaire.
                </div>
            @endif

        </div>

        <div class="footer">
            Green Express — Reçu généré automatiquement. Merci de conserver ce document comme preuve de paiement.
        </div>
    </div>
</body>
</html>
