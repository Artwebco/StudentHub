<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lesson_templates', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_mk')->nullable()->after('name_en');
        });

        DB::table('lesson_templates')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function ($template) {
                DB::table('lesson_templates')
                    ->where('id', $template->id)
                    ->update([
                        'name_en' => $template->name,
                        'name_mk' => $template->name,
                    ]);
            });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('lesson_type_id')->nullable()->after('service_description')->constrained('lesson_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lesson_type_id');
        });

        Schema::table('lesson_templates', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_mk']);
        });
    }
};
