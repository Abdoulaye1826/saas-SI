<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne de détail d'un cadeau (un produit offert, avec sa quantité et,
 * si suivi par IMEI, l'unité précise offerte).
 */
class GiftItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_id',
        'product_id',
        'product_imei_id',
        'quantity',
        'unit_value',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productImei(): BelongsTo
    {
        return $this->belongsTo(ProductImei::class);
    }
}
