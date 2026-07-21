<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Élargit la colonne enum "status" pour accepter "partially_returned"
     * (retour partiel, distinct de "returned" = retour complet) — voir
     * 2026_07_21_143315_add_returned_status_to_invoices_table.php pour le
     * même pattern.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status ENUM('issued', 'partial', 'paid', 'partially_returned', 'returned', 'cancelled') NOT NULL DEFAULT 'issued' COMMENT 'Statut de la facture (recalculé automatiquement selon les paiements et les retours)'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY status ENUM('issued', 'partial', 'paid', 'returned', 'cancelled') NOT NULL DEFAULT 'issued'");
        }
    }
};
