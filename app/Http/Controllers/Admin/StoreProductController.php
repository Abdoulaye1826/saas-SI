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
 * des interrupteurs (présence en ligne, vedette, nouveauté) sans passer par
 * la fiche produit complète. Réutilise ProductService::paginate()
 * (Product/produits.index) — pas de deuxième liste de produits à maintenir.
 */
class StoreProductController extends Controller
{
    /**
     * Bascules disponibles depuis cette liste rapide → libellés utilisés
     * dans les messages de confirmation. "Promotion" est volontairement
     * absente : contrairement aux autres, activer is_promo sans saisir de
     * promo_price n'a aucun effet visible côté boutique (voir
     * Product::effectivePrice()) — ça reste réservé à la fiche produit
     * complète, où le prix promo se saisit au même endroit.
     */
    private const TOGGLABLE_FIELDS = [
        'show_on_store' => 'la présence en ligne',
        'is_featured' => 'le statut vedette',
        'is_new' => 'le statut nouveauté',
    ];

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

    public function toggle(Product $product, string $field): RedirectResponse
    {
        abort_unless(array_key_exists($field, self::TOGGLABLE_FIELDS), 404);

        $product->update([$field => ! $product->{$field}]);

        if ($field === 'show_on_store' && $product->show_on_store && ! $product->is_active) {
            return back()->with('error', "« {$product->name} » sera visible en ligne dès qu'il sera aussi marqué actif (fiche produit).");
        }

        $label = self::TOGGLABLE_FIELDS[$field];

        return back()->with('success', $product->{$field}
            ? "« {$product->name} » — {$label} activé(e)."
            : "« {$product->name} » — {$label} désactivé(e).");
    }
}
