<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    public const STATUS_LABELS = [
        'unpaid'  => 'غير مدفوعة',
        'partial' => 'مدفوعة جزئياً',
        'paid'    => 'مدفوعة',
    ];

    protected $fillable = [
        'patient_id', 'visit_id', 'issued_at', 'subtotal', 'discount', 'total', 'notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // رقم الفاتورة: INV-00001
        static::created(function (Invoice $invoice) {
            $invoice->number = 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT);
            $invoice->saveQuietly();
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_at')->latest('id');
    }

    public function getPaidAttribute(): float
    {
        // إذا الاستعلام استخدم withSum ما بنعمل استعلام زيادة
        $sum = array_key_exists('payments_sum_amount', $this->attributes)
            ? $this->attributes['payments_sum_amount']
            : $this->payments->sum('amount');

        return (float) $sum;
    }

    public function getBalanceAttribute(): float
    {
        return round((float) $this->total - $this->paid, 2);
    }

    public function getStatusAttribute(): string
    {
        if ($this->paid >= (float) $this->total) {
            return 'paid';
        }

        return $this->paid > 0 ? 'partial' : 'unpaid';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status];
    }
}
