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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status'); // Cash, Online, Split
            $table->decimal('cash_amount', 10, 2)->default(0.00)->after('payment_method');
            $table->decimal('online_amount', 10, 2)->default(0.00)->after('cash_amount');
        });

        // Migrate existing order statuses: 'Paid' -> 'Complete', others -> 'Unpaid'
        DB::table('orders')->where('status', 'Paid')->update([
            'status' => 'Complete',
            'payment_method' => 'Cash',
        ]);

        DB::table('orders')->whereIn('status', ['KOT Pending', 'Cooking', 'Items Served', 'Bill Generated'])->update([
            'status' => 'Unpaid',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'cash_amount', 'online_amount']);
        });
    }
};
