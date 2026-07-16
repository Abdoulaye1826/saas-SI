<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : relie un paiement à l'avance client qui l'a financé, pour
 * éviter de compter deux fois la même entrée de trésorerie (l'argent est
 * déjà rentré au moment de l'avance, pas au moment où elle règle la
 * facture).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('client_advance_id')->nullable()->after('invoice_id')
                ->constrained('client_advances')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_advance_id');
        });
    }
};
