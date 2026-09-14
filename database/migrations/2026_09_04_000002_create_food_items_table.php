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
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // In INR ₹
            $table->string('prep_time')->default('15 mins');
            $table->string('status')->default('Available'); // Available, Low Stock, Out of Stock
            $table->string('image')->nullable();
            $table->boolean('is_veg')->default(false);
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->integer('orders_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
