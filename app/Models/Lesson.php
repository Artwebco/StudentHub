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
        'lesson_status',
        'notes'
    ];

    protected $casts = [
        'lesson_date' => 'date:d.m.Y',
    ];

    // Релации (за да знае часот на кој студент припаѓа)
    public function student()
    {
        // return $this->belongsTo(Student::class);
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function lessonType()
    {
        // Сега се поврзуваме со LessonTemplate, но ја користиме истата колона 'lesson_type_id'
        return $this->belongsTo(LessonTemplate::class, 'lesson_type_id');
    }
}
