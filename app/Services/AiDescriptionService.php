<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiDescriptionService
{
    public function generateDescription(string $mealName, ?string $category = null): string
    {
        $apiKey = config('services.groq.api_key');

        if (! $apiKey) {
            throw new RuntimeException('Clé API Groq non configurée. Définissez GROQ_API_KEY dans le fichier .env.');
        }

        $prompt = $this->buildPrompt($mealName, $category);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un chef cuisinier rédigeant des descriptions de plats appétissantes et professionnelles pour un service de livraison de repas au Congo (Kolwezi). Réponds uniquement avec la description, sans guillemets ni préfixe.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 150,
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

            $description = trim($response->json('choices.0.message.content') ?? '');

            if (! $description) {
                throw new RuntimeException('La réponse de l\'IA est vide. Veuillez réessayer.');
            }

            return $description;
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('AI description generation failed', [
                'meal' => $mealName,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Impossible de générer la description. Veuillez réessayer ou rédiger manuellement.');
        }
    }

    private function buildPrompt(string $mealName, ?string $category): string
    {
        $context = $category ? " (catégorie: {$category})" : '';

        return "Rédige une description courte et appétissante (1 à 2 phrases, maximum 200 caractères) pour le plat suivant : « {$mealName} »{$context}. Mets en valeur les ingrédients, le goût et l'aspect visuel. Style chaleureux et professionnel.";
    }
}
