<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('road')->nullable()->after('email');
            $table->string('house')->nullable()->after('road');
            $table->string('floor')->nullable()->after('house');
            $table->decimal('latitude', 10, 8)->nullable()->after('floor');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['email', 'road', 'house', 'floor', 'latitude', 'longitude']);
        });
    }
};
