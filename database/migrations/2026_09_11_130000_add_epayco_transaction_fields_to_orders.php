<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda TODA la información transaccional que devuelve ePayco para poder:
 *  - Ver por qué se rechazó un pago (motivo textual del banco).
 *  - Mostrarle al cliente el motivo en la confirmación.
 *  - Hacer reclamos con datos completos (franquicia, banco, ID de transacción,
 *    autorización).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            $add = function (string $col) use ($t) {
                if (! Schema::hasColumn('orders', $col)) {
                    $t->string($col)->nullable();
                }
            };
            $add('payment_transaction_id');   // x_transaction_id (ID único de la transacción en ePayco)
            $add('payment_response_code');    // x_cod_response (1/2/3/4)
            $add('payment_response_reason');  // x_response_reason_text (motivo textual: "Aprobada", "Fondos insuficientes", ...)
            $add('payment_franchise');        // x_franchise (VS/MC/AM/PSE/NEQUI/EFECTY...)
            $add('payment_bank');             // x_bank_name (banco de la tarjeta o del PSE)
            $add('payment_authorization');    // x_approval_code / x_id_invoice (nº autorización del banco)
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            foreach ([
                'payment_transaction_id',
                'payment_response_code',
                'payment_response_reason',
                'payment_franchise',
                'payment_bank',
                'payment_authorization',
            ] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
