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
        Schema::create('lesson_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Име (пр. Индивидуален час)
            $table->string('description')->nullable(); // Опис (пр. Еден на еден настава)
            $table->integer('duration');        // Времетраење во минути (30, 45, 60)
            $table->decimal('default_price', 10, 2)->default(0); // Стандардна цена
            $table->string('icon')->default('clock'); // Икона (опционално)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_templates');
    }
};
