<?php

namespace App\Services;

use App\Models\ShippingSetting;
use App\Models\ShippingZone;

/**
 * Cotizador de envío multi-zona.
 *
 * Regla:
 *   1. Si `subtotal >= free_shipping_threshold` → envío gratis (is_free=true).
 *   2. Se busca la primera zona ACTIVA que contenga el departamento del cliente
 *      (ordenada por sort_order asc, id asc).
 *   3. cost = base_cost + max(0, ceil(weight_kg) - 1) * extra_kg_cost
 *   4. Si el departamento no está en ninguna zona activa, cost = config
 *      `shipping.fallback_cost` (transportadora "Otro", 3-5 días por defecto).
 */
class ShippingService
{
    /**
     * @return array{
     *   cost: int,
     *   carrier: string,               // slug ("servientrega", "otro"…)
     *   carrier_label: string,         // label legible ("Servientrega", "Otro")
     *   zone_name: ?string,            // nombre de la zona o null si fallback
     *   delivery_days_min: int,
     *   delivery_days_max: int,
     *   is_free: bool,
     * }
     */
    public function quote(?string $department, ?float $weightKg = null, ?float $subtotal = null): array
    {
        $threshold = (float) ShippingSetting::get('free_shipping_threshold', 0);
        $subtotal  = (float) ($subtotal ?? 0);

        if ($threshold > 0 && $subtotal > 0 && $subtotal >= $threshold) {
            // Envío gratis por umbral. Igualmente devolvemos la zona (si aplica)
            // para que el checkout pueda mostrar la transportadora y los días.
            $zone = ShippingZone::findForDepartment($department);

            return $this->fromZoneOrFallback($zone, isFree: true, cost: 0);
        }

        $zone = ShippingZone::findForDepartment($department);

        if (! $zone) {
            return $this->fallback(isFree: false);
        }

        $weightKg = $weightKg ?? (float) config('shipping.default_weight_kg', 1);
        $extraKg  = max(0, (int) ceil($weightKg) - 1);
        $cost     = (int) $zone->base_cost + ($extraKg * (int) $zone->extra_kg_cost);

        return $this->fromZoneOrFallback($zone, isFree: false, cost: $cost);
    }

    /**
     * Helper: si hay zona, arma respuesta con sus datos; si no, cae al fallback.
     */
    private function fromZoneOrFallback(?ShippingZone $zone, bool $isFree, int $cost): array
    {
        if (! $zone) {
            $res = $this->fallback($isFree);
            $res['cost'] = $cost;

            return $res;
        }

        return [
            'cost'              => $cost,
            'carrier'           => (string) $zone->carrier,
            'carrier_label'     => $zone->carrierLabel(),
            'zone_name'         => (string) $zone->name,
            'delivery_days_min' => (int) $zone->delivery_days_min,
            'delivery_days_max' => (int) $zone->delivery_days_max,
            'is_free'           => $isFree,
        ];
    }

    private function fallback(bool $isFree): array
    {
        return [
            'cost'              => $isFree ? 0 : (int) config('shipping.fallback_cost', 20000),
            'carrier'           => 'otro',
            'carrier_label'     => (string) (config('shipping.carriers.otro') ?? 'Otro'),
            'zone_name'         => null,
            'delivery_days_min' => 3,
            'delivery_days_max' => 5,
            'is_free'           => $isFree,
        ];
    }
}
