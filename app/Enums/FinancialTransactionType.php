<?php

namespace App\Enums;

enum FinancialTransactionType: string
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

    public function badgeClass(): string
    {
        return match ($this) {
            self::In => 'bg-success',
            self::Out => 'bg-danger',
        };
    }
}
