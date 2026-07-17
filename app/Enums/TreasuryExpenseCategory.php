<?php

namespace App\Enums;

enum TreasuryExpenseCategory: string
{
    case AchatMarchandises = 'achat_marchandises';
    case Loyer = 'loyer';
    case Electricite = 'electricite';
    case Eau = 'eau';
    case Internet = 'internet';
    case Transport = 'transport';
    case Salaire = 'salaire';
    case Divers = 'divers';

    public function label(): string
    {
        return match ($this) {
            self::AchatMarchandises => 'Achat marchandises',
            self::Loyer => 'Loyer',
            self::Electricite => 'Électricité',
            self::Eau => 'Eau',
            self::Internet => 'Internet',
            self::Transport => 'Transport',
            self::Salaire => 'Salaire',
            self::Divers => 'Divers',
        };
    }
}
