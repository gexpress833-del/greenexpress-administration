<?php

namespace App\Services;

class AiDescriptionService
{
    public function __construct(private AiService $ai) {}

    public function generateDescription(string $mealName, ?string $category = null): string
    {
        return $this->ai->generateMealDescription($mealName, $category);
    }
}
