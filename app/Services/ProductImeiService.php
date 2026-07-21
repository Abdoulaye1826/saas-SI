<?php

namespace App\Services;

use App\Enums\ImeiStatus;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\ProductImei;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * Gestion des IMEI d'un produit suivi unité par unité (téléphones).
 * Le stock du produit est toujours recalculé à partir du nombre d'IMEI
 * disponibles — aucune incohérence possible entre stock et IMEI.
 */
class ProductImeiService
{
    public function __construct(private readonly ActivityLogService $activityLog)
    {
    }

    /**
     * Ajoute un ou plusieurs IMEI au stock d'un produit (saisie manuelle ou
     * scan douchette — fonctionnellement identique, un IMEI scanné est juste
     * un IMEI saisi rapidement dans le même champ texte).
     *
     * @param  string[]  $imeis
     * @return ProductImei[]
     */
    public function store(Product $product, array $imeis): array
    {
        $imeis = array_values(array_unique(array_filter(array_map('trim', $imeis))));

        if (empty($imeis)) {
            throw new \RuntimeException('Veuillez saisir au moins un IMEI.');
        }

        foreach ($imeis as $imei) {
            if (!preg_match('/^\d{14,17}$/', $imei)) {
                throw new \RuntimeException("L'IMEI « {$imei} » est invalide (14 à 17 chiffres attendus).");
            }
        }

        $existing = ProductImei::whereIn('imei', $imeis)->pluck('imei');
        if ($existing->isNotEmpty()) {
            throw new \RuntimeException(
                'IMEI déjà enregistré : ' . $existing->implode(', ') . '. Chaque IMEI doit être unique.'
            );
        }

        return DB::transaction(function () use ($product, $imeis) {
            $created = [];
            // Le vrai nombre d'IMEI disponibles avant l'ajout — pas
            // $product->stock_quantity, qui peut être périmée (ex : produit
            // dont le suivi IMEI vient d'être activé sans encore avoir
            // d'IMEI enregistré, stock_quantity conservant l'ancienne
            // valeur saisie manuellement). Sans ça, le journal de
            // mouvements affichait un before/after incohérent avec le
            // stock réellement synchronisé par syncImeiStock() ensuite.
            $quantityBefore = $product->imeis()->available()->count();

            foreach ($imeis as $imei) {
                $created[] = ProductImei::create([
                    'product_id' => $product->id,
                    'imei' => $imei,
                    'status' => ImeiStatus::Available,
                ]);
            }

            $product->syncImeiStock();

            // Un mouvement par IMEI ajouté, pour rester cohérent avec la
            // traçabilité unité par unité utilisée pour les ventes/retours.
            foreach ($created as $index => $imeiModel) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => StockMovementType::Entry,
                    'quantity' => 1,
                    'quantity_before' => $quantityBefore + $index,
                    'quantity_after' => $quantityBefore + $index + 1,
                    'reason' => "Ajout IMEI {$imeiModel->imei}",
                ]);
            }

            $this->activityLog->log(
                'create',
                $product,
                count($created) . " IMEI ajouté(s) au stock de {$product->name}"
            );

            return $created;
        });
    }

    public function destroy(ProductImei $imei): void
    {
        if ($imei->status !== ImeiStatus::Available) {
            throw new \RuntimeException("Impossible de supprimer un IMEI {$imei->status->label()}.");
        }

        DB::transaction(function () use ($imei) {
            $product = $imei->product;
            $imeiValue = $imei->imei;
            // Voir le commentaire équivalent dans store() : le vrai nombre
            // d'IMEI disponibles avant le retrait, pas stock_quantity qui
            // peut être périmée.
            $quantityBefore = $product->imeis()->available()->count();

            $imei->delete();
            $product->syncImeiStock();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => StockMovementType::Exit,
                'quantity' => 1,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $product->fresh()->stock_quantity,
                'reason' => "Retrait IMEI {$imeiValue}",
            ]);

            $this->activityLog->log('delete', null, "IMEI supprimé : {$imeiValue} ({$product->name})");
        });
    }
}
