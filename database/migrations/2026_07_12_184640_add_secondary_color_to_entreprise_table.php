<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise', function (Blueprint $table) {
            $table->string('secondary_color', 7)->nullable()->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('entreprise', function (Blueprint $table) {
            $table->dropColumn('secondary_color');
        });
    }
};
