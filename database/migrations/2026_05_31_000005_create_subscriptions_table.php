<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('agent_id')->constrained('users');
            $table->enum('type', ['weekly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->integer('remaining_days')->default(0);
            $table->decimal('price', 12, 2);
            $table->enum('status', ['pending', 'active', 'suspended', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('admin_validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
