<?php

namespace App\Enums;

/**
 * Statut de la boutique en ligne (App\Models\OnlineStoreSettings::status).
 */
enum StoreStatus: string
{
    case Active = 'active';
    case Disabled = 'disabled';
    case TemporarilyClosed = 'temporarily_closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Boutique active',
            self::Disabled => 'Boutique désactivée',
            self::TemporarilyClosed => 'Fermeture temporaire',
        };
    }

    /**
     * true seulement pour Active : toutes les autres valeurs affichent la
     * page "boutique indisponible" (voir EnsureStoreIsOpen).
     */
    public function isOpen(): bool
    {
        return $this === self::Active;
    }
}
