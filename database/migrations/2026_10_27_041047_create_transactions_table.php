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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 30);
            $table->decimal('pendapatan', 15, 2);
            $table->decimal('pengeluaran', 15, 2);
            $table->decimal('m1_pen', 15, 2);
            $table->decimal('m1_peng', 15, 2);
            $table->decimal('m2_pen', 15, 2);
            $table->decimal('m2_peng', 15, 2);
            $table->decimal('m3_pen', 15, 2);
            $table->decimal('m3_peng', 15, 2);
            $table->decimal('m4_pen', 15, 2);
            $table->decimal('m4_peng', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
