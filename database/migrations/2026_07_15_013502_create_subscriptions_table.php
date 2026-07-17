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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('client_customer_id')->constrained();
            $table->foreignId('pg_connection_id')->constrained();
            $table->string('subscription_type')->comment('SUBSCRIPTION, RECURRING');
            $table->string('site_reference_id');
            $table->unique(['client_id', 'site_reference_id']);
            $table->string('subscription_id');
            $table->unique(['pg_connection_id', 'subscription_id']);
            $table->string('plan_id')->nullable();
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->string('period')->comment('DAILY, WEEKLY, MONTHLY, QUATERLY, YEARLY');
            $table->integer('interval');
            $table->integer('amount');
            $table->string('payment_method')->nullable();
            $table->string('currency')->default('INR');
            $table->string('status')->comment('CREATED, AUTHENTICATED, ACTIVE, PENDING, HALTED, CANCELLED, PAUSED, EXPIRED, COMPLETED');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
