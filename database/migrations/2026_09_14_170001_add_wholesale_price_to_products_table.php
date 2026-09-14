<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Precios wholesale por producto.
 *
 * - `wholesale_price`: precio para mayoristas aprobados. Si es NULL, se aplica
 *   el descuento por defecto configurado en config('wholesale.default_discount')
 *   (20% de descuento por defecto).
 * - `wholesale_min_qty`: cantidad mínima por unidad para que el precio mayorista
 *   aplique en el carrito. Default 1 (siempre aplica).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('wholesale_price')->nullable()->after('cost_price');
            $table->unsignedInteger('wholesale_min_qty')->default(1)->after('wholesale_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_min_qty']);
        });
    }
};
