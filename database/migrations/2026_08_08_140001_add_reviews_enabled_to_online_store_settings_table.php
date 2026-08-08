<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_store_settings', function (Blueprint $table) {
            $table->boolean('reviews_enabled')->default(true)->after('free_delivery_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('online_store_settings', function (Blueprint $table) {
            $table->dropColumn('reviews_enabled');
        });
    }
};
