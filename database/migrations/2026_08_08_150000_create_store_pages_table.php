<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 191);
            $table->text('content')->nullable();
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('show_in_footer')->default(true);
            $table->timestamps();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_pages');
    }
};
