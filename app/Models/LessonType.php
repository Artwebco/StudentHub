<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'duration'];

    public function studentPrices()
{
    return $this->hasMany(StudentLessonPrice::class);
}

}
