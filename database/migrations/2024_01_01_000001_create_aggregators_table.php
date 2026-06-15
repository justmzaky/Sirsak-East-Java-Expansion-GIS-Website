<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aggregators', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 10)->unique()->comment('e.g. AGG01');
            $table->string('name', 150);
            $table->string('pic_name', 100)->nullable()->comment('Person in charge');
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('regency', 100);
            $table->string('phone', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aggregators');
    }
};
