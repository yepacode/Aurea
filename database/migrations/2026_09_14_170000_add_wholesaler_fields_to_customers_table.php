<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Programa de mayoristas / distribuidoras.
 *
 * Añade a la tabla customers los campos necesarios para que un cliente
 * regular pueda solicitar convertirse en mayorista y que el admin pueda
 * aprobar/rechazar la solicitud. Cuando queda aprobado, el flag
 * `is_wholesaler` = true dispara los precios wholesale en el catálogo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_wholesaler')->default(false)->after('zip_code');
            $table->enum('wholesaler_status', ['none', 'pending', 'approved', 'rejected'])
                ->default('none')
                ->after('is_wholesaler');
            $table->string('wholesaler_company_name')->nullable()->after('wholesaler_status');
            $table->string('wholesaler_nit')->nullable()->after('wholesaler_company_name');
            $table->string('wholesaler_monthly_volume')->nullable()->after('wholesaler_nit');
            $table->timestamp('wholesaler_requested_at')->nullable()->after('wholesaler_monthly_volume');
            $table->timestamp('wholesaler_approved_at')->nullable()->after('wholesaler_requested_at');
            $table->text('wholesaler_notes')->nullable()->after('wholesaler_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'is_wholesaler',
                'wholesaler_status',
                'wholesaler_company_name',
                'wholesaler_nit',
                'wholesaler_monthly_volume',
                'wholesaler_requested_at',
                'wholesaler_approved_at',
                'wholesaler_notes',
            ]);
        });
    }
};
