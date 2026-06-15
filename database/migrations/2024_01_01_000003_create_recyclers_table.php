<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recyclers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 10)->unique()->comment('e.g. REC01');
            $table->string('name', 150);
            $table->string('company_type', 50)->nullable()->comment('PT, CV, UD, etc');
            $table->string('pic_name', 100)->nullable();
            $table->text('address')->nullable();
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
        Schema::dropIfExists('recyclers');
    }
};
