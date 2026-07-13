<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->decimal('price_fc', 12, 2)->default(0)->after('price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency', 10)->default('usd')->after('total_amount');
            $table->decimal('total_amount_fc', 12, 2)->default(0)->after('currency');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price_fc', 12, 2)->default(0)->after('unit_price');
            $table->decimal('total_price_fc', 12, 2)->default(0)->after('total_price');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('currency', 10)->default('usd')->after('price');
            $table->decimal('price_fc', 12, 2)->default(0)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('price_fc');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['currency', 'total_amount_fc']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price_fc', 'total_price_fc']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'price_fc']);
        });
    }
};
