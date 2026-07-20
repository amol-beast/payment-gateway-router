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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('response_code')->nullable()->comment('0: success, 1: failed')->change();
            $table->string('payment_method')->nullable()->change();
            $table->string('transaction_id')->nullable()->change();
            $table->string('transaction_purpose')->nullable()->change();
            $table->integer('service_tax_amount')->nullable()->default(0)->change();
            $table->integer('processing_fee_amount')->nullable()->default(0)->change();
            $table->integer('pg_fees')->nullable()->default(0)->change();
            $table->integer('total_amount')->nullable()->default(0)->change();
            $table->dateTime('transaction_date_time')->nullable()->comment('transaction date time')->change();
            $table->json('request_data')->nullable()->change();
            $table->json('response_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('response_code')->comment('0: success, 1: failed')->change();
            $table->string('payment_method')->change();
            $table->string('transaction_id')->change();
            $table->string('transaction_purpose')->nullable()->change();
            $table->integer('service_tax_amount')->default(0)->change();
            $table->integer('processing_fee_amount')->default(0)->change();
            $table->integer('pg_fees')->default(0)->change();
            $table->integer('total_amount')->default(0)->change();
            $table->dateTime('transaction_date_time')->comment('transaction date time')->change();
            $table->json('request_data')->nullable()->change();
            $table->json('response_data')->nullable()->change();
        });
    }
};
