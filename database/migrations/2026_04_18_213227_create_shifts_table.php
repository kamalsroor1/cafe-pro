<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('started_by')->constrained('users');
            $table->foreignUuid('closed_by')->nullable()->constrained('users');
            $table->decimal('starting_cash', 10, 2);
            $table->decimal('ending_cash', 10, 2)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('expected_cash', 10, 2)->nullable();
            $table->decimal('cash_difference', 10, 2)->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Now we can add shift_id foreign key to wastage_logs
        Schema::table('wastage_logs', function (Blueprint $table) {
            $table->foreign('shift_id')->references('id')->on('shifts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wastage_logs', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
        });
        Schema::dropIfExists('shifts');
    }
};
