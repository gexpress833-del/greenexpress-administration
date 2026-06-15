<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'weekly' => 'hebdomadaire',
            'monthly' => 'mensuel',
        ];

        foreach ($mapping as $oldType => $slug) {
            $typeId = DB::table('subscription_types')->where('slug', $slug)->value('id');
            if ($typeId) {
                DB::table('subscriptions')
                    ->whereNull('subscription_type_id')
                    ->where('type', $oldType)
                    ->update(['subscription_type_id' => $typeId]);
            }
        }
    }

    public function down(): void
    {
        DB::table('subscriptions')->update(['subscription_type_id' => null]);
    }
};
