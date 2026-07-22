<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME', 'Administrateur');
        $email = env('ADMIN_EMAIL', 'admin@greenexpress.cd');
        $password = env('ADMIN_PASSWORD', 'GreenExpress2026!');

        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            $admin->update([
                'name' => $name,
                'email' => $email,
                'is_active' => true,
            ]);
            $this->command->info('Admin user updated successfully!');
        } else {
            User::create([
                'name' => $name,
                'email' => $email,
                'phone' => '+243000000000',
                'role' => 'admin',
                'password' => Hash::make($password),
                'password_changed_at' => null,
                'is_active' => true,
            ]);
            $this->command->info('Admin user created successfully! Please change the password after first login.');
        }
    }
}
