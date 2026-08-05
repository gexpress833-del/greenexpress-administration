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
            "Un nouveau repas '{$meal->name}' a été ajouté au menu au prix de ".number_format((float) $meal->price_fc, 0, ',', '.').' FC ($'.number_format((float) $meal->price, 2).')',
            'meal',
            Meal::class,
            $meal->id
        );
    }

    public function updated(Meal $meal): void
    {
        if ($meal->wasChanged('price') || $meal->wasChanged('price_fc') || $meal->wasChanged('name') || $meal->wasChanged('description')) {
            NotificationService::notifyAllUsers(
                'Menu mis à jour',
                "Le repas '{$meal->name}' a été modifié. Prix actuel : ".number_format((float) $meal->price_fc, 0, ',', '.').' FC ($'.number_format((float) $meal->price, 2).')',
                'meal',
                Meal::class,
                $meal->id
            );
        }
    }
}
