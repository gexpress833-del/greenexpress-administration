<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->date('calculated_for_date')->nullable()->after('order_id');
            $table->index(['agent_id', 'calculated_for_date']);
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropIndex(['agent_id', 'calculated_for_date']);
            $table->dropColumn('calculated_for_date');
        });
    }
};
