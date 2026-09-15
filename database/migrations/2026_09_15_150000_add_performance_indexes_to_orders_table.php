<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de rendimiento para orders:
     *  - (payment_status, status, created_at): dashboard/admin filtran por estos.
     *  - (created_at): tendencias y ordenaciones cronológicas.
     *  - (customer_id): sólo se agrega si no existe (usualmente ya está por la FK).
     */
    public function up(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM orders'))->pluck('Key_name')->unique()->all();

        Schema::table('orders', function (Blueprint $table) use ($existing) {
            if (! in_array('orders_payment_status_status_created_at_index', $existing, true)) {
                $table->index(['payment_status', 'status', 'created_at'], 'orders_payment_status_status_created_at_index');
            }

            if (! in_array('orders_created_at_index', $existing, true)) {
                $table->index('created_at', 'orders_created_at_index');
            }

            // La FK ya suele generar un índice; sólo agregamos si no aparece ninguno sobre customer_id.
            $hasCustomerIndex = collect(DB::select('SHOW INDEX FROM orders'))
                ->contains(fn ($i) => $i->Column_name === 'customer_id');

            if (! $hasCustomerIndex) {
                $table->index('customer_id', 'orders_customer_id_index');
            }
        });
    }

    public function down(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM orders'))->pluck('Key_name')->unique()->all();

        Schema::table('orders', function (Blueprint $table) use ($existing) {
            if (in_array('orders_payment_status_status_created_at_index', $existing, true)) {
                $table->dropIndex('orders_payment_status_status_created_at_index');
            }

            if (in_array('orders_created_at_index', $existing, true)) {
                $table->dropIndex('orders_created_at_index');
            }

            if (in_array('orders_customer_id_index', $existing, true)) {
                $table->dropIndex('orders_customer_id_index');
            }
        });
    }
};
