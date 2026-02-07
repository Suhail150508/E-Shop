<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Virtual Try-On (Preview On Me): only show try-on for products with this flag.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_tryable')) {
                $table->boolean('is_tryable')->default(false)->after('is_flash_sale');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_tryable');
        });
    }
};
