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
        Schema::table('food_items', function (Blueprint $table) {
            $table->boolean('has_half_portion')->default(false)->after('price');
            $table->decimal('half_price', 10, 2)->nullable()->after('has_half_portion');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('portion', 50)->default('Regular')->after('category_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            $table->dropColumn(['has_half_portion', 'half_price']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('portion');
        });
    }
};
