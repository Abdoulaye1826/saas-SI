<?php

namespace App\Models;

use App\Enums\OnlineOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Commande passée depuis la boutique en ligne. Reste indépendante d'une
 * Vente tant qu'elle n'est pas confirmée par le staff (voir
 * OnlineOrderService::confirm()) — sale_id n'est rempli qu'à ce moment-là.
 */
class OnlineOrder extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'sale_id',
        'status',
        'guest_name',
        'guest_phone',
        'guest_email',
        'delivery_method',
        'delivery_address',
        'delivery_city',
        'delivery_zone',
        'delivery_fee',
        'payment_method',
        'subtotal',
        'total',
        'notes',
        'assigned_driver_id',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'status' => OnlineOrderStatus::class,
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OnlineOrderItem::class);
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('order_number', 'like', "%{$term}%")
                ->orWhere('guest_name', 'like', "%{$term}%")
                ->orWhere('guest_phone', 'like', "%{$term}%");
        });
    }

    // ─── Méthodes métier ─────────────────────────────────────

    public function isNew(): bool
    {
        return $this->status === OnlineOrderStatus::New;
    }

    public function isCancelled(): bool
    {
        return $this->status === OnlineOrderStatus::Cancelled;
    }

    public function isConfirmed(): bool
    {
        return $this->sale_id !== null;
    }
}
