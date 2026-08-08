<?php

namespace App\Models;

use App\Enums\StoreStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Table singleton (une seule ligne, id=1) : réglages de la boutique en
 * ligne (statut, informations générales, apparence, bannière d'accueil,
 * SEO par défaut), modifiables depuis le back-office. Même patron que
 * App\Models\Entreprise (cache + invalidation sur saved/deleted).
 */
class OnlineStoreSettings extends Model
{
    protected $table = 'online_store_settings';

    protected $fillable = [
        'status',
        'name', 'logo_path', 'favicon_path', 'slogan', 'description',
        'phone', 'whatsapp_number', 'email', 'address', 'opening_hours',
        'primary_color', 'secondary_color', 'navbar_color', 'button_color',
        'link_color', 'footer_color',
        'hero_image_path', 'hero_title', 'hero_subtitle', 'hero_button_label', 'hero_button_url',
        'meta_title', 'meta_description', 'og_image_path',
    ];

    protected $casts = [
        'status' => StoreStatus::class,
        'opening_hours' => 'array',
    ];

    public const CACHE_KEY = 'online_store.settings';

    public const DEFAULT_PRIMARY_COLOR = '#153BFF';
    public const DEFAULT_SECONDARY_COLOR = '#0F172A';
    public const DEFAULT_NAVBAR_COLOR = '#0F172A';
    public const DEFAULT_BUTTON_COLOR = '#153BFF';
    public const DEFAULT_LINK_COLOR = '#153BFF';
    public const DEFAULT_FOOTER_COLOR = '#0F172A';

    public static function current(): self
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()->firstOrCreate(['id' => 1], ['status' => StoreStatus::Disabled->value]);
            });
        } catch (\Throwable) {
            // Même garde-fou que Entreprise::current() : si la base est
            // inaccessible ou la table pas encore migrée, on retombe sur une
            // instance non persistée plutôt que de casser l'affichage.
            return new self(['status' => StoreStatus::Disabled]);
        }
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::forgetCache());
        static::deleted(fn () => self::forgetCache());
    }

    public function isOpen(): bool
    {
        return $this->status instanceof StoreStatus && $this->status->isOpen();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image_path ? Storage::disk('public')->url($this->hero_image_path) : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image_path ? Storage::disk('public')->url($this->og_image_path) : null;
    }
}
