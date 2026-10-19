<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos informativos de la sincronización con Miracle.
 *
 * NO tocan el precio de venta (`price`) ni ninguna lógica existente: solo
 * guardan, como referencia, los dos precios de Miracle (Centro de Experiencia
 * y Distribuidor), una firma del set de imágenes ya importado (para no
 * re-descargar si no cambió) y la fecha de la última sincronización.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('miracle_ref')->nullable()->after('internal_code')->index();
            $table->decimal('miracle_precio_centro', 12, 2)->nullable()->after('cost_price');
            $table->decimal('miracle_precio_distribuidor', 12, 2)->nullable()->after('miracle_precio_centro');
            $table->string('miracle_img_sig', 64)->nullable()->after('miracle_precio_distribuidor');
            $table->timestamp('miracle_synced_at')->nullable()->after('miracle_img_sig');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['miracle_ref']);
            $table->dropColumn([
                'miracle_ref',
                'miracle_precio_centro',
                'miracle_precio_distribuidor',
                'miracle_img_sig',
                'miracle_synced_at',
            ]);
        });
    }
};
