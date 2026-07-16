<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::pluck('id')->all();
        $categories = Category::pluck('id', 'name');

        if ($categories->isEmpty() || empty($suppliers)) {
            return;
        }

        $products = [
            // ── Téléphones ──
            ['category' => 'Téléphones', 'ref' => 'TEL-IP13-128', 'name' => 'iPhone 13 128 Go', 'brand' => 'Apple', 'purchase' => 380000, 'sale' => 450000, 'stock' => 8],
            ['category' => 'Téléphones', 'ref' => 'TEL-IP12-64', 'name' => 'iPhone 12 64 Go', 'brand' => 'Apple', 'purchase' => 290000, 'sale' => 350000, 'stock' => 6],
            ['category' => 'Téléphones', 'ref' => 'TEL-SGA54', 'name' => 'Samsung Galaxy A54', 'brand' => 'Samsung', 'purchase' => 150000, 'sale' => 185000, 'stock' => 12],
            ['category' => 'Téléphones', 'ref' => 'TEL-SGS23', 'name' => 'Samsung Galaxy S23', 'brand' => 'Samsung', 'purchase' => 360000, 'sale' => 425000, 'stock' => 5],
            ['category' => 'Téléphones', 'ref' => 'TEL-TECCAM20', 'name' => 'Tecno Camon 20', 'brand' => 'Tecno', 'purchase' => 75000, 'sale' => 95000, 'stock' => 18],
            ['category' => 'Téléphones', 'ref' => 'TEL-INFHOT40', 'name' => 'Infinix Hot 40', 'brand' => 'Infinix', 'purchase' => 60000, 'sale' => 78000, 'stock' => 20],
            ['category' => 'Téléphones', 'ref' => 'TEL-XIARN12', 'name' => 'Xiaomi Redmi Note 12', 'brand' => 'Xiaomi', 'purchase' => 88000, 'sale' => 110000, 'stock' => 14],

            // ── Ordinateurs & Tablettes ──
            ['category' => 'Ordinateurs & Tablettes', 'ref' => 'TAB-IPAD10', 'name' => 'iPad 10ᵉ génération', 'brand' => 'Apple', 'purchase' => 270000, 'sale' => 320000, 'stock' => 5],
            ['category' => 'Ordinateurs & Tablettes', 'ref' => 'TAB-SGTA9', 'name' => 'Samsung Galaxy Tab A9', 'brand' => 'Samsung', 'purchase' => 110000, 'sale' => 140000, 'stock' => 7],
            ['category' => 'Ordinateurs & Tablettes', 'ref' => 'PC-HPPAV15', 'name' => 'HP Pavilion 15" i5', 'brand' => 'HP', 'purchase' => 320000, 'sale' => 385000, 'stock' => 4],

            // ── Consoles & Jeux vidéo ──
            ['category' => 'Consoles & Jeux vidéo', 'ref' => 'CON-PS5-STD', 'name' => 'PlayStation 5 Standard', 'brand' => 'Sony', 'purchase' => 390000, 'sale' => 450000, 'stock' => 6],
            ['category' => 'Consoles & Jeux vidéo', 'ref' => 'CON-PS4-SLIM', 'name' => 'PlayStation 4 Slim', 'brand' => 'Sony', 'purchase' => 145000, 'sale' => 180000, 'stock' => 9],
            ['category' => 'Consoles & Jeux vidéo', 'ref' => 'CON-XBXS', 'name' => 'Xbox Series S', 'brand' => 'Microsoft', 'purchase' => 230000, 'sale' => 280000, 'stock' => 5],
            ['category' => 'Consoles & Jeux vidéo', 'ref' => 'ACC-DUALSENSE', 'name' => 'Manette PS5 DualSense', 'brand' => 'Sony', 'purchase' => 35000, 'sale' => 45000, 'stock' => 22],

            // ── Accessoires ──
            ['category' => 'Accessoires', 'ref' => 'ACC-CHGUSBC', 'name' => 'Chargeur rapide USB-C 25W', 'brand' => 'Générique', 'purchase' => 3000, 'sale' => 5000, 'stock' => 45],
            ['category' => 'Accessoires', 'ref' => 'ACC-CBLIGHT', 'name' => 'Câble Lightning 1m', 'brand' => 'Générique', 'purchase' => 2000, 'sale' => 3500, 'stock' => 60],
            ['category' => 'Accessoires', 'ref' => 'ACC-PWB20K', 'name' => 'Power bank 20 000 mAh', 'brand' => 'Anker', 'purchase' => 8500, 'sale' => 12000, 'stock' => 25],
            ['category' => 'Accessoires', 'ref' => 'ACC-COQIP13', 'name' => 'Coque de protection iPhone 13', 'brand' => 'Générique', 'purchase' => 2000, 'sale' => 4000, 'stock' => 40],

            // ── Audio & Son ──
            ['category' => 'Audio & Son', 'ref' => 'AUD-JBLBT', 'name' => 'Écouteurs Bluetooth JBL', 'brand' => 'JBL', 'purchase' => 13000, 'sale' => 18000, 'stock' => 20],
            ['category' => 'Audio & Son', 'ref' => 'AUD-ENCBT', 'name' => 'Enceinte Bluetooth portable', 'brand' => 'JBL', 'purchase' => 16000, 'sale' => 22000, 'stock' => 15],
            ['category' => 'Audio & Son', 'ref' => 'AUD-SONYWH', 'name' => 'Casque audio Sony WH-CH520', 'brand' => 'Sony', 'purchase' => 27000, 'sale' => 35000, 'stock' => 10],

            // ── Objets connectés ──
            ['category' => 'Objets connectés', 'ref' => 'OBJ-SMARTW', 'name' => 'Montre connectée Smartwatch', 'brand' => 'Générique', 'purchase' => 20000, 'sale' => 28000, 'stock' => 16],
        ];

        foreach ($products as $p) {
            $categoryId = $categories[$p['category']] ?? null;
            if ($categoryId === null) {
                continue;
            }

            Product::firstOrCreate(
                ['reference' => $p['ref']],
                [
                    'category_id' => $categoryId,
                    'supplier_id' => $suppliers[array_rand($suppliers)],
                    'name' => $p['name'],
                    'brand' => $p['brand'],
                    'purchase_price' => $p['purchase'],
                    'sale_price' => $p['sale'],
                    'stock_quantity' => $p['stock'],
                    'minimum_stock' => 5,
                    'is_active' => true,
                ]
            );
        }
    }
}
