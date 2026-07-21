<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Issued = 'issued';
    case Partial = 'partial';
    case Paid = 'paid';
    case PartiallyReturned = 'partially_returned';
    case Returned = 'returned';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Issued => 'Non payé',
            self::Partial => 'Partiellement payée',
            self::Paid => 'Payée',
            self::PartiallyReturned => 'Partiellement retourné',
            self::Returned => 'Retourné',
            self::Cancelled => 'Annulée',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Issued => 'bg-secondary',
            self::Partial => 'bg-warning text-dark',
            self::Paid => 'bg-success',
            self::PartiallyReturned => 'bg-info text-dark',
            self::Returned => 'bg-dark',
            self::Cancelled => 'bg-danger',
        };
    }
}
