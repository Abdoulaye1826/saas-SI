<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    /**
     * Crée la ligne singleton avec des valeurs génériques neutres. Chaque
     * client renseigne ses propres informations depuis le panneau admin
     * (/admin/entreprise) après déploiement — ce seeder ne doit jamais
     * contenir de données réelles d'un client particulier (numéro de
     * téléphone, NINEA, RCCM, logo...), sous peine de les voir apparaître
     * par défaut chez tous les nouveaux clients.
     */
    public function run(): void
    {
        Entreprise::firstOrCreate(['id' => 1], [
            'name' => 'Mon Entreprise',
            'country' => 'Sénégal',
            'currency' => 'XOF',
            'accent_color' => '#1e3a5f',
        ]);
    }
}
