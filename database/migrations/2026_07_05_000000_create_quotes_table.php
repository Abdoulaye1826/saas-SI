<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : devis (proposition de prix avant vente, sans impact stock)
 * et leurs lignes de détail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number', 30)->unique()->comment('Numéro de devis auto-généré');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Utilisateur ayant créé le devis');
            $table->date('quote_date');
            $table->date('valid_until')->nullable()->comment('Date de validité du devis');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('Remise globale en montant');
            $table->decimal('subtotal_ht', 12, 2)->default(0)->comment('Total hors taxes');
            $table->decimal('total_ttc', 12, 2)->default(0)->comment('Total toutes taxes comprises');
            $table->enum('status', ['draft', 'sent', 'accepted', 'refused', 'converted'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('converted_sale_id')->nullable()->constrained('sales')->nullOnDelete()
                ->comment('Vente issue de la conversion de ce devis, une fois accepté');
            $table->timestamps();

            $table->index('quote_date');
            $table->index('status');
            $table->index('customer_id');
            $table->index('user_id');
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2)->comment('Prix unitaire proposé au client');
            $table->decimal('line_total', 12, 2)->comment('Total ligne après remise');
            $table->timestamps();

            $table->index('quote_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
