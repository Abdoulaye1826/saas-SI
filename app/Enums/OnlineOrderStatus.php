<?php

namespace App\Enums;

enum OnlineOrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nouvelle',
            self::Confirmed => 'Confirmée',
            self::Preparing => 'En préparation',
            self::Ready => 'Prête',
            self::Shipped => 'Expédiée',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::New => 'bg-secondary',
            self::Confirmed => 'bg-primary',
            self::Preparing => 'bg-info text-dark',
            self::Ready => 'bg-warning text-dark',
            self::Shipped => 'bg-primary',
            self::Delivered => 'bg-success',
            self::Cancelled => 'bg-danger',
        };
    }

    /**
     * Transitions autorisées depuis ce statut — évite de faire régresser
     * une commande (ex: repasser une commande "Livrée" en "Nouvelle") ou de
     * modifier une commande déjà annulée.
     *
     * @return self[]
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::New => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Preparing, self::Cancelled],
            self::Preparing => [self::Ready, self::Cancelled],
            self::Ready => [self::Shipped, self::Cancelled],
            self::Shipped => [self::Delivered, self::Cancelled],
            self::Delivered, self::Cancelled => [],
        };
    }

    public function isFinal(): bool
    {
        return $this === self::Delivered || $this === self::Cancelled;
    }
}
