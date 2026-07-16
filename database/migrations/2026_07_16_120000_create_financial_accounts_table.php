<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : comptes financiers (Caisse, Banque, Wave, Orange Money...)
 * du module Trésorerie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['cash', 'bank', 'mobile_money', 'other'])->default('cash');
            $table->enum('payment_method', ['wave', 'orange_money', 'cash'])->nullable()
                ->comment('Relie ce compte au mode de paiement correspondant, pour le créditer automatiquement à chaque encaissement.');
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('payment_method');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
