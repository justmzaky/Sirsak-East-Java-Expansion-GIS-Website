<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('transaction_code', 30)->unique()->comment('WC-YYYYMMDD-XXXX');
            $table->foreignUlid('waste_unit_id')->constrained('waste_units')->restrictOnDelete();
            $table->foreignUlid('aggregator_id')->constrained('aggregators')->restrictOnDelete();
            $table->foreignUlid('recorded_by')->constrained('users')->restrictOnDelete();
            $table->enum('material_type', ['PET', 'MLP', 'Kardus', 'Metal', 'HDPE', 'Campuran']);
            $table->enum('material_condition', ['Bersih & Kering', 'Kotor / Campuran', 'Basah'])->default('Bersih & Kering');
            $table->decimal('gross_weight_kg', 10, 2)->comment('Berat kotor');
            $table->decimal('tare_weight_kg', 10, 2)->default(0)->comment('Berat tara/kemasan');
            $table->decimal('net_weight_kg', 10, 2)->comment('Berat bersih = kotor - tara');
            $table->decimal('price_per_kg', 12, 2)->default(0);
            $table->decimal('total_value', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('collected_at');
            $table->timestamps();

            $table->index(['waste_unit_id', 'collected_at']);
            $table->index(['aggregator_id', 'collected_at']);
            $table->index(['material_type', 'collected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
