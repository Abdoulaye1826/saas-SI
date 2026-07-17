<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : module Trésorerie simplifié — un seul grand livre
 * entrées/sorties, pas de comptes multiples ni de virements.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']);
            $table->string('category', 50)->nullable();
            $table->decimal('amount', 14, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('reference', 100)->nullable()->comment('Ex: numéro de facture, pour les entrées générées automatiquement.');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};
