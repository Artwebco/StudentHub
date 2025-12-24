<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLessonPrice extends Model
{
    protected $fillable = ['student_id', 'lesson_type_id', 'price'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function lessonType() {
        return $this->belongsTo(LessonType::class);
    }
}