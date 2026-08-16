<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Élargit l'enum product_imeis.status avec 'offered' (IMEI sorti du stock
 * via un cadeau, distinct de 'sold' — cahier §5 : « l'IMEI est marqué
 * comme sorti/offert »).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE product_imeis MODIFY status ENUM('available','reserved','sold','offered') DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE product_imeis MODIFY status ENUM('available','reserved','sold') DEFAULT 'available'");
    }
};
