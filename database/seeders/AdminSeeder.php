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
                'password' => Hash::make($password),
                'is_active' => true,
            ]);
            echo "Admin user updated successfully!\n";
        } else {
            User::create([
                'name' => $name,
                'email' => $email,
                'phone' => '+243000000000',
                'role' => 'admin',
                'password' => Hash::make($password),
                'is_active' => true,
            ]);
            echo "Admin user created successfully!\n";
        }

        echo "Email: {$email}\n";
        echo "Password: {$password}\n";
        echo "Please change the default password after first login.\n";
    }
}
