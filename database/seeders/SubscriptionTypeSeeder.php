<?php

namespace Database\Seeders;

use App\Models\SubscriptionType;
use Illuminate\Database\Seeder;

class SubscriptionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Hebdomadaire',
                'slug' => 'hebdomadaire',
                'description' => 'Abonnement d\'une semaine avec 7 repas',
                'price' => 25.00,
                'price_fc' => 70000,
                'duration_days' => 7,
                'currency' => 'USD',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Bi-mensuel',
                'slug' => 'bi-mensuel',
                'description' => 'Abonnement de 15 jours avec 15 repas',
                'price' => 50.00,
                'price_fc' => 140000,
                'duration_days' => 15,
                'currency' => 'USD',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Mensuel',
                'slug' => 'mensuel',
                'description' => 'Abonnement d\'un mois avec 30 repas',
                'price' => 90.00,
                'price_fc' => 252000,
                'duration_days' => 30,
                'currency' => 'USD',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Trimestriel',
                'slug' => 'trimestriel',
                'description' => 'Abonnement de 3 mois - meilleur rapport qualité/prix',
                'price' => 240.00,
                'price_fc' => 672000,
                'duration_days' => 90,
                'currency' => 'USD',
                'is_active' => true,
                'display_order' => 4,
            ],
        ];

        foreach ($types as $type) {
            SubscriptionType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }

        echo "Subscription types seeded successfully!\n";
    }
}
