<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Formulario simple para que el admin pegue sus IDs de GA4 y Meta Pixel.
 * Se aplican de inmediato a config('services.analytics.*') vía AppServiceProvider
 * en el siguiente request; no requiere tocar .env ni desplegar.
 */
class AnalyticsSettingsAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.analytics.index', [
            'ga4_measurement_id' => AnalyticsSetting::get(
                'ga4_measurement_id',
                config('services.analytics.ga4', '')
            ),
            'meta_pixel_id' => AnalyticsSetting::get(
                'meta_pixel_id',
                config('services.analytics.meta_pixel', '')
            ),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        // Validación laxa: dejamos que el admin pegue el ID como aparece en
        // el panel de Google/Meta, sin exigir un formato exacto (los formatos
        // de Meta han cambiado en el pasado).
        $data = $request->validate([
            'ga4_measurement_id' => ['nullable', 'string', 'max:50', 'regex:/^G-[A-Z0-9]+$/i'],
            'meta_pixel_id'      => ['nullable', 'string', 'max:32', 'regex:/^\d{6,20}$/'],
        ], [
            'ga4_measurement_id.regex' => 'El Measurement ID de GA4 debe empezar con G- (ej. G-XXXXXXXXXX).',
            'meta_pixel_id.regex'      => 'El Meta Pixel ID debe ser solo dígitos (15–16 normalmente).',
        ]);

        AnalyticsSetting::set('ga4_measurement_id', trim((string) ($data['ga4_measurement_id'] ?? '')));
        AnalyticsSetting::set('meta_pixel_id',      trim((string) ($data['meta_pixel_id'] ?? '')));

        return redirect()->route('admin.analytics.index')
            ->with('success', 'IDs de analítica actualizados. Se aplican en el siguiente request.');
    }
}
