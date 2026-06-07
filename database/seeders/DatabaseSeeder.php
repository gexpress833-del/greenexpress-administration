<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Commission;
use App\Models\Delivery;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Green Express',
            'email' => 'admin@greenexpress.com',
            'phone' => '+243000000001',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
        ]);

        // Agent
        User::create([
            'name' => 'Agent Commercial',
            'email' => 'agent@greenexpress.com',
            'phone' => '+243000000002',
            'role' => 'agent',
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
        ]);

        // Livreur
        User::create([
            'name' => 'Livreur Principal',
            'email' => 'livreur@greenexpress.com',
            'phone' => '+243000000003',
            'role' => 'livreur',
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
        ]);

        // Client
        User::create([
            'name' => 'Client Abonné',
            'email' => 'client@greenexpress.com',
            'phone' => '+243000000004',
            'address' => 'Kolwezi, Gombe',
            'role' => 'client',
            'password' => Hash::make('password'),
            'password_changed_at' => now(),
        ]);

        // Categories
        $categories = [
            ['name' => 'Plats Principaux', 'slug' => 'plats-principaux'],
            ['name' => 'Entrées', 'slug' => 'entrees'],
            ['name' => 'Desserts', 'slug' => 'desserts'],
            ['name' => 'Boissons', 'slug' => 'boissons'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Meals
        $rate = 2800;

        Meal::create([
            'name' => 'Poulet Rôti',
            'description' => 'Poulet rôti avec légumes de saison',
            'price' => 15.00,
            'price_fc' => 15.00 * $rate,
            'category_id' => 1,
            'status' => 'available',
        ]);

        Meal::create([
            'name' => 'Salade César',
            'description' => 'Salade fraîche avec poulet grillé',
            'price' => 8.00,
            'price_fc' => 8.00 * $rate,
            'category_id' => 2,
            'status' => 'available',
        ]);

        Meal::create([
            'name' => 'Tiramisu',
            'description' => 'Dessert italien classique',
            'price' => 6.00,
            'price_fc' => 6.00 * $rate,
            'category_id' => 3,
            'status' => 'available',
        ]);

        Meal::create([
            'name' => 'Jus d\'Orange',
            'description' => 'Jus frais pressé',
            'price' => 3.00,
            'price_fc' => 3.00 * $rate,
            'category_id' => 4,
            'status' => 'available',
        ]);

        $agent = User::where('role', 'agent')->first();
        $client = User::where('role', 'client')->first();
        $livreur = User::where('role', 'livreur')->first();

        // Orders
        $order1 = Order::create([
            'code' => 'GX-TEST001',
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_phone' => $client->phone,
            'delivery_address' => $client->address,
            'delivery_date' => now()->addDay(),
            'currency' => 'usd',
            'status' => 'pending',
            'total_amount' => 0,
            'total_amount_fc' => 0,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'meal_id' => 1,
            'quantity' => 2,
            'unit_price' => 15.00,
            'unit_price_fc' => 15.00 * $rate,
            'total_price' => 30.00,
            'total_price_fc' => 30.00 * $rate,
        ]);
        $order1->total_amount = 30.00;
        $order1->total_amount_fc = 30.00 * $rate;
        $order1->save();

        $order2 = Order::create([
            'code' => 'GX-TEST002',
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_phone' => $client->phone,
            'delivery_address' => $client->address,
            'delivery_date' => now(),
            'currency' => 'usd',
            'status' => 'delivered',
            'total_amount' => 0,
            'total_amount_fc' => 0,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'meal_id' => 2,
            'quantity' => 1,
            'unit_price' => 8.00,
            'unit_price_fc' => 8.00 * $rate,
            'total_price' => 8.00,
            'total_price_fc' => 8.00 * $rate,
        ]);
        $order2->total_amount = 8.00;
        $order2->total_amount_fc = 8.00 * $rate;
        $order2->save();

        // Delivery
        Delivery::create([
            'order_id' => $order2->id,
            'livreur_id' => $livreur->id,
            'delivery_code' => 'DLV-TEST001',
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Subscription
        Subscription::create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'type' => 'weekly',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'total_days' => 7,
            'remaining_days' => 7,
            'price' => 50.00,
            'currency' => 'usd',
            'price_fc' => 50.00 * $rate,
            'status' => 'active',
        ]);

        // Commission
        Commission::create([
            'agent_id' => $agent->id,
            'order_id' => $order1->id,
            'type' => 'points',
            'points' => 12,
            'amount_usd' => 1.50,
            'amount_fc' => 4200,
            'description' => 'Points gagnés pour la commande ' . $order1->code,
        ]);

        // Withdrawal
        Withdrawal::create([
            'agent_id' => $agent->id,
            'amount_usd' => 10.00,
            'amount_fc' => 28000,
            'status' => 'pending',
        ]);
    }
}
