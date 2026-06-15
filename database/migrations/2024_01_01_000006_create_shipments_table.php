<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('shipment_code', 30)->unique()->comment('SHP-YYYYMMDD-XXXX');
            $table->foreignUlid('aggregator_id')->constrained('aggregators')->restrictOnDelete();
            $table->foreignUlid('recycler_id')->constrained('recyclers')->restrictOnDelete();
            $table->foreignUlid('dispatched_by')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('material_type', ['PET', 'MLP', 'Kardus', 'Metal', 'HDPE', 'Campuran']);
            $table->decimal('shipped_weight_kg', 10, 2);
            $table->decimal('received_weight_kg', 10, 2)->nullable();
            $table->enum('status', ['dispatched', 'in_transit', 'received', 'cancelled'])->default('dispatched');
            $table->string('vehicle_info', 100)->nullable()->comment('Nomor plat / info kendaraan');
            $table->text('notes')->nullable();
            $table->timestamp('dispatched_at');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->index(['aggregator_id', 'status']);
            $table->index(['recycler_id', 'status']);
            $table->index(['material_type', 'dispatched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
