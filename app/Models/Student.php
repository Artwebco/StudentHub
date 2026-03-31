<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'active',
        'invoice_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // ОВАА ФУНКЦИЈА МОРА ДА ЈА ДОДАДЕШ:
    public function invoices(): HasMany
    {
        // Провери дали моделот се вика точно Invoice (или StudentInvoice)
        return $this->hasMany(Invoice::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function lessonPrices(): HasMany
    {
        return $this->hasMany(StudentLessonPrice::class);
    }
}
