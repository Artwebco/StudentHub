<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    // Ова им дозволува на овие полиња да бидат запишани во базата
    protected $fillable = [
        'invoice_number',
        'student_id',
        'date_from',
        'date_to',
        'total_amount',
        'is_paid'
    ];

    // Врска со моделот Student
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public static function generateNextInvoiceNumber()
    {
        $currentMonth = date('m');
        $currentYear = date('y');
        $monthYearSuffix = $currentMonth . $currentYear;

        $lastInvoice = self::where('invoice_number', 'like', "%/{$monthYearSuffix}")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            $nextNumber = 1;
        } else {
            $parts = explode('/', $lastInvoice->invoice_number);
            $nextNumber = (int) $parts[0] + 1;
        }

        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/' . $monthYearSuffix;
    }
}
