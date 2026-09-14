<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use App\Models\ShippingZoneDepartment;
use Illuminate\Database\Seeder;

/**
 * Zonas de envío de ejemplo — sólo se ejecuta si aún no hay ninguna. Así el
 * seeder es idempotente y no pisa la configuración real del cliente cuando
 * se corre `db:seed` en un ambiente ya poblado.
 */
class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        if (ShippingZone::query()->exists()) {
            $this->command?->info('ShippingZoneSeeder: ya hay zonas; se omite.');
            return;
        }

        $zonas = [
            [
                'zone' => [
                    'name'              => 'Bogotá D.C.',
                    'carrier'           => 'servientrega',
                    'base_cost'         => 12000,
                    'extra_kg_cost'     => 2000,
                    'delivery_days_min' => 1,
                    'delivery_days_max' => 2,
                    'sort_order'        => 1,
                    'is_active'         => true,
                ],
                'departments' => ['Bogotá D.C.'],
            ],
            [
                'zone' => [
                    'name'              => 'Ciudades principales',
                    'carrier'           => 'servientrega',
                    'base_cost'         => 14000,
                    'extra_kg_cost'     => 2500,
                    'delivery_days_min' => 2,
                    'delivery_days_max' => 3,
                    'sort_order'        => 2,
                    'is_active'         => true,
                ],
                'departments' => [
                    'Antioquia',
                    'Valle del Cauca',
                    'Atlántico',
                    'Santander',
                    'Bolívar',
                ],
            ],
            [
                'zone' => [
                    'name'              => 'Resto del país',
                    'carrier'           => 'interrapidisimo',
                    'base_cost'         => 18000,
                    'extra_kg_cost'     => 3000,
                    'delivery_days_min' => 3,
                    'delivery_days_max' => 5,
                    'sort_order'        => 3,
                    'is_active'         => true,
                ],
                'departments' => [
                    'Amazonas', 'Arauca', 'Boyacá', 'Caldas', 'Caquetá', 'Casanare',
                    'Cauca', 'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía',
                    'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta', 'Nariño',
                    'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda',
                    'San Andrés y Providencia', 'Sucre', 'Tolima', 'Vaupés', 'Vichada',
                ],
            ],
        ];

        foreach ($zonas as $entry) {
            $zone = ShippingZone::create($entry['zone']);
            foreach ($entry['departments'] as $dept) {
                ShippingZoneDepartment::create([
                    'shipping_zone_id' => $zone->id,
                    'department'       => $dept,
                ]);
            }
        }
    }
}
