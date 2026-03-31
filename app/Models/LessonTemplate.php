<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTemplate extends Model
{
    // Ова им дозволува на овие полиња да бидат запишани во базата
    protected $fillable = ['name', 'description', 'duration', 'default_price', 'icon'];
}
