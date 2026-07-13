<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Construit le message d'identifiants pré-rempli pour un nouveau client.
     */
    public function credentialsMessage(string $email, string $password): string
    {
        return "Bonjour,\n\n"
            ."Bienvenue sur Green Express. Votre compte a été créé avec succès.\n\n"
            ."Identifiant : {$email}\n"
            ."Mot de passe temporaire : {$password}\n\n"
            ."Pour votre sécurité, veuillez modifier ce mot de passe dès votre première connexion.\n\n"
            .'Green Express vous remercie.';
    }

    /**
     * Génère le lien WhatsApp "click-to-chat" (wa.me) avec les identifiants
     * pré-remplis. L'agent n'a plus qu'à cliquer puis envoyer le message.
     */
    public function credentialsLink(string $phone, string $email, string $password): string
    {
        return $this->link($phone, $this->credentialsMessage($email, $password));
    }

    /**
     * Génère un lien WhatsApp "click-to-chat" avec message pré-rempli.
     *
     * Aucune API externe : on s'appuie uniquement sur le protocole wa.me
     * officiel de WhatsApp qui ouvre la conversation avec le texte préparé.
     */
    public function link(string $phone, string $message): string
    {
        $number = $this->normalizePhone($phone);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    /**
     * Message de confirmation de commande pour un client.
     */
    public function orderConfirmationMessage(string $clientName, string $orderCode, float $total, string $deliveryDate, ?string $validationCode = null): string
    {
        $msg = "Bonjour {$clientName},\n\n"
            ."Votre commande {$orderCode} a bien été enregistrée par Green Express.\n"
            .'Montant total : $'.number_format($total, 2)."\n"
            ."Date de livraison prévue : {$deliveryDate}.\n\n";

        if ($validationCode) {
            $msg .= "Code de validation client : {$validationCode}\n"
                ."Important : ne communiquez ce code au livreur qu'après avoir reçu votre commande en main propre.\n\n";
        }

        $msg .= "Merci pour votre confiance.\nGreen Express";

        return $msg;
    }

    /**
     * Lien WhatsApp de confirmation de commande.
     */
    public function orderConfirmationLink(string $phone, string $clientName, string $orderCode, float $total, string $deliveryDate, ?string $validationCode = null): string
    {
        return $this->link($phone, $this->orderConfirmationMessage($clientName, $orderCode, $total, $deliveryDate, $validationCode));
    }

    /**
     * Message du livreur au client (en route).
     */
    public function deliveryOnTheWayMessage(string $clientName, string $orderCode): string
    {
        return "Bonjour {$clientName},\n\n"
            ."Votre commande Green Express {$orderCode} est en cours de livraison.\n"
            ."Merci de préparer votre code de validation client.\n\n"
            ."Important : remettez ce code uniquement après réception de votre commande.\n"
            .'À tout de suite.';
    }

    /**
     * Lien WhatsApp "en route" pour le livreur.
     */
    public function deliveryOnTheWayLink(string $phone, string $clientName, string $orderCode): string
    {
        return $this->link($phone, $this->deliveryOnTheWayMessage($clientName, $orderCode));
    }

    /**
     * Message de retrait approuvé pour un agent.
     */
    public function withdrawalApprovedMessage(float $amountUsd): string
    {
        return "Bonjour,\n\n"
            .'Votre demande de retrait de $'.number_format($amountUsd, 2)." a été approuvée.\n"
            ."L'administration vous contactera pour la remise des fonds.\n\n"
            ."Merci pour votre travail.\nGreen Express";
    }

    /**
     * Lien WhatsApp de retrait approuvé.
     */
    public function withdrawalApprovedLink(string $phone, float $amountUsd): string
    {
        return $this->link($phone, $this->withdrawalApprovedMessage($amountUsd));
    }

    /**
     * Message d'activation d'abonnement pour un client.
     */
    public function subscriptionActivatedMessage(string $clientName, string $type, string $endDate): string
    {
        $typeLabel = $type === 'weekly' ? 'hebdomadaire' : 'mensuel';

        return "Bonjour {$clientName},\n\n"
            ."Votre abonnement {$typeLabel} Green Express a été activé.\n"
            ."Vos livraisons démarrent selon le planning prévu.\n"
            ."Date de fin prévue : {$endDate}.\n\n"
            ."Merci pour votre confiance.\nGreen Express";
    }

    /**
     * Lien WhatsApp d'activation d'abonnement.
     */
    public function subscriptionActivatedLink(string $phone, string $clientName, string $type, string $endDate): string
    {
        return $this->link($phone, $this->subscriptionActivatedMessage($clientName, $type, $endDate));
    }

    /**
     * Message de crédit de commission pour un agent.
     */
    public function commissionCreditedMessage(string $orderCode, float $amountUsd, string $type): string
    {
        $typeLabels = ['points' => 'Points', 'bonus_meal' => 'Bonus repas', 'commission_5' => 'Commission 5%', 'commission_10' => 'Commission 10%', 'daily_commission' => 'Commission journalière'];
        $label = $typeLabels[$type] ?? $type;

        return "Bonjour,\n\n"
            ."Une progression liée à la commande {$orderCode} a été enregistrée sur votre compte agent.\n"
            ."Type : {$label}\n"
            .'Montant estimé : $'.number_format($amountUsd, 2)."\n\n"
            ."Le suivi définitif reste disponible dans votre espace agent.\n"
            ."Merci pour votre travail.\nGreen Express";
    }

    /**
     * Lien WhatsApp de crédit de commission.
     */
    public function commissionCreditedLink(string $phone, string $orderCode, float $amountUsd, string $type): string
    {
        return $this->link($phone, $this->commissionCreditedMessage($orderCode, $amountUsd, $type));
    }

    /**
     * Nettoie un numéro de téléphone pour le format wa.me (chiffres uniquement).
     */
    public function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
