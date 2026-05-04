<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('first_name_mk')->nullable()->after('custom_client_name');
            $table->string('last_name_mk')->nullable()->after('first_name_mk');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['first_name_mk', 'last_name_mk']);
        });
    }
};
