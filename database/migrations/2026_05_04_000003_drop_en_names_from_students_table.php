<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'first_name_en')) {
                $table->dropColumn('first_name_en');
            }
            if (Schema::hasColumn('students', 'last_name_en')) {
                $table->dropColumn('last_name_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name_en')->nullable();
            $table->string('last_name_en')->nullable();
        });
    }
};
