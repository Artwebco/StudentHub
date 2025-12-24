<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'country', 
        'active', 
        'invoice_type'
    ];

    public function lessonPrices()
{
    return $this->hasMany(StudentLessonPrice::class);
}
}


