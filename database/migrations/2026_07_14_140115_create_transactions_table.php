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
            $table->foreignId('client_id')->constrained();
            $table->foreignId('client_customer_id')->constrained();
            $table->string('site_reference_id');
            $table->unique(['client_id', 'site_reference_id']);
            $table->foreignId('pg_connection_id')->constrained()->default;
            $table->integer('amount');
            $table->string('currency')->default('INR');
            $table->integer('transaction_amount');
            $table->string('response_code')->comment('0: success, 1: failed');
            $table->string('status')->comment('PENDING, SUCCESS, FAILED');
            $table->string('payment_method');
            $table->string('transaction_purpose')->nullable();
            $table->string('transaction_id');
            $table->index(['transaction_id']);
            $table->integer('service_tax_amount')->default(0);
            $table->integer('processing_fee_amount')->default(0);
            $table->boolean('recover_pg_fees')->default(false)->comment('true: recover pg fees, false: do not recover');
            $table->integer('pg_fees')->default(0);
            $table->integer('total_amount')->default(0);
            $table->dateTime('transaction_date_time')->comment('transaction date time');
            $table->json('response_data')->nullable();
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
