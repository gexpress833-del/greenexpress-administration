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
        Schema::table('delivery_points', function (Blueprint $table): void {
            $table->dropForeign(['delivery_id']);
            $table->foreignId('delivery_id')->nullable()->change();
            $table->foreign('delivery_id')->references('id')->on('deliveries')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_points', function (Blueprint $table): void {
            $table->dropForeign(['delivery_id']);
            $table->foreignId('delivery_id')->nullable(false)->change();
            $table->foreign('delivery_id')->references('id')->on('deliveries')->cascadeOnDelete();
        });
    }
};
