<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained(); // Кој ученик бил на час
    $table->foreignId('lesson_type_id')->constrained(); // Колку долго траел часот
    $table->decimal('price_at_time', 10, 2); // Ја преземаме цената од student_lesson_prices
    $table->date('lesson_date'); // Кога се одржал часот
    $table->boolean('is_paid')->default(false); // Дали е платено или ти должи
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
