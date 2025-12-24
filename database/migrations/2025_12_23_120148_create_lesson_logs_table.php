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
    Schema::create('lesson_logs', function (Blueprint $table) {
        $table->id();
        
        // Референца кон студентот
        $table->foreignId('student_id')->constrained()->onDelete('cascade');
        
        // Референца кон типот на час (30, 45, 60, 90 мин)
        $table->foreignId('lesson_type_id')->constrained();
        
        // ФИКСНАТА ЦЕНА: Овде ја запишуваме цената од `student_lesson_prices` во моментот на часот.
        // Ова е клучно за ако цената се смени во иднина, старите записи да останат точни.
        $table->decimal('price_at_time', 10, 2);
        
        $table->date('lesson_date'); // Кога се одржал часот
        $table->boolean('is_paid')->default(false); // Статус: Дали е платено?
        $table->text('notes')->nullable(); // Што работевте на часот?
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_logs');
    }
};
