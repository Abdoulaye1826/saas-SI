<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_id')->constrained('gifts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            // Renseigné uniquement pour les produits suivis par IMEI (un
            // IMEI = un appareil, quantity forcée à 1 — même règle que
            // sale_items.product_imei_id).
            $table->foreignId('product_imei_id')->nullable()->constrained('product_imeis')->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            // Valeur informative uniquement (prix de vente au moment du
            // cadeau) — jamais un montant à payer. Voir cahier §6.
            $table->decimal('unit_value', 12, 2)->default(0);

            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_items');
    }
};
