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
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name_mk')->nullable();
            $table->string('first_name_en')->nullable();
            $table->string('last_name_mk')->nullable();
            $table->string('last_name_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['first_name_mk', 'first_name_en', 'last_name_mk', 'last_name_en']);
        });
    }
};
