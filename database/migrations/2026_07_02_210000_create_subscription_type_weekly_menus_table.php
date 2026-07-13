<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_type_weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('week_number');
            $table->string('day');
            $table->foreignId('meal_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['subscription_type_id', 'week_number', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_type_weekly_menus');
    }
};
