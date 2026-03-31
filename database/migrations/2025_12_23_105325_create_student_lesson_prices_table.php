<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_lesson_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->unique(['student_id', 'lesson_type_id']); // За да нема дупликати
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_lesson_prices');
    }
};
