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
            // Го правиме student_id да може да биде празен за фактури на услуги
            $table->foreignId('student_id')->nullable()->change();

            // Ги додаваме новите полиња за надворешни клиенти и описи
            $table->string('custom_client_name')->nullable()->after('student_id');
            $table->text('service_description')->nullable()->after('custom_client_name');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Ги бришеме додадените колони
            $table->dropColumn(['custom_client_name', 'service_description']);

            // Го враќаме student_id да биде задолжителен (како што беше претходно)
            // ЗАБЕЛЕШКА: Ова може да јави грешка ако веќе имаш внесено фактури без student_id во базата
            $table->foreignId('student_id')->nullable(false)->change();
        });
    }
};
