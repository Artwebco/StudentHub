<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{

    protected $fillable = [
        'invoice_number',
        'sequence_number',
        'student_id',
        'custom_client_name',
        'service_description',
        'quantity',
        'unit_price',
        'date_from',
        'date_to',
        'total_amount',
        'status',       // Новата колона за 'paid', 'unpaid', 'cancelled'
        'cancelled_reason',
        'cancelled_at',
        'email_sent_at',
        'email_sent_to',
        'email_sent_count',
        'email_last_error',
        'is_paid',      // Твојата стара колона (ако планираш да ја избришеш, тргни ја и од тука)
        'is_advance',
        'discount_percent'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'email_sent_at' => 'datetime',
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
