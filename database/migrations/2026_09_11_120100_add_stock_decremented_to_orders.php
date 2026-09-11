<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Marca cuándo se descontó el stock de un pedido (idempotencia).
 * Los pedidos con pago diferido (ePayco / transferencia / contra entrega)
 * NO descuentan al crearse; sólo cuando el pago pasa a 'paid'.
 */
return new class extends Migration
{
    public function up(): void
    {
        $added = false;
        Schema::table('orders', function (Blueprint $table) use (&$added) {
            if (! Schema::hasColumn('orders', 'stock_decremented_at')) {
                if (Schema::hasColumn('orders', 'review_requested_at')) {
                    $table->timestamp('stock_decremented_at')->nullable()->after('review_requested_at');
                } else {
                    $table->timestamp('stock_decremented_at')->nullable();
                }
                $added = true;
            }
        });

        // Backfill: los pedidos anteriores YA descontaron stock al crearse
        // (con el flujo viejo). Marcar todos como decrementados evita que un
        // webhook tardío los descuente por segunda vez.
        if ($added) {
            DB::statement('UPDATE orders SET stock_decremented_at = COALESCE(stock_decremented_at, created_at)');
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stock_decremented_at')) {
                $table->dropColumn('stock_decremented_at');
            }
        });
    }
};
