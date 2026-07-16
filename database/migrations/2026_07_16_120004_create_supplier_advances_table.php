<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : avances versées à un fournisseur, historisées séparément
 * des sorties de trésorerie classiques.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_used', 14, 2)->default(0);
            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->text('observation')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_advances');
    }
};
