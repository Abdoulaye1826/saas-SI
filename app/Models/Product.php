<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Produit du catalogue gaming.
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'reference',
        'barcode',
        'name',
        'slug',
        'description',
        'brand',
        'purchase_price',
        'sale_price',
        'promo_price',
        'supplier_sale_price',
        'stock_quantity',
        'minimum_stock',
        'image',
        'is_active',
        'tracks_imei',
        'show_on_store',
        'is_featured',
        'is_new',
        'is_promo',
        'allow_order',
        'show_stock',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'supplier_sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
        'tracks_imei' => 'boolean',
        'show_on_store' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_promo' => 'boolean',
        'allow_order' => 'boolean',
        'show_stock' => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function imeis(): HasMany
    {
        return $this->hasMany(ProductImei::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    // ─── Accesseurs ──────────────────────────────────────────

    protected function margin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price - $this->purchase_price,
        );
    }

    protected function marginRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->purchase_price <= 0) {
                    return 0;
                }

                return round((($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
            },
        );
    }

    protected function stockStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->stock_quantity <= 0) {
                    return 'out_of_stock';
                }

                if ($this->stock_quantity <= $this->minimum_stock) {
                    return 'low_stock';
                }

                return 'in_stock';
            },
        );
    }

    protected function stockStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->stock_status) {
                'out_of_stock' => 'Rupture',
                'low_stock' => 'Stock faible',
                default => 'En stock',
            },
        );
    }

    /**
     * Prix effectivement affiché sur la boutique : le prix promo s'il est
     * actif et renseigné, sinon le prix de vente normal.
     */
    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->is_promo && $this->promo_price !== null && (float) $this->promo_price > 0)
                ? (float) $this->promo_price
                : (float) $this->sale_price,
        );
    }

    /**
     * Moyenne des avis approuvés (null si aucun) — arrondie à 1 décimale
     * pour l'affichage (ex: "4.3 ★").
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                $avg = $this->relationLoaded('approvedReviews')
                    ? $this->approvedReviews->avg('rating')
                    : $this->approvedReviews()->avg('rating');

                return $avg !== null ? round((float) $avg, 1) : null;
            },
        );
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%");
        });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['brand'] ?? null, fn ($q, $brand) => $q->where('brand', $brand))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->when(isset($filters['show_on_store']), fn ($q) => $q->where('show_on_store', $filters['show_on_store']))
            ->when(($filters['stock_status'] ?? null) === 'low', fn ($q) => $q->lowStock())
            ->when(($filters['stock_status'] ?? null) === 'out', fn ($q) => $q->outOfStock());
    }

    /**
     * Produits publiés sur la boutique en ligne : visibles ET vendables.
     * Un produit désactivé côté back-office (is_active=false) disparaît
     * automatiquement de la boutique, même si show_on_store est resté coché.
     */
    public function scopeOnStore($query)
    {
        return $query->where('show_on_store', true)->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopePromo($query)
    {
        return $query->where('is_promo', true)->whereNotNull('promo_price');
    }

    public function scopeStoreFilter($query, array $filters)
    {
        return $query
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when(($filters['availability'] ?? null) === 'in_stock', fn ($q) => $q->where('stock_quantity', '>', 0))
            ->when(($filters['availability'] ?? null) === 'out_of_stock', fn ($q) => $q->where('stock_quantity', '<=', 0))
            ->when($filters['is_new'] ?? null, fn ($q) => $q->new())
            ->when($filters['is_promo'] ?? null, fn ($q) => $q->promo())
            ->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('sale_price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('sale_price', '<=', $max));
    }

    // ─── Méthodes métier ─────────────────────────────────────

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Recalcule le stock à partir du nombre d'IMEI réellement disponibles.
     * Source de vérité unique pour les produits suivis par IMEI : garantit
     * qu'aucune incohérence n'est possible entre stock_quantity et les IMEI.
     */
    public function syncImeiStock(): void
    {
        $count = $this->imeis()->available()->count();
        $this->update(['stock_quantity' => $count]);
    }

    /**
     * Génère un slug unique pour l'URL boutique (/boutique/produits/{slug}),
     * à partir du nom. Ajoute un suffixe numérique en cas de collision —
     * contrairement à Category::setNameAttribute() qui suppose l'unicité du
     * nom, la référence produit peut se répéter partiellement d'un nom à
     * l'autre (ex: "Manette PS5" vendue en plusieurs coloris).
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug($name) ?: 'produit';
        $slug = $base;
        $suffix = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix++;
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }
}
