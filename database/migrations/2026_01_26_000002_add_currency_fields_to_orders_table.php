<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('base_currency', 16)->nullable()->after('currency');
            $table->string('order_currency', 16)->nullable()->after('base_currency');
            $table->decimal('exchange_rate', 15, 6)->default(1)->after('order_currency');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['base_currency', 'order_currency', 'exchange_rate']);
        });
    }
};
