<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'gender', 'birth_date',
        'address', 'allergies', 'medical_history',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function booted(): void
    {
        // رقم الملف يتولد تلقائياً بعد الحفظ: P-00001
        static::created(function (Patient $patient) {
            $patient->file_number = 'P-' . str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $patient->saveQuietly();
        });
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q) use ($term) {
            $q->where(function (Builder $q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('file_number', 'like', "%{$term}%");
            });
        });
    }

    public function appointments(): HasMany
{
    return $this->hasMany(Appointment::class);
}
public function toothRecords(): HasMany
{
    return $this->hasMany(ToothRecord::class);
}
public function visits(): HasMany
{
    return $this->hasMany(Visit::class)->latest('visited_at')->latest('id');
}
public function invoices(): HasMany
{
    return $this->hasMany(Invoice::class)->latest('issued_at')->latest('id');
}
}
