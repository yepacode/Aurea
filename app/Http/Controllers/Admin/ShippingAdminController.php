<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use App\Models\ShippingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping.index', [
            'defaultPrice' => ShippingSetting::get('default_price', '99.00'),
            'freeThreshold' => ShippingSetting::get('free_shipping_threshold', '0'),
            'rates' => ShippingRate::orderBy('state')->get(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'default_price' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        ShippingSetting::set('default_price', $request->input('default_price'));
        ShippingSetting::set('free_shipping_threshold', $request->input('free_shipping_threshold'));

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Configuración de envío actualizada.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'state' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Prevent duplicate zone
        if (ShippingRate::whereRaw('LOWER(state) = ?', [mb_strtolower($validated['state'])])->exists()) {
            return redirect()->route('admin.shipping.index')
                ->with('error', 'Ya existe una tarifa para la zona "' . $validated['state'] . '".');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['city'] = null;

        ShippingRate::create($validated);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Zona de envío creada: ' . $validated['state'] . '.');
    }

    public function update(Request $request, ShippingRate $shippingRate): RedirectResponse
    {
        $validated = $request->validate([
            'state' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Prevent duplicate zone (excluding current rate)
        $duplicate = ShippingRate::whereRaw('LOWER(state) = ?', [mb_strtolower($validated['state'])])
            ->where('id', '!=', $shippingRate->id)
            ->exists();
        if ($duplicate) {
            return redirect()->route('admin.shipping.index')
                ->with('error', 'Ya existe otra tarifa para la zona "' . $validated['state'] . '".');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $shippingRate->update($validated);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Tarifa actualizada.');
    }

    public function destroy(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->delete();

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Tarifa eliminada.');
    }
}
