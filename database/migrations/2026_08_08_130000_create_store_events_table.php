<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Compteur d'événements simple pour les statistiques e-commerce (page 28
 * du cahier des charges) : page vue / fiche produit consultée / ajout
 * panier. Volontairement minimal — aucune donnée personnelle stockée
 * (pas d'IP, pas d'user-agent, pas d'identifiant visiteur) : ce sont des
 * compteurs d'événements, pas un outil d'analyse comportementale.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->comment('page_view | product_view | cart_add');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_events');
    }
};
