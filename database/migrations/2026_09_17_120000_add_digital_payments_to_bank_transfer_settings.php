<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade columnas para pagos digitales colombianos (Nequi, Daviplata,
 * Bancolombia a la mano) y NIT del titular al schema key/value existente.
 *
 * La tabla sigue siendo key/value: cada nueva clave se guarda como una fila
 * (BankTransferSetting::set('nequi_enabled', '1'), etc.). Aun así definimos
 * las columnas físicas para compatibilidad con futuras refactorizaciones y
 * para dejar el schema documentado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_transfer_settings', function (Blueprint $t) {
            $columns = [
                'nit'                        => fn ($t) => $t->string('nit')->nullable(),
                'nequi_enabled'              => fn ($t) => $t->boolean('nequi_enabled')->default(false),
                'nequi_number'               => fn ($t) => $t->string('nequi_number')->nullable(),
                'nequi_instructions'         => fn ($t) => $t->text('nequi_instructions')->nullable(),
                'daviplata_enabled'          => fn ($t) => $t->boolean('daviplata_enabled')->default(false),
                'daviplata_number'           => fn ($t) => $t->string('daviplata_number')->nullable(),
                'daviplata_instructions'     => fn ($t) => $t->text('daviplata_instructions')->nullable(),
                'bancolombia_amano_enabled'  => fn ($t) => $t->boolean('bancolombia_amano_enabled')->default(false),
                'bancolombia_amano_number'   => fn ($t) => $t->string('bancolombia_amano_number')->nullable(),
            ];

            foreach ($columns as $name => $definition) {
                if (! Schema::hasColumn('bank_transfer_settings', $name)) {
                    $definition($t);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_transfer_settings', function (Blueprint $t) {
            foreach ([
                'nit',
                'nequi_enabled', 'nequi_number', 'nequi_instructions',
                'daviplata_enabled', 'daviplata_number', 'daviplata_instructions',
                'bancolombia_amano_enabled', 'bancolombia_amano_number',
            ] as $col) {
                if (Schema::hasColumn('bank_transfer_settings', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
