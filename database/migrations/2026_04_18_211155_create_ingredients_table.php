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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('unit', ['g', 'kg', 'ml', 'l', 'pcs', 'tbsp', 'tsp']);
            $table->decimal('stock_qty', 10, 3)->default(0);
            $table->decimal('min_stock_qty', 10, 3)->default(0);
            $table->decimal('cost_per_unit', 10, 4);
            $table->string('supplier')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('stock_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
