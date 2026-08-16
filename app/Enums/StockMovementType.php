<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Entry = 'entry';
    case Exit = 'exit';
    case Adjustment = 'adjustment';
    case Sale = 'sale';
    case Return = 'return';
    case Gift = 'gift';

    public function label(): string
    {
        return match ($this) {
            self::Entry => 'Entrée stock',
            self::Exit => 'Sortie stock',
            self::Adjustment => 'Ajustement',
            self::Sale => 'Vente',
            self::Return => 'Retour',
            self::Gift => 'Cadeau',
        };
    }
}
