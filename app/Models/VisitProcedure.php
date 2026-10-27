<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitProcedure extends Model
{
    // السعر الافتراضي (تقدر تعدله بالفورم) + حالة السن اللي بتنعكس على المخطط
    public const CATALOG = [
        'كشف'       => ['price' => 50,   'condition' => null],
        'تنظيف'     => ['price' => 100,  'condition' => null],
        'حشوة'      => ['price' => 150,  'condition' => 'filled'],
        'علاج عصب'  => ['price' => 500,  'condition' => 'root_canal'],
        'تاج'       => ['price' => 700,  'condition' => 'crown'],
        'زرعة'      => ['price' => 3000, 'condition' => 'implant'],
        'خلع'       => ['price' => 100,  'condition' => 'missing'],
        'تقويم'     => ['price' => 200,  'condition' => null],
        'أخرى'      => ['price' => 0,    'condition' => null],
    ];

    protected $fillable = ['visit_id', 'tooth_number', 'name', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
