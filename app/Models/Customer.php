<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Client de la boutique.
 *
 * Authentifiable depuis la Phase 3 (comptes clients, guard `customer` —
 * voir config/auth.php) : un Customer peut être créé par le staff ou via un
 * checkout invité (sans mot de passe), puis "réclamé" plus tard par son
 * propriétaire via l'inscription (voir RegisteredCustomerController) sans
 * jamais dupliquer la fiche.
 */
class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'type',
        'phone',
        'email',
        'password',
        'address',
        'city',
        'registered_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'registered_at' => 'date',
        'password' => 'hashed',
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

    public function onlineOrders(): HasMany
    {
        return $this->hasMany(OnlineOrder::class);
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

    // ─── Méthodes métier ─────────────────────────────────────

    public function hasAccount(): bool
    {
        return $this->password !== null;
    }
}
