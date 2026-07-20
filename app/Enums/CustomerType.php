<?php

namespace App\Enums;

enum CustomerType: string
{
    case Client = 'client';
    case Revendeur = 'revendeur';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Revendeur => 'Revendeur',
        };
    }
}
