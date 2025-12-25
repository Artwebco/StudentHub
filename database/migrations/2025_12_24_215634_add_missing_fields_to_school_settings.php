<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            // Проверуваме дали колоната постои пред да ја додадеме (за да нема грешка)
            if (!Schema::hasColumn('school_settings', 'activity')) {
                $table->string('activity')->nullable();
            }
            if (!Schema::hasColumn('school_settings', 'registration_number')) {
                $table->string('registration_number')->nullable();
            }
            if (!Schema::hasColumn('school_settings', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('school_settings', 'website')) {
                $table->string('website')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['activity', 'registration_number', 'email', 'website']);
        });
    }
};
