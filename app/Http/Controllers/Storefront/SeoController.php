<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StorePage;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Plan de site XML — reprend exactement les mêmes ensembles que ceux
     * affichés publiquement (Product::onStore(), Category::onStore(),
     * StorePage::published()), aucune deuxième liste à maintenir.
     */
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('store.home'), 'priority' => '1.0'],
            ['loc' => route('store.products.index'), 'priority' => '0.9'],
        ]);

        Product::onStore()->get(['id', 'slug', 'updated_at'])->each(function (Product $product) use ($urls) {
            $urls->push([
                'loc' => route('store.products.show', $product),
                'lastmod' => $product->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]);
        });

        Category::onStore()->get(['id', 'slug', 'updated_at'])->each(function (Category $category) use ($urls) {
            $urls->push([
                'loc' => route('store.categories.show', $category),
                'lastmod' => $category->updated_at?->toAtomString(),
                'priority' => '0.7',
            ]);
        });

        StorePage::published()->get(['id', 'slug', 'updated_at'])->each(function (StorePage $page) use ($urls) {
            $urls->push([
                'loc' => route('store.pages.show', $page),
                'lastmod' => $page->updated_at?->toAtomString(),
                'priority' => '0.5',
            ]);
        });

        return response()
            ->view('storefront.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
