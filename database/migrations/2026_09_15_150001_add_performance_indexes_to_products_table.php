<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de rendimiento para products:
     *  - (is_active, is_featured, sort_order): home/catálogo.
     *  - (category_id, is_active): filtros por categoría.
     *  - (brand_id, is_active): filtros por marca.
     * `slug` ya tiene el índice único creado en la migración original, no hace falta añadir uno extra.
     */
    public function up(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM products'))->pluck('Key_name')->unique()->all();

        Schema::table('products', function (Blueprint $table) use ($existing) {
            if (! in_array('products_is_active_is_featured_sort_order_index', $existing, true)) {
                $table->index(['is_active', 'is_featured', 'sort_order'], 'products_is_active_is_featured_sort_order_index');
            }

            if (! in_array('products_category_id_is_active_index', $existing, true)) {
                $table->index(['category_id', 'is_active'], 'products_category_id_is_active_index');
            }

            if (! in_array('products_brand_id_is_active_index', $existing, true)) {
                $table->index(['brand_id', 'is_active'], 'products_brand_id_is_active_index');
            }
        });
    }

    public function down(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM products'))->pluck('Key_name')->unique()->all();

        Schema::table('products', function (Blueprint $table) use ($existing) {
            if (in_array('products_is_active_is_featured_sort_order_index', $existing, true)) {
                $table->dropIndex('products_is_active_is_featured_sort_order_index');
            }

            if (in_array('products_category_id_is_active_index', $existing, true)) {
                $table->dropIndex('products_category_id_is_active_index');
            }

            if (in_array('products_brand_id_is_active_index', $existing, true)) {
                $table->dropIndex('products_brand_id_is_active_index');
            }
        });
    }
};
