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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // اسم المشروب (قهوة، شاي..)
            $table->decimal('price', 10, 2); // السعر
            $table->string('image')->nullable(); // صورة المنتج (اختياري)
            $table->string('category')->nullable(); // تصنيف (سخن، ساقد، حلويات)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
