<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single table for all translatable dynamic content (Product, Page, etc.).
     * Normalized, scalable, CodeCanyon-friendly.
     */
    public function up(): void
    {
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type');
            $table->unsignedBigInteger('translatable_id');
            $table->string('language_code', 10);
            $table->string('field', 64);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(
                ['translatable_type', 'translatable_id', 'language_code', 'field'],
                'content_translations_unique'
            );
            $table->index(['translatable_type', 'translatable_id']);
            $table->index('language_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
