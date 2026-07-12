<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprise', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('legal_name', 191)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('address_line1', 191)->nullable();
            $table->string('address_line2', 191)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Sénégal');
            $table->string('ninea', 50)->nullable();
            $table->string('rccm', 100)->nullable();
            $table->string('website', 191)->nullable();
            $table->string('currency', 10)->default('XOF');
            $table->text('invoice_footer_note')->nullable();
            $table->string('accent_color', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprise');
    }
};
