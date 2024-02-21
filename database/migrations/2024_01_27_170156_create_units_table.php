<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->foreignUuid('unit_type_id')->constrained('unit_types')->cascadeOnDelete();
            $table->string('unit_number');
            $table->string('owner_name');
            $table->decimal('balance', 10, 2);
            $table->enum('type', ['house', 'apartment']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
