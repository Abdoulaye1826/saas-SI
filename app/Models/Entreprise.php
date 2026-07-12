<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Table singleton (une seule ligne, id=1) : les informations propres à
 * l'entreprise cliente de ce déploiement (nom, logo, coordonnées, mentions
 * légales), modifiables depuis le panneau admin plutôt que codées en dur.
 */
class Entreprise extends Model
{
    protected $table = 'entreprise';

    protected $fillable = [
        'name', 'legal_name', 'logo_path', 'email', 'phone',
        'whatsapp_number', 'address_line1', 'address_line2',
        'city', 'country', 'ninea', 'rccm', 'website',
        'currency', 'invoice_footer_note', 'accent_color',
    ];

    public const CACHE_KEY = 'entreprise.settings';

    public static function current(): self
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()->firstOrCreate(['id' => 1], ['name' => 'Mon Entreprise']);
            });
        } catch (\Throwable) {
            // Composer de vue global (§8) : appelé sur CHAQUE page, y compris
            // les pages d'erreur. Si la base est inaccessible ou la table pas
            // encore migrée, on retombe sur une instance non persistée plutôt
            // que de casser l'affichage de la page d'erreur elle-même.
            return new self(['name' => 'Mon Entreprise']);
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

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    /**
     * DomPDF ne charge pas les images distantes par défaut
     * (dompdf.enable_remote = false) : les documents PDF ont besoin du
     * logo encodé en base64 plutôt que d'une URL.
     */
    public function getLogoBase64Attribute(): string
    {
        $disk = Storage::disk('public');

        if ($this->logo_path && $disk->exists($this->logo_path)) {
            $mime = $disk->mimeType($this->logo_path) ?: 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode($disk->get($this->logo_path));
        }

        $default = public_path('images/logo.jpeg');

        return is_file($default)
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($default))
            : '';
    }
}
