<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Réglages de la boutique en ligne — table singleton (une seule ligne,
 * id=1), même principe que `entreprise` (voir App\Models\Entreprise).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_store_settings', function (Blueprint $table) {
            $table->id();

            // ── Statut ────────────────────────────────────────────
            $table->string('status', 30)->default('disabled')->comment('active | disabled | temporarily_closed');

            // ── Informations générales ──────────────────────────────
            $table->string('name', 150)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('slogan', 191)->nullable();
            $table->text('description')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 191)->nullable();
            $table->json('opening_hours')->nullable();

            // ── Apparence ────────────────────────────────────────────
            $table->string('primary_color', 7)->nullable();
            $table->string('secondary_color', 7)->nullable();
            $table->string('navbar_color', 7)->nullable();
            $table->string('button_color', 7)->nullable();
            $table->string('link_color', 7)->nullable();
            $table->string('footer_color', 7)->nullable();

            // ── Bannière principale (page d'accueil) ────────────────
            $table->string('hero_image_path')->nullable();
            $table->string('hero_title', 191)->nullable();
            $table->string('hero_subtitle', 255)->nullable();
            $table->string('hero_button_label', 100)->nullable();
            $table->string('hero_button_url', 255)->nullable();

            // ── SEO par défaut ───────────────────────────────────────
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('og_image_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_store_settings');
    }
};
