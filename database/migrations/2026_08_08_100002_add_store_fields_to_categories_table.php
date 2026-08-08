<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_on_store')->default(false)->after('is_active');
            $table->string('image_path')->nullable()->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('image_path');

            $table->index('show_on_store');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['show_on_store']);
            $table->dropColumn(['show_on_store', 'image_path', 'sort_order']);
        });
    }
};
