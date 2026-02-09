<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds weight and dimensions for product physical attributes (shipping, specs).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('weight', 50)->nullable()->after('stock');
            $table->string('dimensions', 100)->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'dimensions']);
        });
    }
};
