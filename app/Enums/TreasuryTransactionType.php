<?php

namespace App\Enums;

enum TreasuryTransactionType: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Entrée',
            self::Out => 'Sortie',
        };
    }
}
