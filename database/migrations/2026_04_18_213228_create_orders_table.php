<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('shift_id')->constrained('shifts');
            $table->foreignUuid('cashier_id')->constrained('users');
            $table->foreignUuid('waiter_id')->nullable()->constrained('users');
            $table->enum('type', ['dine_in', 'takeaway', 'delivery'])->default('dine_in');
            $table->string('table_number')->nullable();
            $table->integer('guest_count')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('status')->default('pending'); // pending, preparing, ready, completed, cancelled
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('payment_method')->nullable(); // cash, card, online
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, partially_paid
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
