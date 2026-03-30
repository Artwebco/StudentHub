<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('service_description'); // Цел број за часови
            $table->integer('unit_price')->nullable()->after('quantity');         // Цел број за цена
            $table->boolean('is_advance')->default(false)->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Ги бришеме колоните ако се враќа миграцијата
            $table->dropColumn(['quantity', 'unit_price', 'is_advance']);
        });
    }
};
