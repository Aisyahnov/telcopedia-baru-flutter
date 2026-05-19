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
        // Rename table withdrawals to penarikan_dana
        Schema::rename('withdrawals', 'penarikan_dana');

        // Rename column balance to saldo in users table
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('balance', 'saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('saldo', 'balance');
        });

        Schema::rename('penarikan_dana', 'withdrawals');
    }
};
