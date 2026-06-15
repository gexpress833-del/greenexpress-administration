<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('livreur_points')) {
            return;
        }

        Schema::create('livreur_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livreur_id')->constrained('users');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->nullOnDelete();
            $table->integer('points')->default(0);
            $table->decimal('value_usd', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['livreur_id', 'delivery_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livreur_points');
    }
};
