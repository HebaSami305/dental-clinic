<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = [
        'cash'     => 'نقداً',
        'card'     => 'بطاقة',
        'transfer' => 'حوالة بنكية',
        'check'    => 'شيك',
    ];

    protected $fillable = ['invoice_id', 'amount', 'method', 'paid_at', 'notes'];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
