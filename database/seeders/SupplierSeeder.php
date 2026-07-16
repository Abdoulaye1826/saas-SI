<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Teranga Distribution', 'phone' => '+221 33 821 45 12', 'email' => 'contact@teranga-distribution.sn', 'address' => 'Zone Industrielle, Route de Rufisque', 'country' => 'Sénégal'],
            ['name' => 'Sahel Import-Export', 'phone' => '+221 33 864 30 27', 'email' => 'ventes@sahel-import.sn', 'address' => 'Avenue Cheikh Anta Diop, Dakar', 'country' => 'Sénégal'],
            ['name' => 'Baobab Trading SARL', 'phone' => '+221 77 456 12 89', 'email' => 'contact@baobabtrading.sn', 'address' => 'Marché Sandaga, Plateau', 'country' => 'Sénégal'],
            ['name' => 'Dakar Wholesale Tech', 'phone' => '+221 33 842 90 11', 'email' => null, 'address' => 'Liberté 6 Extension, Dakar', 'country' => 'Sénégal'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier + ['is_active' => true]);
        }
    }
}
