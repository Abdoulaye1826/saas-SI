<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Client de la boutique.
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'type',
        'phone',
        'email',
        'address',
        'city',
        'registered_at',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'registered_at' => 'date',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeOfType($query, ?string $type)
    {
        if (empty($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('full_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
