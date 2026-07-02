<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_types', function (Blueprint $table) {
            $table->foreignId('monday_meal_id')->nullable()->constrained('meals')->nullOnDelete()->after('display_order');
            $table->foreignId('tuesday_meal_id')->nullable()->constrained('meals')->nullOnDelete()->after('monday_meal_id');
            $table->foreignId('wednesday_meal_id')->nullable()->constrained('meals')->nullOnDelete()->after('tuesday_meal_id');
            $table->foreignId('thursday_meal_id')->nullable()->constrained('meals')->nullOnDelete()->after('wednesday_meal_id');
            $table->foreignId('friday_meal_id')->nullable()->constrained('meals')->nullOnDelete()->after('thursday_meal_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_types', function (Blueprint $table) {
            $table->dropForeign(['friday_meal_id']);
            $table->dropForeign(['thursday_meal_id']);
            $table->dropForeign(['wednesday_meal_id']);
            $table->dropForeign(['tuesday_meal_id']);
            $table->dropForeign(['monday_meal_id']);
            $table->dropColumn([
                'monday_meal_id',
                'tuesday_meal_id',
                'wednesday_meal_id',
                'thursday_meal_id',
                'friday_meal_id',
            ]);
        });
    }
};
