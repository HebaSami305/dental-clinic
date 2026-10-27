<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    public const STATUSES = [
        'scheduled'   => 'محجوز',
        'in_progress' => 'قيد العلاج',
        'completed'   => 'تم',
        'cancelled'   => 'ملغي',
        'no_show'     => 'لم يحضر',
    ];

    public const TYPES = ['كشف', 'تنظيف', 'حشوة', 'علاج عصب', 'خلع', 'تقويم', 'تركيب', 'أخرى'];

    protected $fillable = [
        'patient_id', 'starts_at', 'duration_minutes', 'type', 'status', 'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function getEndsAtAttribute()
    {
        return $this->starts_at->copy()->addMinutes($this->duration_minutes);
    }

    public function scopeOnDate(Builder $query, $date): Builder
    {
        return $query->whereDate('starts_at', $date);
    }
}
