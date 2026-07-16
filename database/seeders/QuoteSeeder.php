<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\User;
use App\Services\QuoteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        // Voir le commentaire équivalent dans SaleSeeder::run().
        Model::reguard();

        $customerIds = Customer::pluck('id')->all();
        $products = Product::query()->where('is_active', true)->get(['id', 'sale_price']);
        $userIds = User::pluck('id')->all();

        if (empty($customerIds) || $products->isEmpty() || empty($userIds)) {
            return;
        }

        /** @var QuoteService $quoteService */
        $quoteService = app(QuoteService::class);

        // Un devis récent a plus de chances d'être encore "en attente"
        // (brouillon/envoyé) ; un devis plus ancien a eu le temps d'être
        // accepté, refusé, ou d'expirer.
        $statusByAge = [
            'recent' => ['draft', 'sent', 'sent', 'accepted'],
            'old' => ['accepted', 'refused', 'sent', 'draft'],
        ];

        $total = 18;

        for ($i = 0; $i < $total; $i++) {
            $daysAgo = random_int(1, 90);
            $date = Carbon::now()->subDays($daysAgo);
            $validUntil = $date->copy()->addDays(random_int(7, 21));

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

            $bucket = $daysAgo <= 21 ? 'recent' : 'old';
            $status = $statusByAge[$bucket][array_rand($statusByAge[$bucket])];

            $data = [
                'customer_id' => $customerIds[array_rand($customerIds)],
                'valid_until' => $validUntil->toDateString(),
                'discount_amount' => 0,
                'status' => $status,
                'notes' => null,
                'product_id' => $productIds,
                'quantity' => $quantities,
                'unit_price' => $unitPrices,
            ];

            $userId = $userIds[array_rand($userIds)];

            try {
                $quote = $quoteService->create($data, $userId);
            } catch (\Throwable) {
                continue;
            }

            DB::table('quotes')->where('id', $quote->id)->update([
                'quote_date' => $date->toDateString(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
