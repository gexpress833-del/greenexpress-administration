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
        Schema::table('agent_points', function (Blueprint $table) {
            $table->unique(['agent_id', 'order_id'], 'agent_points_agent_order_unique');
        });

        Schema::table('agent_rewards', function (Blueprint $table) {
            $table->unique(['agent_id', 'type', 'earned_date'], 'agent_rewards_agent_type_date_unique');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->unique(['agent_id', 'type', 'earned_date'], 'badges_agent_type_date_unique');
        });

        Schema::table('delivery_reviews', function (Blueprint $table) {
            $table->unique('order_id', 'delivery_reviews_order_unique');
        });
    }

    public function down(): void
    {
        Schema::table('agent_points', function (Blueprint $table) {
            $table->dropUnique('agent_points_agent_order_unique');
        });

        Schema::table('agent_rewards', function (Blueprint $table) {
            $table->dropUnique('agent_rewards_agent_type_date_unique');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropUnique('badges_agent_type_date_unique');
        });

        Schema::table('delivery_reviews', function (Blueprint $table) {
            $table->dropUnique('delivery_reviews_order_unique');
        });
    }
};
