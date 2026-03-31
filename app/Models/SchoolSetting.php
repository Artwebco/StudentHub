<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name',
        'activity',
        'registration_number',
        'iban_number',
        'swift_number',
        'tax_number',
        'address',
        'bank_account',
        'bank_name',
        'email',
        'website',
        'phone',
        'logo_path'
    ];
}
