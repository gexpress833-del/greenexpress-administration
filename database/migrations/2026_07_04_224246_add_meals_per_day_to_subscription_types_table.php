<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscription_types', function (Blueprint $table) {
            $table->integer('meals_per_day')->default(1)->after('duration_days');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_types', function (Blueprint $table) {
            $table->dropColumn('meals_per_day');
        });
    }
};
