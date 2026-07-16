<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestrateur principal des seeders.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            SaleSeeder::class,
            QuoteSeeder::class,
            EntrepriseSeeder::class,
        ]);
    }
}
