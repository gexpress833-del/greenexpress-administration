<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('points')->nullable()->after('user_id');
            $table->string('mobile_money_operator')->nullable()->after('amount_fc');
            $table->string('mobile_money_number')->nullable()->after('mobile_money_operator');
        });

        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->foreignId('agent_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'points', 'mobile_money_operator', 'mobile_money_number']);
        });
    }
};
