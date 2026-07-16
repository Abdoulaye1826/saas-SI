<?php

namespace Database\Seeders;

use App\Models\FinancialAccount;
use Illuminate\Database\Seeder;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Comptes types, avec le mode de paiement associé pour Wave/Orange
     * Money/Espèces — indispensable pour que le mapping automatique
     * paiement -> compte fonctionne dès l'installation.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Caisse principale', 'type' => 'cash', 'payment_method' => 'cash'],
            ['name' => 'Banque', 'type' => 'bank', 'payment_method' => null],
            ['name' => 'Wave', 'type' => 'mobile_money', 'payment_method' => 'wave'],
            ['name' => 'Orange Money', 'type' => 'mobile_money', 'payment_method' => 'orange_money'],
            ['name' => 'Caisse secondaire', 'type' => 'cash', 'payment_method' => null],
        ];

        foreach ($accounts as $account) {
            FinancialAccount::firstOrCreate(
                ['name' => $account['name']],
                $account + ['current_balance' => 0, 'is_active' => true]
            );
        }
    }
}
