<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('parent_name')->nullable()->after('timezone');
            $table->string('parent_email')->nullable()->after('parent_name');
            $table->boolean('send_invoices_to_parent')->default(false)->after('parent_email');
            $table->boolean('send_notifications_to_parent')->default(false)->after('send_invoices_to_parent');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'parent_name',
                'parent_email',
                'send_invoices_to_parent',
                'send_notifications_to_parent',
            ]);
        });
    }
};
