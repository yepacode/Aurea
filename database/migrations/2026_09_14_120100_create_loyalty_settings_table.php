<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración global del programa de puntos.
 * Se maneja con el mismo patrón key/value que AnalyticsSetting:
 *   - enabled:               '1' / '0'
 *   - earn_rate:             puntos por peso gastado (0.001 = 1 punto por cada $1.000 COP)
 *   - points_expiry_months:  meses hasta que un lote de puntos vence
 *   - min_redemption:        mínimo de puntos requeridos para canjear (fase 2)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'enabled'              => '1',
            'earn_rate'            => '0.001',   // 1 punto por cada $1.000 COP
            'points_expiry_months' => '12',
            'min_redemption'       => '500',
        ];

        $now = now();
        foreach ($defaults as $key => $value) {
            DB::table('loyalty_settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
