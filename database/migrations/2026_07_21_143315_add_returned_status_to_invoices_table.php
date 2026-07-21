<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Élargit la colonne enum "status" pour accepter "returned" — voir
     * 2026_06_26_000001_create_payments_table.php pour le même pattern
     * (ajout de "partial").
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status ENUM('issued', 'partial', 'paid', 'returned', 'cancelled') NOT NULL DEFAULT 'issued' COMMENT 'Statut de la facture (recalculé automatiquement selon les paiements et les retours)'");
        }
        // SQLite stocke déjà "status" en colonne string libre depuis la
        // migration des paiements (pas de contrainte enum à élargir).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status ENUM('issued', 'partial', 'paid', 'cancelled') NOT NULL DEFAULT 'issued'");
        }
    }
};
