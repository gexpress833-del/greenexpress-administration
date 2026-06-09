<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKeys = DB::select('SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = "subscriptions" AND CONSTRAINT_TYPE = "FOREIGN KEY"');
        $hasFk = collect($foreignKeys)->contains(fn ($fk) => str_contains($fk->CONSTRAINT_NAME, 'client_id'));

        Schema::table('subscriptions', function (Blueprint $table) use ($hasFk) {
            if ($hasFk) {
                $table->dropForeign(['client_id']);
            }
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->foreign('client_id')->references('id')->on('users')->nullOnDelete();
            $table->string('client_name')->nullable()->after('client_id');
            $table->string('client_phone')->nullable()->after('client_name');
            $table->string('client_email')->nullable()->after('client_phone');
            $table->timestamp('credentials_generated_at')->nullable()->after('validated_by');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('users');
            $table->dropColumn(['client_name', 'client_phone', 'client_email', 'credentials_generated_at']);
        });
    }
};
