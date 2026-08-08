<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_store_settings', function (Blueprint $table) {
            $table->boolean('delivery_enabled')->default(true)->after('og_image_path');
            $table->boolean('pickup_enabled')->default(true)->after('delivery_enabled');
            $table->decimal('delivery_fee_dakar', 12, 2)->default(2000)->after('pickup_enabled');
            $table->decimal('delivery_fee_other', 12, 2)->default(3000)->after('delivery_fee_dakar');
            $table->decimal('free_delivery_threshold', 12, 2)->nullable()->after('delivery_fee_other');
        });
    }

    public function down(): void
    {
        Schema::table('online_store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_enabled', 'pickup_enabled', 'delivery_fee_dakar',
                'delivery_fee_other', 'free_delivery_threshold',
            ]);
        });
    }
};
