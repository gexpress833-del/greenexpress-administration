<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu d'abonnement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { border-bottom: 3px solid #16a34a; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 24px; font-weight: 700; color: #15803d; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 16px 0 10px; }
        .grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .grid td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: top; }
        .grid .label { width: 34%; background: #f9fafb; font-weight: 700; color: #374151; }
        .credentials { border: 2px solid #16a34a; background: #ecfdf5; padding: 14px; margin-top: 14px; }
        .warning { border: 1px solid #f59e0b; background: #fffbeb; padding: 10px; margin-top: 12px; color: #92400e; }
        .footer { margin-top: 24px; font-size: 10px; color: #6b7280; text-align: center; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; background: #dcfce7; color: #166534; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Green Express</div>
        <div class="muted">Reçu de paiement et identifiants client</div>
        <div class="muted">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    <div class="title">Informations de l'abonnement</div>
    <table class="grid">
        <tr>
            <td class="label">N° reçu</td>
            <td>ABO-{{ str_pad((string) $subscription->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label">Statut</td>
            <td><span class="badge">{{ ucfirst($subscription->status) }}</span></td>
        </tr>
        <tr>
            <td class="label">Type</td>
            <td>{{ $subscription->subscriptionType?->name ?? $subscription->type_label }}</td>
        </tr>
        <tr>
            <td class="label">Période</td>
            <td>Du {{ $subscription->start_date?->format('d/m/Y') }} au {{ $subscription->end_date?->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Durée</td>
            <td>{{ $subscription->total_days }} jours ouvrables</td>
        </tr>
        <tr>
            <td class="label">Montant payé</td>
            <td>$ {{ number_format((float) $subscription->price, 2) }} / {{ number_format((float) $subscription->price_fc, 0, ',', '.') }} FC</td>
        </tr>
    </table>

    <div class="title">Client</div>
    <table class="grid">
        <tr>
            <td class="label">Nom</td>
            <td>{{ $client->name }}</td>
        </tr>
        <tr>
            <td class="label">Téléphone</td>
            <td>{{ $client->phone }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $client->email }}</td>
        </tr>
    </table>

    <div class="title">Agent</div>
    <table class="grid">
        <tr>
            <td class="label">Agent</td>
            <td>{{ $subscription->agent?->name }}</td>
        </tr>
        <tr>
            <td class="label">Email agent</td>
            <td>{{ $subscription->agent?->email }}</td>
        </tr>
    </table>

    <div class="credentials">
        <div class="title" style="margin-top: 0;">Identifiants de connexion client</div>
        <table class="grid" style="margin-bottom: 0;">
            <tr>
                <td class="label">Identifiant</td>
                <td>{{ $client->email }}</td>
            </tr>
            <tr>
                <td class="label">Mot de passe temporaire</td>
                <td><strong>{{ $temporaryPassword }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="warning">
        Ce mot de passe est temporaire. Le client doit le changer à sa première connexion pour sécuriser son compte.
    </div>

    <div class="footer">
        Green Express vous remercie pour votre confiance.
    </div>
</body>
</html>
