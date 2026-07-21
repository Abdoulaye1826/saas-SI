<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Logique métier du catalogue produits.
 */
class ProductService
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $allowedSorts = ['name', 'reference', 'sale_price', 'stock_quantity', 'created_at'];
        $sort = in_array($filters['sort'] ?? '', $allowedSorts, true) ? $filters['sort'] : 'created_at';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return Product::query()
            ->with('category')
            ->search($filters['search'] ?? null)
            ->filter([
                'category_id' => $filters['category_id'] ?? null,
                'brand' => $filters['brand'] ?? null,
                'is_active' => isset($filters['is_active']) && $filters['is_active'] !== ''
                    ? (bool) $filters['is_active']
                    : null,
                'stock_status' => $filters['stock_status'] ?? null,
            ])
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getBrands(): array
    {
        return Product::query()
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->all();
    }

    public function create(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $this->storeImage($image);
        }

        $product = Product::create($data);

        $this->activityLog->log('create', $product, "Produit créé : {$product->name} ({$product->reference})");

        return $product;
    }

    public function update(Product $product, array $data, ?UploadedFile $image = null, bool $removeImage = false): Product
    {
        if ($removeImage && $product->image) {
            $this->deleteImage($product->image);
            $data['image'] = null;
        }

        if ($image) {
            if ($product->image) {
                $this->deleteImage($product->image);
            }
            $data['image'] = $this->storeImage($image);
        }

        $product->update($data);

        // Si le suivi IMEI vient d'être activé (ou l'était déjà), on
        // resynchronise immédiatement le stock sur le nombre réel d'IMEI
        // disponibles — sinon un produit qui passe en suivi IMEI garde
        // affichée son ancienne quantité saisie manuellement jusqu'au
        // premier ajout d'IMEI, ce qui est incohérent avec la règle "le
        // stock d'un produit IMEI est toujours calculé, jamais saisi".
        if ($product->tracks_imei) {
            $product->syncImeiStock();
        }

        $this->activityLog->log('update', $product, "Produit modifié : {$product->name}");

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        if ($product->saleItems()->exists()) {
            throw new \RuntimeException('Impossible de supprimer un produit lié à des ventes.');
        }

        // stock_movements.product_id est en restrictOnDelete() : sans ce
        // garde-fou, supprimer un produit ayant un historique de stock (ou
        // des IMEI enregistrés) faisait remonter une erreur SQL brute
        // (contrainte de clé étrangère) au lieu d'un message compréhensible.
        if ($product->stockMovements()->exists() || $product->imeis()->exists()) {
            throw new \RuntimeException('Impossible de supprimer un produit ayant un historique de stock ou des IMEI enregistrés.');
        }

        $name = $product->name;

        if ($product->image) {
            $this->deleteImage($product->image);
        }

        $product->delete();

        $this->activityLog->log('delete', null, "Produit supprimé : {$name}");
    }

    private function storeImage(UploadedFile $image): string
    {
        return $image->store('products', 'public');
    }

    private function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
