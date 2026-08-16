<?php

namespace App\Enums;

enum ImeiStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Offered = 'offered';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Reserved => 'Réservé',
            self::Sold => 'Vendu',
            self::Offered => 'Offert',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'bg-success',
            self::Reserved => 'bg-warning text-dark',
            self::Sold => 'bg-secondary',
            self::Offered => 'bg-info text-dark',
        };
    }
}
