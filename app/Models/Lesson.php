<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lesson_type_id',
        'price_at_time',
        'lesson_date',
        'start_time',
        'end_time',
        'notes'
    ];

    protected $casts = [
        'lesson_date' => 'date:d.m.Y',
    ];

    // Релации (за да знае часот на кој студент припаѓа)
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function lessonType()
    {
        return $this->belongsTo(LessonType::class);
    }
}
