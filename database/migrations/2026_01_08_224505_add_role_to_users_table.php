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
        Schema::table('users', function (Blueprint $table) {
            // Додаваме колона за улога, по дефолт ќе биде 'student'
            $table->string('role')->default('student')->after('email');

            // Можеш да додадеш и телефон или активен статус ако ти треба
            $table->string('phone')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ги бришеме колоните ако ја вратиме миграцијата назад
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
