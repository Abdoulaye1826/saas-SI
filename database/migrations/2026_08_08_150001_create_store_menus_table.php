<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Éléments de menu personnalisés, affichés à la suite des liens fixes
 * (Accueil/Boutique) et des catégories déjà générées dynamiquement dans
 * la nav publique (voir storefront/partials/nav.blade.php) — ne
 * remplacent pas les liens de catégorie, viennent s'y ajouter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_menus', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            $table->string('url', 255)->comment('Relatif (/boutique/page/xxx) ou absolu (https://...)');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('opens_new_tab')->default(false);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_menus');
    }
};
