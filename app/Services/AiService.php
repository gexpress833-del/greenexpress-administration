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

    public function generateWhatsAppMessage(string $clientName, string $orderCode, string $itemsSummary, string $totalAmount, string $totalAmountFc, string $currency = 'usd'): string
    {
        $primaryAmount = $currency === 'fc' ? $totalAmountFc : $totalAmountFc;
        $secondaryAmount = $totalAmount;

        $prompt = "Rédige un message WhatsApp court et chaleureux (maximum 300 caractères) pour informer un client que sa commande a été enregistrée. Client : {$clientName}. Code de commande : {$orderCode}. Repas commandés : {$itemsSummary}. Montant total : {$primaryAmount} (soit {$secondaryAmount}). Date de livraison prévue. Le message doit mettre en avant le montant en Francs Congolais (FC) comme devise principale, inclure un emoji, mentionner le code de commande. Ne pas inclure de guillemets.";

        return $this->chat(
            'Tu es un assistant clientèle pour Green Express, un service de livraison de repas à Kolwezi (Congo). Tu rédiges des messages WhatsApp courts, chaleureux et professionnels. Le Franc Congolais (FC) est la devise principale. Réponds uniquement avec le message, sans guillemets ni préfixe.',
            $prompt,
            200
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
