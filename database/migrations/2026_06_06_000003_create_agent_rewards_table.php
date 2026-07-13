<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users');
            $table->string('type'); // free_standard_meal
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->date('earned_date');
            $table->timestamp('claimed_at')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_rewards');
    }
};
