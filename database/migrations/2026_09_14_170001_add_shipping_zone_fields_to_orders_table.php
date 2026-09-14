<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot en el pedido de la zona/tiempo estimado cotizados en checkout.
 *
 * `shipping_carrier` ya existe (add_shipping_tracking_to_orders_table) y se
 * reutiliza — al pasar por checkout se pisa con la transportadora de la zona
 * cotizada, pero el admin sigue pudiendo editarla desde el detalle de la
 * orden como hasta ahora.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_zone_name', 120)->nullable()->after('shipping_carrier');
            $table->unsignedSmallInteger('shipping_delivery_days_min')->nullable()->after('shipping_zone_name');
            $table->unsignedSmallInteger('shipping_delivery_days_max')->nullable()->after('shipping_delivery_days_min');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_zone_name',
                'shipping_delivery_days_min',
                'shipping_delivery_days_max',
            ]);
        });
    }
};
