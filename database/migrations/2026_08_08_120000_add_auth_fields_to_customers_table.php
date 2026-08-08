<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rend `Customer` authentifiable (Phase 3 : comptes clients). Index uniques
 * ajoutés sans risque : vérifié au préalable, aucun doublon d'email ni de
 * téléphone en base (MySQL autorise plusieurs NULL sur un index unique,
 * seules les valeurs non nulles doivent être uniques).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken()->after('password');
            $table->unique('phone');
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropUnique(['email']);
            $table->dropColumn(['password', 'remember_token']);
        });
    }
};
