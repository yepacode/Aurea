<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Models\ShippingZoneDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * CRUD del sistema multi-envío por zonas. Cada zona agrupa varios
 * departamentos y define su transportadora, costo base, costo por kg extra y
 * tiempo estimado de entrega.
 */
class ShippingZoneAdminController extends Controller
{
    public function index(): View
    {
        $zones = ShippingZone::with('departments')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.shipping-zones.index', [
            'zones'    => $zones,
            'carriers' => (array) config('shipping.carriers', []),
        ]);
    }

    public function create(): View
    {
        return view('admin.shipping-zones.edit', [
            'zone'              => new ShippingZone(['carrier' => 'servientrega', 'is_active' => true]),
            'assignedDepartments' => [],
            'carriers'          => (array) config('shipping.carriers', []),
            'departments'       => (array) config('shipping.departments', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($validated) {
            $zone = ShippingZone::create($this->attrsFromValidated($validated));
            $this->syncDepartments($zone, $validated['departments'] ?? []);
        });

        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona de envío creada.');
    }

    public function edit(ShippingZone $shippingZone): View
    {
        $shippingZone->load('departments');

        return view('admin.shipping-zones.edit', [
            'zone'                => $shippingZone,
            'assignedDepartments' => $shippingZone->departments->pluck('department')->all(),
            'carriers'            => (array) config('shipping.carriers', []),
            'departments'         => (array) config('shipping.departments', []),
        ]);
    }

    public function update(Request $request, ShippingZone $shippingZone): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($shippingZone, $validated) {
            $shippingZone->update($this->attrsFromValidated($validated));
            $this->syncDepartments($shippingZone, $validated['departments'] ?? []);
        });

        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona de envío actualizada.');
    }

    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        $shippingZone->delete();

        return redirect()->route('admin.shipping-zones.index')
            ->with('success', 'Zona eliminada.');
    }

    /**
     * Reglas comunes a store/update. `departments` es un array de nombres tal
     * cual salen del `<select multiple>` del form. Los validamos contra el
     * catálogo de departamentos para evitar valores libres inesperados.
     */
    private function validated(Request $request): array
    {
        $carriers    = array_keys((array) config('shipping.carriers', []));
        $departments = (array) config('shipping.departments', []);

        return $request->validate([
            'name'              => 'required|string|max:120',
            'carrier'           => 'required|string|in:' . implode(',', $carriers),
            'base_cost'         => 'required|integer|min:0',
            'extra_kg_cost'     => 'nullable|integer|min:0',
            'delivery_days_min' => 'required|integer|min:0|max:60',
            'delivery_days_max' => 'required|integer|min:0|max:60|gte:delivery_days_min',
            'sort_order'        => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
            'departments'       => 'nullable|array',
            'departments.*'     => 'string|in:' . implode(',', $departments),
        ]);
    }

    private function attrsFromValidated(array $v): array
    {
        return [
            'name'              => $v['name'],
            'carrier'           => $v['carrier'],
            'base_cost'         => (int) $v['base_cost'],
            'extra_kg_cost'     => (int) ($v['extra_kg_cost'] ?? 0),
            'delivery_days_min' => (int) $v['delivery_days_min'],
            'delivery_days_max' => (int) $v['delivery_days_max'],
            'sort_order'        => (int) ($v['sort_order'] ?? 0),
            'is_active'         => (bool) ($v['is_active'] ?? false),
        ];
    }

    /**
     * Sincroniza el conjunto de departamentos de la zona (delete + insert).
     * La lista puede venir vacía (zona sin departamentos: nunca cotiza).
     */
    private function syncDepartments(ShippingZone $zone, array $departments): void
    {
        $departments = array_values(array_unique(array_filter($departments)));

        $zone->departments()->delete();

        if (empty($departments)) {
            return;
        }

        $rows = array_map(fn ($d) => [
            'shipping_zone_id' => $zone->id,
            'department'       => $d,
            'created_at'       => now(),
            'updated_at'       => now(),
        ], $departments);

        ShippingZoneDepartment::insert($rows);
    }
}
