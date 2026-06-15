<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin User', 'email' => 'admin@greenexpress.test', 'phone' => '+10000000000', 'role' => 'admin', 'password' => 'password'],
            ['name' => 'Agent User', 'email' => 'agent@greenexpress.test', 'phone' => '+10000000001', 'role' => 'agent', 'password' => 'password'],
            ['name' => 'Delivery User', 'email' => 'deliverer@greenexpress.test', 'phone' => '+10000000002', 'role' => 'livreur', 'password' => 'password'],
            ['name' => 'Cook User', 'email' => 'cook@greenexpress.test', 'phone' => '+10000000003', 'role' => 'cuisinier', 'password' => 'password'],
            ['name' => 'Client User', 'email' => 'client@greenexpress.test', 'phone' => '+10000000004', 'role' => 'client', 'password' => 'password'],
        ];

        foreach ($accounts as $a) {
            $plain = $a['password'] ?? 'password';
            $a['password'] = Hash::make($plain);

            User::updateOrCreate([
                'email' => $a['email'],
            ], $a);
        }
    }
}
