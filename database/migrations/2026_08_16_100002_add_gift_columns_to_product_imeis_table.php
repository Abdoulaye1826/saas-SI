<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un IMEI offert en cadeau doit rester traçable (cahier §5), au même
 * titre qu'un IMEI vendu (sale_id) ou reçu en échange (exchange_sale_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_imeis', 'gift_id')) {
            Schema::table('product_imeis', function (Blueprint $table) {
                $table->foreignId('gift_id')->nullable()->after('exchange_sale_id')->constrained('gifts')->nullOnDelete();
                $table->timestamp('given_at')->nullable()->after('sold_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_imeis', 'gift_id')) {
            Schema::table('product_imeis', function (Blueprint $table) {
                $table->dropConstrainedForeignId('gift_id');
                $table->dropColumn('given_at');
            });
        }
    }
};
