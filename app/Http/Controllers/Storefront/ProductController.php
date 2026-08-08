<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Catalogue public (recherche/filtres) et fiche produit.
 *
 * IMPORTANT : ne charge/n'expose jamais les IMEI (App\Models\ProductImei).
 * `show_stock` ne contrôle que l'affichage d'un compteur de quantité,
 * jamais le détail unité par unité.
 */
class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'availability', 'is_new', 'is_promo', 'min_price', 'max_price']);

        $products = Product::query()
            ->with('category')
            ->onStore()
            ->search($filters['search'] ?? null)
            ->storeFilter($filters)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::onStore()->get();

        return view('storefront.products.index', compact('products', 'categories', 'filters'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->show_on_store && $product->is_active, 404);

        $product->load('category');

        $related = Product::onStore()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('storefront.products.show', compact('product', 'related'));
    }
}
