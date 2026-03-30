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
            $table->timestamp('email_sent_at')->nullable()->after('cancelled_at');
            $table->string('email_sent_to')->nullable()->after('email_sent_at');
            $table->unsignedInteger('email_sent_count')->default(0)->after('email_sent_to');
            $table->text('email_last_error')->nullable()->after('email_sent_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['email_sent_at', 'email_sent_to', 'email_sent_count', 'email_last_error']);
        });
    }
};
