<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignUuid('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignUuid('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->string('receipt_path')->nullable();
            $table->date('expense_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
