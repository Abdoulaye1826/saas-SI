<?php

namespace App\Models;

use App\Enums\ImeiStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Unité physique (téléphone) identifiée par son IMEI, rattachée à un
 * produit suivi unité par unité (Product::tracks_imei).
 */
class ProductImei extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'imei',
        'status',
        'sale_id',
        'exchange_sale_id',
        'sold_at',
        'gift_id',
        'given_at',
    ];

    protected $casts = [
        'status' => ImeiStatus::class,
        'sold_at' => 'datetime',
        'given_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Vente ayant vendu ce téléphone. */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /** Vente d'échange via laquelle ce téléphone est entré en stock. */
    public function exchangeSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'exchange_sale_id');
    }

    /** Cadeau ayant offert ce téléphone (sortie de stock gratuite). */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', ImeiStatus::Available);
    }
}
