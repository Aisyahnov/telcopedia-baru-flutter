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
        // Update Carts Table
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        });

        // Update Orders Table
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_amount', 12, 2)->after('voucher_id')->default(0);
            $table->decimal('discount_amount', 12, 2)->after('subtotal_amount')->default(0);
            $table->decimal('admin_fee', 12, 2)->after('discount_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn('voucher_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal_amount', 'discount_amount', 'admin_fee']);
        });
    }
};
