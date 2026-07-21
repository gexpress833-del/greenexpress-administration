<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('users')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get(['id', 'phone']);

        foreach ($users as $user) {
            $normalized = preg_replace('/[^0-9+]/', '', $user->phone);

            if ($normalized !== $user->phone) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['phone' => $normalized]);
            }
        }
    }

    public function down(): void
    {
        // Irreversible data transformation
    }
};
