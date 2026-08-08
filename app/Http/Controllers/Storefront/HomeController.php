<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OnlineStoreSettings;
use App\Models\Product;
use Illuminate\View\View;

/**
 * Page d'accueil de la boutique publique : bannière, produits vedettes,
 * nouveautés, promotions, catégories — tout est lu directement depuis les
 * modèles du back-office (Product, Category, OnlineStoreSettings), aucune
 * duplication de données.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        $settings = OnlineStoreSettings::current();

        $featuredProducts = Product::onStore()->featured()->latest()->limit(8)->get();
        $newProducts = Product::onStore()->new()->latest()->limit(8)->get();
        $promoProducts = Product::onStore()->promo()->latest()->limit(8)->get();
        $categories = Category::onStore()->withCount(['products' => fn ($q) => $q->onStore()])->get();

        return view('storefront.home', compact('settings', 'featuredProducts', 'newProducts', 'promoProducts', 'categories'));
    }
}
