<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('activity')->nullable();           // Дејност
            $table->string('registration_number')->nullable(); // Матичен број
            $table->string('address')->nullable();            // Целосна адреса
            $table->string('tax_number')->nullable();         // ЕДБ
            $table->string('bank_account')->nullable();       // Жиро сметка
            $table->string('bank_name')->nullable();          // Банка
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
