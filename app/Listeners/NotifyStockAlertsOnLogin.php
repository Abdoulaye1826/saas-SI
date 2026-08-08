<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\StockAlertNotificationService;
use Illuminate\Auth\Events\Login;

/**
 * Notifie l'utilisateur des alertes stock (rupture / stock faible) dès sa
 * connexion, au plus une fois par jour pour ne pas le spammer.
 *
 * L'événement Login est générique à tous les guards : depuis la Phase 3
 * (comptes clients, guard `customer`), $event->user peut être un
 * App\Models\Customer — les alertes stock sont une notion strictement
 * staff, on ignore silencieusement tout ce qui n'est pas un User.
 */
class NotifyStockAlertsOnLogin
{
    public function __construct(
        private readonly StockAlertNotificationService $stockAlertNotificationService
    ) {
    }

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->stockAlertNotificationService->notifyIfNeeded($event->user);
    }
}
