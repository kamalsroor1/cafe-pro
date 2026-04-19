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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->string('method'); // cash, card, etc.
            $table->decimal('amount', 10, 2);
            $table->decimal('tendered', 10, 2)->nullable(); // amount given by customer
            $table->decimal('change', 10, 2)->nullable(); // change returned to customer
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed, refunded
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
