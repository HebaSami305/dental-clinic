<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    protected $fillable = ['patient_id', 'visited_at', 'diagnosis', 'notes'];

    protected $casts = [
        'visited_at' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(VisitProcedure::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->procedures->sum('price');
    }
    public function invoice(): HasOne
{
    return $this->hasOne(Invoice::class);
}
}
