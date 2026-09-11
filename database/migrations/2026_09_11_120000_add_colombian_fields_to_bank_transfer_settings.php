<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade columnas de perfil bancario colombiano al schema de bank_transfer_settings.
 *
 * Nota: la tabla es key/value y el código de la app sigue usando ese patrón
 * (BankTransferSetting::set('account_type', ...) → nueva fila con key='account_type').
 * Estas columnas se agregan por compatibilidad con futuras refactors y para
 * cumplir el requerimiento; hoy quedan disponibles pero sin uso directo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_transfer_settings', function (Blueprint $t) {
            if (! Schema::hasColumn('bank_transfer_settings', 'account_type')) {
                $t->string('account_type')->nullable();
            }
            if (! Schema::hasColumn('bank_transfer_settings', 'document_type')) {
                $t->string('document_type')->nullable();
            }
            if (! Schema::hasColumn('bank_transfer_settings', 'document_number')) {
                $t->string('document_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_transfer_settings', function (Blueprint $t) {
            foreach (['account_type', 'document_type', 'document_number'] as $col) {
                if (Schema::hasColumn('bank_transfer_settings', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
