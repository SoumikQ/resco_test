<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial default settings
        $defaults = [
            'restaurant_name' => 'The Grand Royal Restaurant',
            'tagline' => 'Finest Culinary & Dining Experience',
            'currency' => '₹',
            'phone' => '+91 98765 43210',
            'email' => 'info@grandroyal.in',
            'address' => '12 Park Street, Kolkata, West Bengal - 700016',
            'gstin' => '',
            'invoice_footer' => 'Thank you for dining with us! Please visit again.',
        ];

        foreach ($defaults as $k => $v) {
            DB::table('restaurant_settings')->insert([
                'key' => $k,
                'value' => $v,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_settings');
    }
};
