<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiService
{
    private ?string $apiKey;

    private string $model;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    public function generateMealDescription(string $mealName, ?string $category = null): string
    {
        $context = $category ? " (catégorie: {$category})" : '';

        $prompt = "Rédige une description courte et appétissante (1 à 2 phrases, maximum 200 caractères) pour le plat suivant : « {$mealName} »{$context}. Mets en valeur les ingrédients, le goût et l'aspect visuel. Style chaleureux et professionnel.";

        return $this->chat(
            'Tu es un chef cuisinier rédigeant des descriptions de plats appétissantes et professionnelles pour un service de livraison de repas au Congo (Kolwezi). Réponds uniquement avec la description, sans guillemets ni préfixe.',
            $prompt,
            150
        );
    }

    public function generateCategoryDescription(string $categoryName): string
    {
        $prompt = "Rédige une description courte (1 à 2 phrases, maximum 200 caractères) pour la catégorie de repas suivante : « {$categoryName} ». Explique brièvement ce que cette catégorie propose. Style professionnel et chaleureux.";

        return $this->chat(
            'Tu es un expert en restauration rédigeant des descriptions de catégories de repas pour un service de livraison au Congo (Kolwezi). Réponds uniquement avec la description, sans guillemets ni préfixe.',
            $prompt,
            150
        );
    }

    public function generateWhatsAppMessage(string $clientName, string $orderCode, string $itemsSummary, string $totalAmount, string $totalAmountFc, string $validationCode, string $deliveryDate, string $currency = 'usd'): string
    {
        $prompt = "Rédige un message WhatsApp de confirmation de commande pour un client. Voici les informations OBLIGATOIRES à inclure dans le message :\n"
            ."- Nom du client : {$clientName}\n"
            ."- Code de commande : {$orderCode}\n"
            ."- Repas commandés : {$itemsSummary}\n"
            ."- Montant total : {$totalAmountFc} (soit {$totalAmount})\n"
            ."- Date de livraison prévue : {$deliveryDate}\n"
            ."- Code de validation client : {$validationCode}\n\n"
            ."Le message DOIT obligatoirement contenir TOUTES ces informations, en particulier le code de validation et la date de livraison. Mets en avant le montant en Francs Congolais (FC). Ajoute un avertissement de ne pas communiquer le code de validation au livreur avant d'avoir reçu la commande. Sois chaleureux, inclus un emoji. Maximum 400 caractères. Ne pas inclure de guillemets.";

        return $this->chat(
            'Tu es un assistant clientèle pour Green Express, un service de livraison de repas à Kolwezi (Congo). Tu rédiges des messages WhatsApp clairs, chaleureux et professionnels. Le Franc Congolais (FC) est la devise principale. Tu dois TOUJOURS inclure toutes les informations fournies, surtout le code de validation client et la date de livraison. Réponds uniquement avec le message, sans guillemets ni préfixe.',
            $prompt,
            300
        );
    }

    public function generateDashboardReport(array $kpi): string
    {
        $data = json_encode($kpi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = "Voici les statistiques d'un service de livraison de repas (Green Express à Kolwezi, Congo) pour la période du {$kpi['period']['start']} au {$kpi['period']['end']} :\n\n{$data}\n\nRédige un résumé analytique court (3 à 5 phrases) en français, mettant en évidence les points clés : chiffre d'affaires, nombre de commandes, tendances, et une recommandation. Style professionnel et direct.";

        return $this->chat(
            'Tu es un analyste business pour un service de livraison de repas. Tu rédiges des résumés clairs et actionnables en français. Réponds uniquement avec le résumé, sans guillemets ni préfixe.',
            $prompt,
            300
        );
    }

    public function generateNotificationMessage(string $context, string $details): string
    {
        $prompt = "Rédige un message de notification court et engageant (maximum 250 caractères) pour une application de livraison de repas. Contexte : {$context}. Détails : {$details}. Le message doit être naturel, motivant et professionnel. Ne pas inclure de guillemets.";

        return $this->chat(
            'Tu es un assistant qui rédige des notifications push engageantes pour une application de livraison de repas au Congo (Kolwezi). Réponds uniquement avec le message, sans guillemets ni préfixe.',
            $prompt,
            200
        );
    }

    public function generateHelpResponse(string $userQuestion, array $context = [], ?string $userName = null, ?string $userRole = null): string
    {
        $nameContext = $userName ? " L'utilisateur s'appelle {$userName}." : '';
        $roleContext = $userRole ? " Son rôle dans l'application est : {$userRole}." : '';

        $dynamicContext = $this->buildHelpContext($context);

        $prompt = "Question de l'utilisateur : {$userQuestion}";

        $system = 'Tu es le Service Client de Green Express, un service de livraison de repas basé à Kolwezi en République Démocratique du Congo.'
            .' Green Express est le NOM DE L\'ENTREPRISE, pas le nom de l\'utilisateur.'
            .' Tu réponds aux questions des utilisateurs avec professionnalisme, clarté et courtoisie.'
            .' Voici les règles et informations VÉRIFIÉES sur le fonctionnement réel de la plateforme Green Express. Tu DOIS te baser UNIQUEMENT sur ces informations pour répondre.'
            ." Si une information n'est pas dans ce contexte, oriente l'utilisateur vers le support WhatsApp sans inventer."
            .' Ne mentionne jamais que tu es une IA, un modèle de langage ou un robot. Tu es un conseiller du Service Client Green Express.'
            .' Réponds en français, de manière concise, utile et structurée.'
            .' IMPORTANT : Ne salue PAS l\'utilisateur à chaque réponse. Un seul "Bonjour" au début de la conversation suffit, ensuite réponds directement à la question sans formule de salutation.'
            ."{$nameContext}{$roleContext}\n\n"
            ."--- CONTEXTE MÉTIER GREEN EXPRESS ---\n"
            ."{$dynamicContext}\n"
            .'--- FIN DU CONTEXTE ---';

        return $this->chat($system, $prompt, 600);
    }

    private function buildHelpContext(array $context): string
    {
        $exchangeRate = $context['exchange_rate'] ?? 'non disponible';
        $subscriptionTypes = $context['subscription_types'] ?? [];
        $userRole = $context['user_role'] ?? 'utilisateur';

        $typesText = empty($subscriptionTypes)
            ? 'Les formules d\'abonnement disponibles sont définies par l\'administrateur.'
            : "Formules d'abonnement actives :\n".collect($subscriptionTypes)
                ->map(static function (array $type): string {
                    $name = $type['name'] ?? 'Formule';
                    $description = $type['description'] ?? 'Abonnement Green Express';
                    $price = $type['price'] ?? 0;
                    $priceFc = $type['price_fc'] ?? 0;
                    $duration = $type['duration_days'] ?? 0;
                    $mealsPerDay = $type['meals_per_day'] ?? 0;

                    return "- {$name} : {$description} | prix USD {$price} | prix FC {$priceFc} | durée {$duration} jours | repas par jour : {$mealsPerDay}";
                })
                ->implode("\n");

        return "RÔLES UTILISATEURS :\n"
            ."- admin : gère les utilisateurs, repas, catégories, commandes, abonnements, livraisons, taux de change, notifications et logs.\n"
            ."- agent : crée des commandes et des abonnements pour les clients, gagne des points et commissions, peut faire des retraits.\n"
            ."- livreur : récupère et livre les commandes, utilise le code de validation client.\n"
            ."- cuisinier : voit les commandes à préparer.\n"
            ."- client : passe des commandes, consulte ses abonnements et son historique.\n\n"
            ."COMMANDES :\n"
            ."- Une commande est créée par un agent avec les informations client (nom, téléphone, adresse, date et heure de livraison).\n"
            ."- Le code de commande commence par GX- (ex: GX-6A64A0397CAF2).\n"
            ."- Le code de validation client est un code alphanumérique de 6 caractères en majuscules.\n"
            ."- Le client ne doit communiquer son code de validation au livreur qu'après avoir reçu sa commande en main propre.\n"
            ."- Statuts d'une commande : pending (en attente), confirmed (confirmée), preparing (en préparation), delivering (en livraison), delivered (livrée), cancelled (annulée).\n"
            ."- Le montant total est stocké en USD (total_amount) et en Francs Congolais (total_amount_fc). La devise affichée principale est le FC.\n"
            ."- Le client peut payer en USD ou en FC selon la devise choisie par l'agent lors de la création.\n"
            ."- Les prix des repas sont définis par l'administrateur avec un prix FC calculé automatiquement via le taux de change.\n\n"
            ."LIVRAISONS :\n"
            ."- Une livraison est créée automatiquement pour chaque commande avec un code DLV-.\n"
            ."- Statuts de livraison : assigned (assignée), picked_up (récupérée), in_transit (en cours), delivered (livrée).\n"
            ."- Le livreur doit valider la livraison avec le code de validation du client.\n\n"
            ."TAUX DE CHANGE :\n"
            ."- Taux actuel : 1 USD = {$exchangeRate} FC.\n"
            ."- Ce taux est mis à jour par l'administrateur et sert à calculer les montants en FC.\n\n"
            ."ABONNEMENTS :\n"
            ."{$typesText}\n"
            ."- Statuts d'abonnement : pending (en attente de validation admin), active (actif), suspended (suspendu), expired (expiré), rejected (rejeté).\n"
            ."- Un abonnement doit être validé par un administrateur pour devenir actif.\n"
            ."- Les repas livrés dans le cadre d'un abonnement suivent le menu défini pour chaque jour de semaine (lundi au vendredi).\n"
            ."- Les suspensions peuvent être demandées pour ajuster les dates de livraison.\n\n"
            ."POINTS ET RÉCOMPENSES (AGENTS) :\n"
            ."- Les agents gagnent des points à chaque fois qu'un client valide une commande (en donnant son code de validation au livreur).\n"
            ."- Les agents gagnent également des points sur les abonnements qu'ils créent.\n"
            ."- Chaque point a une valeur en USD, définie par l'administrateur.\n"
            ."- Les points sont cumulés sur le compte de l'agent et peuvent être convertis en retraits via mobile money.\n"
            ."- Pour faire un retrait, l'agent doit fournir son opérateur mobile money et son numéro de téléphone.\n"
            ."- Statuts de retrait : pending (en attente), approved (approuvé), paid (payé), rejected (rejeté).\n"
            ."- La valeur exacte en points par commande est définie par l'administrateur et peut varier.\n\n"
            ."DEVISES :\n"
            ."- Franc Congolais (FC) est la devise principale affichée. Le dollar USD est affiché comme équivalent secondaire.\n"
            ."- Les agents choisissent la devise (USD ou FC) lors de la création de la commande.\n\n"
            ."UTILISATEUR ACTUEL :\n"
            ."- Rôle : {$userRole}. Adapte ta réponse à ce rôle si la question concerne des fonctionnalités spécifiques.";
    }

    public function chat(string $systemPrompt, string $userPrompt, int $maxTokens = 150): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Clé API Groq non configurée. Définissez GROQ_API_KEY dans le fichier .env.');
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'max_tokens' => $maxTokens,
                    'temperature' => 0.8,
                ]);

            if (! $response->successful()) {
                $apiMessage = $response->json('error.message') ?? $response->body();

                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new RuntimeException('Erreur Groq ('.$response->status().') : '.$apiMessage);
            }

            $content = trim($response->json('choices.0.message.content') ?? '');

            if (! $content) {
                throw new RuntimeException('La réponse de l\'IA est vide. Veuillez réessayer.');
            }

            return $content;
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('AI generation failed', [
                'prompt' => $userPrompt,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Impossible de générer la réponse. Veuillez réessayer.');
        }
    }
}
