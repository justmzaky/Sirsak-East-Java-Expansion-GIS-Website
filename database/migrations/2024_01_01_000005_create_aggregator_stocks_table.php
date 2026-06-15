<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aggregator_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('aggregator_id')->constrained('aggregators')->cascadeOnDelete();
            $table->enum('material_type', ['PET', 'MLP', 'Kardus', 'Metal', 'HDPE', 'Campuran']);
            $table->decimal('stock_kg', 12, 2)->default(0);
            $table->timestamp('last_updated_at')->useCurrent();

            $table->unique(['aggregator_id', 'material_type']);
            $table->index('aggregator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aggregator_stocks');
    }
};
