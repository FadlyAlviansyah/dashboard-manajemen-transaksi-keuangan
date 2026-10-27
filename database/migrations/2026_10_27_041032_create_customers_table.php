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
        Schema::create('customers', function (Blueprint $table) {
            $table->string('customer_id', 10)->primary();
            $table->string('name', 100);
            $table->string('gender', 10);
            $table->string('state', 60)->index();
            $table->string('occupation', 50);
            $table->string('customer_segment', 30)->index();
            $table->decimal('annual_income', 15, 2);
            $table->date('join_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
