<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('token');
            $table->string('token_hash', 64)->unique();
            $table->string('platform', 20)->nullable();
            $table->string('device_id', 191)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'revoked_at']);
            $table->index(['device_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
