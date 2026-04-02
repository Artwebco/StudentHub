<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTemplate extends Model
{
    protected $fillable = ['name', 'name_en', 'name_mk', 'description', 'duration', 'default_price', 'icon'];

    public function getAdminNameAttribute(): string
    {
        return $this->firstNonEmpty([$this->name_en, $this->name, $this->name_mk]);
    }

    public function getInvoiceNameAttribute(): string
    {
        return $this->firstNonEmpty([$this->name_mk, $this->name, $this->name_en]);
    }

    protected function firstNonEmpty(array $values): string
    {
        foreach ($values as $value) {
            if (filled($value)) {
                return (string) $value;
            }
        }

        return '';
    }
}
