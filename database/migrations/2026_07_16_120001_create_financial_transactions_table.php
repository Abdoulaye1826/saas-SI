<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : grand livre de trésorerie (entrées et sorties d'argent
 * unifiées, distinguées par la colonne "type" — même principe que
 * stock_movements pour le stock).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->string('category', 50);
            $table->decimal('amount', 14, 2);
            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            // Relie une écriture générée automatiquement (vente, avance, virement...)
            // à l'enregistrement qui l'a créée, pour remonter au document d'origine.
            $table->nullableMorphs('related');
            $table->string('attachment_path')->nullable()->comment('Justificatif : PDF/image de facture ou reçu.');
            $table->boolean('is_auto_generated')->default(false);
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('category');
            $table->index('date');
            $table->index('is_auto_generated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
