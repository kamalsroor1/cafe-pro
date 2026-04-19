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
        Schema::create('wastage_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ingredient_id')->constrained('ingredients');
            $table->uuid('shift_id')->nullable(); // no foreign key yet since shifts table isn't created
            $table->foreignUuid('recorded_by')->constrained('users');
            $table->decimal('qty_wasted', 10, 3);
            $table->decimal('cost_value', 10, 2);
            $table->enum('reason', ['expired', 'damaged', 'spillage', 'other']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wastage_logs');
    }
};
