<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('points')->default(0);
            $table->decimal('value_usd', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_points');
    }
};
