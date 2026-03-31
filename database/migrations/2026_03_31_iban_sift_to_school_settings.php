<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('school_settings', 'iban')) {
                $table->string('iban')->nullable()->after('website');
            }
            if (!Schema::hasColumn('school_settings', 'sift')) {
                $table->string('sift')->nullable()->after('iban');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'iban')) {
                $table->dropColumn('iban');
            }
            if (Schema::hasColumn('school_settings', 'sift')) {
                $table->dropColumn('swift');
            }
        });
    }
};
