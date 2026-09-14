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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('order_type')->default('Dine In'); // Dine In, Take Away
            $table->string('table_no')->nullable();
            $table->string('attendant')->default('ChrisThomas - 226');
            $table->string('order_time');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00); // 5% GST
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('status')->default('KOT Pending'); // KOT Pending, Cooking, Items Served, Bill Generated, Paid, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
