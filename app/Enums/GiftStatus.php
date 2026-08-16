<?php

namespace App\Enums;

enum GiftStatus: string
{
    case Given = 'given';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Given => 'Offert',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Given => 'bg-success',
            self::Cancelled => 'bg-secondary',
        };
    }
}
