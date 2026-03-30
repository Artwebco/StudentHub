<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Ги менуваме колоните да прифаќаат NULL вредности
            $table->date('date_from')->nullable()->change();
            $table->date('date_to')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Враќање во првобитна состојба ако направите rollback
            $table->date('date_from')->nullable(false)->change();
            $table->date('date_to')->nullable(false)->change();
        });
    }
};
