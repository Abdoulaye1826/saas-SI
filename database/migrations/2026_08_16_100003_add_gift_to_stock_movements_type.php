<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Élargit l'enum stock_movements.type avec 'gift' (sortie de stock pour un
 * produit offert) — Laravel n'a pas d'API portable pour modifier un enum
 * MySQL existant, d'où l'ALTER TABLE en SQL brut (même approche que les
 * autres évolutions d'enum de ce projet).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE stock_movements MODIFY type ENUM('entry','exit','adjustment','sale','return','gift') ".
            "COMMENT 'entry=entrée, exit=sortie, adjustment=inventaire, sale=vente, return=annulation, gift=cadeau'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE stock_movements MODIFY type ENUM('entry','exit','adjustment','sale','return') ".
            "COMMENT 'entry=entrée, exit=sortie, adjustment=inventaire, sale=vente, return=annulation'"
        );
    }
};
