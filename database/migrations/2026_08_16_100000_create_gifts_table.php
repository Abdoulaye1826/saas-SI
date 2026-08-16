<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : cadeaux / produits offerts. Table volontairement séparée de
 * `sales` (pas un 3e sale_type) — un cadeau n'est jamais une vente, ne doit
 * jamais pouvoir alimenter le chiffre d'affaires ni la trésorerie. Voir
 * app/Services/GiftService.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('gift_number', 20)->unique()->comment('Ex. BC-000001');
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete()->comment('Client bénéficiaire');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Utilisateur ayant offert le produit');
            $table->dateTime('gift_date');
            $table->enum('status', ['given', 'cancelled'])->default('given');
            $table->text('notes')->nullable()->comment('Remarque (ex. cadeau fidélité client)');
            $table->timestamps();

            $table->index('status');
            $table->index('gift_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
