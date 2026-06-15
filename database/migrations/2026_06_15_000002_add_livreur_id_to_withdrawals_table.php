<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('withdrawals', 'livreur_id')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                $table->foreignId('livreur_id')->nullable()->after('agent_id')->constrained('users')->nullOnDelete();
            });
        }

        // L'agent_id devient nullable car un retrait peut désormais appartenir
        // soit à un agent, soit à un livreur.
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('livreur_id');
        });
    }
};
