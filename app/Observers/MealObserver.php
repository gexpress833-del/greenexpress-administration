<?php

namespace App\Observers;

use App\Models\Meal;
use App\Services\NotificationService;

class MealObserver
{
    public function created(Meal $meal): void
    {
        NotificationService::notifyAllUsers(
            'Nouveau repas disponible',
            "Un nouveau repas '{$meal->name}' a été ajouté au menu au prix de \${$meal->price}",
            'meal',
            Meal::class,
            $meal->id
        );
    }

    public function updated(Meal $meal): void
    {
        if ($meal->wasChanged('price') || $meal->wasChanged('name') || $meal->wasChanged('description')) {
            NotificationService::notifyAllUsers(
                'Menu mis à jour',
                "Le repas '{$meal->name}' a été modifié. Prix actuel: \${$meal->price}",
                'meal',
                Meal::class,
                $meal->id
            );
        }
    }
}
