<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Catégorie de produits (Consoles, Jeux, Manettes, etc.).
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'show_on_store',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_store' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Catégories publiées sur la boutique en ligne, dans l'ordre choisi par
     * le commerçant (sort_order), sans les catégories désactivées côté
     * back-office même si show_on_store est resté coché.
     */
    public function scopeOnStore($query)
    {
        return $query->where('show_on_store', true)->where('is_active', true)->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path) : null;
    }

    // ─── Mutators ────────────────────────────────────────────

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;

        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}
