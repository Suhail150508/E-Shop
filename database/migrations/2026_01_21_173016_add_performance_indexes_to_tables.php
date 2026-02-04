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
        Schema::table('products', function (Blueprint $table) {
            // Check if index exists before adding to avoid duplication errors
            if (! Schema::hasIndex('products', 'products_name_index')) {
                $table->index('name');
            }
            if (! Schema::hasIndex('products', 'products_is_active_index')) {
                $table->index('is_active');
            }
            if (! Schema::hasIndex('products', 'products_price_index')) {
                $table->index('price');
            }
            if (! Schema::hasIndex('products', 'products_discount_price_index')) {
                $table->index('discount_price');
            }
            if (! Schema::hasIndex('products', 'products_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // status and payment_status are already indexed in create_orders_table
            if (! Schema::hasIndex('orders', 'orders_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            if (! Schema::hasIndex('product_reviews', 'product_reviews_is_approved_index')) {
                $table->index('is_approved');
            }
            if (! Schema::hasIndex('product_reviews', 'product_reviews_rating_index')) {
                $table->index('rating');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['price']);
            $table->dropIndex(['discount_price']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Only drop the one we added
            $table->dropIndex(['created_at']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['rating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });
    }
};
