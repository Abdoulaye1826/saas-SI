<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        // `artisan db:seed` désactive globalement le mass-assignment guarding
        // (Model::unguard()) : sans réactiver le guard ici, Sale::create()
        // dans SaleService essaie d'insérer des clés de commodité
        // (product_id, payment_method...) qui ne sont pas des colonnes de
        // `sales`, normalement filtrées par $fillable en usage normal (hors
        // seed) — ce qui provoque une erreur SQL silencieusement avalée par
        // le try/catch plus bas.
        Model::reguard();

        $customerIds = Customer::pluck('id')->all();
        $products = Product::query()->where('is_active', true)->get(['id', 'sale_price']);
        $userIds = User::pluck('id')->all();

        if (empty($customerIds) || $products->isEmpty() || empty($userIds)) {
            return;
        }

        /** @var SaleService $saleService */
        $saleService = app(SaleService::class);

        $warranties = ['none', '30d', '30d', '3m', '6m', '1y'];
        $paymentMethods = ['wave', 'orange_money', 'cash'];

        $total = 95;

        for ($i = 0; $i < $total; $i++) {
            // Biaise vers des dates récentes : plus de ventes ces dernières
            // semaines que 5 mois plus tôt, pour une courbe de CA croissante
            // plus parlante en démo.
            $daysAgo = (int) round(150 * (mt_rand(0, 1000) / 1000) ** 1.8);
            $date = Carbon::now()->subDays($daysAgo)->setTime(mt_rand(9, 19), mt_rand(0, 59));

            $itemCount = random_int(1, 3);
            $chosenProducts = $products->random(min($itemCount, $products->count()));
            if (! $chosenProducts instanceof \Illuminate\Support\Collection) {
                $chosenProducts = collect([$chosenProducts]);
            }

            $productIds = [];
            $quantities = [];
            $unitPrices = [];
            foreach ($chosenProducts as $product) {
                $productIds[] = $product->id;
                $quantities[] = random_int(1, 2);
                $unitPrices[] = (float) $product->sale_price;
            }

            // ~8% annulées, ~7% brouillon (pas encore finalisées), le reste validé.
            $roll = random_int(1, 100);
            $status = $roll <= 8 ? 'cancelled' : ($roll <= 15 ? 'draft' : 'validated');

            $hasDiscount = random_int(1, 100) <= 15;
            $discount = 0;
            if ($hasDiscount) {
                $subtotal = array_sum(array_map(fn ($q, $p) => $q * $p, $quantities, $unitPrices));
                $discount = round($subtotal * (random_int(3, 10) / 100), -2);
            }

            $isPartialPayment = $status === 'validated' && random_int(1, 100) <= 18;

            $data = [
                'sale_type' => 'vente',
                'customer_id' => random_int(1, 100) <= 78 ? $customerIds[array_rand($customerIds)] : null,
                'status' => $status,
                'discount_amount' => $discount,
                'notes' => null,
                'warranty_duration' => $warranties[array_rand($warranties)],
                'product_id' => $productIds,
                'quantity' => $quantities,
                'unit_price' => $unitPrices,
            ];

            if ($status === 'validated') {
                $data['payment_method'] = $paymentMethods[array_rand($paymentMethods)];
                if ($isPartialPayment) {
                    $subtotal = array_sum(array_map(fn ($q, $p) => $q * $p, $quantities, $unitPrices)) - $discount;
                    $data['amount_given'] = round(max(0, $subtotal) * (random_int(30, 70) / 100), -2);
                }
            }

            $userId = $userIds[array_rand($userIds)];

            try {
                $sale = $saleService->create($data, $userId);
            } catch (\Throwable) {
                // Ignore une combinaison invalide (ex: montant à 0) plutôt que
                // d'interrompre tout le seeding pour une ligne de démo.
                continue;
            }

            $this->backdate($sale, $date);
        }
    }

    private function backdate(Sale $sale, Carbon $date): void
    {
        DB::table('sales')->where('id', $sale->id)->update([
            'sale_date' => $date->toDateString(),
            'sold_at' => $date,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        DB::table('stock_movements')->where('reference', $sale->sale_number)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $invoice = $sale->invoice()->first();
        if ($invoice === null) {
            return;
        }

        DB::table('invoices')->where('id', $invoice->id)->update([
            'issued_at' => $date->toDateString(),
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        DB::table('payments')->where('invoice_id', $invoice->id)->update([
            'paid_at' => $date->toDateString(),
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
