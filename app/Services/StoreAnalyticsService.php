<?php

namespace App\Services;

use App\Models\StoreEvent;

/**
 * Suivi minimal des événements de la boutique publique, pour les
 * statistiques e-commerce du back-office (page Rapports). Volontairement
 * simple : un compteur d'événements, pas un outil d'analyse comportementale
 * (voir la migration create_store_events_table pour le détail des garanties
 * de confidentialité).
 */
class StoreAnalyticsService
{
    public function track(string $type, ?int $productId = null): void
    {
        StoreEvent::create([
            'type' => $type,
            'product_id' => $productId,
            'created_at' => now(),
        ]);
    }
}
