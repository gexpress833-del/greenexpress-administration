<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite limitation: we need to recreate the table to change constraints
        // First, save existing data if any
        $existing = DB::table('deliveries')->get()->toArray();

        Schema::dropIfExists('deliveries');

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('livreur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivery_code')->unique();
            $table->string('status')->default('pending');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Restore data if any
        if (! empty($existing)) {
            foreach ($existing as $row) {
                $data = (array) $row;
                // Ensure status is valid
                if (empty($data['status']) || ! in_array($data['status'], ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed'])) {
                    $data['status'] = 'assigned';
                }
                DB::table('deliveries')->insert($data);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('livreur_id')->constrained('users');
            $table->string('delivery_code')->unique();
            $table->enum('status', ['assigned', 'picked_up', 'in_transit', 'delivered', 'failed'])->default('assigned');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
