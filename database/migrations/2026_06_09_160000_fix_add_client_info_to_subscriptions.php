<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('subscriptions');

        Schema::table('subscriptions', function (Blueprint $table) use ($columns) {
            // Rendre client_id nullable s'il existe et n'est pas déjà nullable
            if (in_array('client_id', $columns)) {
                $table->unsignedBigInteger('client_id')->nullable()->change();
            }

            // Ajouter les colonnes client_* si elles n'existent pas
            if (!in_array('client_name', $columns)) {
                $table->string('client_name')->nullable()->after('client_id');
            }
            if (!in_array('client_phone', $columns)) {
                $table->string('client_phone')->nullable()->after('client_name');
            }
            if (!in_array('client_email', $columns)) {
                $table->string('client_email')->nullable()->after('client_phone');
            }
            if (!in_array('credentials_generated_at', $columns)) {
                $table->timestamp('credentials_generated_at')->nullable()->after('validated_by');
            }
        });
    }

    public function down(): void
    {
        $columns = Schema::getColumnListing('subscriptions');

        Schema::table('subscriptions', function (Blueprint $table) use ($columns) {
            if (in_array('client_name', $columns)) {
                $table->dropColumn('client_name');
            }
            if (in_array('client_phone', $columns)) {
                $table->dropColumn('client_phone');
            }
            if (in_array('client_email', $columns)) {
                $table->dropColumn('client_email');
            }
            if (in_array('credentials_generated_at', $columns)) {
                $table->dropColumn('credentials_generated_at');
            }
        });
    }
};
