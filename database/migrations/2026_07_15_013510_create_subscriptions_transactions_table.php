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
        Schema::create('subscriptions_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->string('transaction_id');
            $table->unique(['subscription_id', 'transaction_id']);
            $table->integer('amount');
            $table->string('currency')->default('INR');
            $table->string('status')->comment('PENDING, SUCCESS, FAILED');
            $table->string('payment_method');
            $table->integer('pg_fees')->default(0);
            $table->integer('pg_tax')->default(0);
            $table->json('data')->nullable();
            $table->dateTime('transaction_date_time')->comment('transaction date time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions_transactions');
    }
};
