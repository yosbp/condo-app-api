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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->integer('reserve_fund');
            $table->decimal('total_amount', 10, 2);
            $table->string('month');
            $table->string('year');
            $table->string('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
