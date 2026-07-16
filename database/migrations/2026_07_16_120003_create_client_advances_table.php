<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : avances versées par un client avant une vente, utilisables
 * plus tard pour régler une facture.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_used', 14, 2)->default(0);
            $table->date('date');
            $table->enum('payment_method', ['wave', 'orange_money', 'cash']);
            $table->string('reference', 100)->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_advances');
    }
};
