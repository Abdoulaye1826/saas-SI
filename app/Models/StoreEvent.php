<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Événement de suivi boutique (page vue / fiche produit consultée / ajout
 * panier) — voir App\Services\StoreAnalyticsService. Immuable : pas
 * d'`updated_at`.
 */
class StoreEvent extends Model
{
    public const TYPE_PAGE_VIEW = 'page_view';
    public const TYPE_PRODUCT_VIEW = 'product_view';
    public const TYPE_CART_ADD = 'cart_add';

    public $timestamps = false;

    protected $fillable = ['type', 'product_id', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
