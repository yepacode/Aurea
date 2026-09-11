<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guardamos el payload COMPLETO que devuelve ePayco (webhook o API validation)
 * en `payment_raw_response`. Así tenemos disponible cualquier campo que la
 * pasarela envíe, incluso los que hoy no mostramos — sin perder información
 * para reclamos, auditoría o soporte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (! Schema::hasColumn('orders', 'payment_raw_response')) {
                $t->json('payment_raw_response')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'payment_raw_response')) {
                $t->dropColumn('payment_raw_response');
            }
        });
    }
};
