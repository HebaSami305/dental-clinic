<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToothRecord extends Model
{
    public const UPPER = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28];
    public const LOWER = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];

    public const CONDITIONS = [
        'healthy'           => ['label' => 'سليم',        'color' => '#ffffff'],
        'cavity'            => ['label' => 'تسوس',        'color' => '#ef4444'],
        'filled'            => ['label' => 'حشوة',        'color' => '#3b82f6'],
        'root_canal'        => ['label' => 'علاج عصب',    'color' => '#f59e0b'],
        'crown'             => ['label' => 'تاج',         'color' => '#a855f7'],
        'implant'           => ['label' => 'زرعة',        'color' => '#14b8a6'],
        'extraction_needed' => ['label' => 'يحتاج خلع',   'color' => '#9f1239'],
        'missing'           => ['label' => 'مفقود',       'color' => '#cbd5e1'],
    ];

    protected $fillable = ['patient_id', 'tooth_number', 'condition', 'notes'];

    public static function allTeeth(): array
    {
        return array_merge(self::UPPER, self::LOWER);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
