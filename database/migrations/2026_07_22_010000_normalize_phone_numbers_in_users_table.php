<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('id')
            ->chunk(200, function ($users) {
                $seen = [];
                foreach ($users as $user) {
                    $normalized = preg_replace('/[^0-9+]/', '', $user->phone);

                    if ($normalized === '' || $normalized === $user->phone) {
                        continue;
                    }

                    if (in_array($normalized, $seen, true)) {
                        $normalized = $normalized.'_dup_'.$user->id;
                    }

                    $seen[] = $normalized;

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['phone' => $normalized]);
                }
            });
    }

    public function down(): void
    {
        // Irreversible data transformation
    }
};
