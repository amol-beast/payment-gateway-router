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
        Schema::table('clients_connections', function (Blueprint $table) {
            $table->string('transactionType')->default('sale')->after('type')->comment('sale, donation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_connections', function (Blueprint $table) {
            $table->dropColumn('transactionType');
        });
    }
};
