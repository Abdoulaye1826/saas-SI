<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vue rapide "Boutique en ligne > Produits" : liste tous les produits avec
 * un interrupteur pour activer/désactiver leur présence sur la boutique
 * (show_on_store) sans passer par la fiche produit complète. Réutilise
 * ProductService::paginate() (Product/produits.index) — pas de deuxième
 * liste de produits à maintenir.
 */
class StoreProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'show_on_store']);
        $products = $this->productService->paginate($filters, 20);
        $categories = $this->categoryService->activeList();

        return view('admin.store.products', compact('products', 'categories', 'filters'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['show_on_store' => ! $product->show_on_store]);

        if ($product->show_on_store && ! $product->is_active) {
            return back()->with('error', "« {$product->name} » sera visible en ligne dès qu'il sera aussi marqué actif (fiche produit).");
        }

        return back()->with('success', $product->show_on_store
            ? "« {$product->name} » est maintenant visible sur la boutique."
            : "« {$product->name} » a été retiré de la boutique.");
    }
}
