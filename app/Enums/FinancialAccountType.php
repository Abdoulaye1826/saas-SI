<?php

namespace App\Enums;

enum FinancialAccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case MobileMoney = 'mobile_money';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Caisse',
            self::Bank => 'Banque',
            self::MobileMoney => 'Mobile Money',
            self::Other => 'Autre',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'bi-cash-stack',
            self::Bank => 'bi-bank',
            self::MobileMoney => 'bi-phone',
            self::Other => 'bi-wallet2',
        };
    }
}
