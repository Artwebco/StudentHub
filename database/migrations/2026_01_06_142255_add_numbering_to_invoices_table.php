<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('sequence_number')->after('id')->nullable();
            $table->string('status')->default('unpaid')->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Ги бришеме колоните ако ја враќаме миграцијата
            $table->dropColumn(['sequence_number', 'status']);
        });
    }
};
