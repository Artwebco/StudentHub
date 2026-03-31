<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'sift') && !Schema::hasColumn('school_settings', 'swift')) {
                $table->renameColumn('sift', 'swift');
            }
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (Schema::hasColumn('school_settings', 'swift') && !Schema::hasColumn('school_settings', 'sift')) {
                $table->renameColumn('swift', 'sift');
            }
        });
    }
};
