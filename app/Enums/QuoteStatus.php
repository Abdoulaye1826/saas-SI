<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Refused = 'refused';
    case Converted = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Sent => 'Envoyé',
            self::Accepted => 'Accepté',
            self::Refused => 'Refusé',
            self::Converted => 'Converti en vente',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary',
            self::Sent => 'bg-info text-dark',
            self::Accepted => 'bg-success',
            self::Refused => 'bg-danger',
            self::Converted => 'bg-dark',
        };
    }
}
