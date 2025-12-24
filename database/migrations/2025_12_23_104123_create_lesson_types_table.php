<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Пр. "30 min", "45 min"
            $table->integer('duration'); // Времетраење во минути
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_types');
    }
};
