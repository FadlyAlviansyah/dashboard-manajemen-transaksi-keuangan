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
            $table->string('transaction_id', 12)->primary();
            $table->date('transaction_date')->index();
            $table->string('customer_id', 10)->index();
            $table->string('transaction_type', 30)->index();
            $table->string('channel', 30)->index();
            $table->string('merchant_category', 40)->index();
            $table->decimal('amount', 15, 2);
            $table->string('transaction_status', 20)->index();
            $table->boolean('is_fraud')->default(false)->index();
            $table->timestamps();
        
            $table->foreign('customer_id')->references('customer_id')->on('customers');
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
