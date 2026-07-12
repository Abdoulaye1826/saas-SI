<?php

use App\Models\Entreprise;

if (! function_exists('entreprise')) {
    /**
     * Informations de l'entreprise cliente de ce déploiement (nom, logo,
     * coordonnées), utilisables en dehors des vues Blade (Mail, Services).
     */
    function entreprise(): Entreprise
    {
        return Entreprise::current();
    }
}
