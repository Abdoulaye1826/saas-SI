<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Champs de publication de la boutique en ligne pour un produit.
 *
 * `is_active` existe déjà et signifie "vendable/visible en caisse" (utilisé
 * par SaleController pour les échanges) : on n'y touche pas. `show_on_store`
 * est un champ distinct, propre à la vitrine publique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_on_store')->default(false)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('show_on_store');
            $table->boolean('is_new')->default(false)->after('is_featured');
            $table->boolean('is_promo')->default(false)->after('is_new');
            $table->decimal('promo_price', 12, 2)->nullable()->after('sale_price');
            $table->boolean('allow_order')->default(true)->after('is_promo');
            $table->boolean('show_stock')->default(true)->after('allow_order');
            $table->string('slug', 220)->nullable()->unique()->after('name');
            $table->string('meta_title', 191)->nullable()->after('description');
            $table->string('meta_description', 255)->nullable()->after('meta_title');

            $table->index('show_on_store');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['show_on_store']);
            $table->dropColumn([
                'show_on_store', 'is_featured', 'is_new', 'is_promo',
                'promo_price', 'allow_order', 'show_stock', 'slug',
                'meta_title', 'meta_description',
            ]);
        });
    }
};
