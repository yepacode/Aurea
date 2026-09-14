<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-envío por zonas configurables.
 *
 * Reemplaza el flat-rate único (`shipping_settings.default_price`) por un
 * sistema donde el admin crea zonas, asigna departamentos a cada una y define
 * tarifa base + kg extra + tiempo de entrega + transportadora.
 *
 * El umbral de envío gratis (`shipping_settings.free_shipping_threshold`) se
 * conserva como config global (independiente de la zona).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            // Transportadora sugerida: enum "abierto" — el admin puede elegir "otro"
            // y luego escribirla libre; guardamos aquí el slug del enum.
            $table->string('carrier', 40)->default('otro');
            $table->unsignedInteger('base_cost')->default(0);          // COP (entero)
            $table->unsignedInteger('extra_kg_cost')->default(0);      // COP por kg adicional
            $table->unsignedSmallInteger('delivery_days_min')->default(1);
            $table->unsignedSmallInteger('delivery_days_max')->default(3);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('shipping_zone_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')
                ->constrained('shipping_zones')
                ->cascadeOnDelete();
            $table->string('department', 100);
            $table->timestamps();

            // Un departamento no se repite dentro de una zona. Sí puede estar
            // en varias zonas distintas — la cotización toma la primera activa
            // por sort_order.
            $table->unique(['shipping_zone_id', 'department']);
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_departments');
        Schema::dropIfExists('shipping_zones');
    }
};
