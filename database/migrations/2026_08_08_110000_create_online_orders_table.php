<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Commandes passées depuis la boutique en ligne. Reste indépendante de
 * `sales` tant qu'elle n'est pas confirmée par un membre du staff : c'est
 * cette confirmation qui crée la Vente (stock décrémenté, facture générée),
 * jamais la commande elle-même (sales.user_id est NOT NULL — aucun
 * utilisateur staff n'existe encore au moment où un visiteur commande).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();

            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();

            $table->string('status', 20)->default('new')->comment('new|confirmed|preparing|ready|shipped|delivered|cancelled');

            // ── Client (achat invité — voir Customer, pas de compte) ────
            $table->string('guest_name', 150);
            $table->string('guest_phone', 50);
            $table->string('guest_email', 150)->nullable();

            // ── Livraison ────────────────────────────────────────────
            $table->string('delivery_method', 20)->default('home')->comment('home|pickup');
            $table->string('delivery_address', 255)->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_zone', 20)->nullable()->comment('dakar|other|pickup');
            $table->decimal('delivery_fee', 12, 2)->default(0);

            // ── Paiement ─────────────────────────────────────────────
            $table->string('payment_method', 30)->default('cash_on_delivery');

            // ── Montants ─────────────────────────────────────────────
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_orders');
    }
};
