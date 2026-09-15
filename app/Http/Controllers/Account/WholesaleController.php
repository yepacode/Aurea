<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Mail\WholesaleRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Área de la clienta: solicitud para entrar al programa de mayoristas.
 *
 * - GET  /cuenta/mayorista/solicitar → muestra el estado actual + formulario.
 * - POST /cuenta/mayorista/solicitar → registra la solicitud y avisa al admin.
 *
 * El middleware `auth:customer` protege ambas rutas (ver routes/web.php).
 */
class WholesaleController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function show(): View
    {
        $customer = $this->customer();
        $volumeOptions = config('wholesale.monthly_volume_options', []);

        return view('account.wholesale-request', compact('customer', 'volumeOptions'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $customer = $this->customer();

        // Si la solicitud ya está pendiente o aprobada, no se re-registra.
        if (in_array($customer->wholesaler_status, ['pending', 'approved'], true)) {
            return redirect()
                ->route('account.wholesale.request')
                ->with('info', 'Ya tienes una solicitud registrada.');
        }

        $volumeKeys = array_keys(config('wholesale.monthly_volume_options', []));

        $data = $request->validate([
            'wholesaler_company_name'   => 'required|string|max:200',
            'wholesaler_nit'            => 'required|string|max:40',
            'city'                      => 'required|string|max:120',
            'wholesaler_monthly_volume' => 'required|string|in:'.implode(',', $volumeKeys),
            'phone'                     => 'required|string|max:30',
            'wholesaler_notes'          => 'nullable|string|max:1500',
        ], [
            'wholesaler_company_name.required'   => 'Cuéntanos la razón social o nombre comercial.',
            'wholesaler_nit.required'            => 'Necesitamos el NIT o documento de identidad.',
            'city.required'                      => 'Necesitamos la ciudad para calcular envío.',
            'wholesaler_monthly_volume.required' => 'Elige un rango estimado.',
            'wholesaler_monthly_volume.in'       => 'Elige uno de los rangos disponibles.',
            'phone.required'                     => 'Un teléfono comercial nos ayuda a atenderte más rápido.',
        ]);

        // Campos comunes: sí son fillable, van por ->fill().
        $customer->fill([
            'phone'                     => $data['phone'],
            'city'                      => $data['city'],
            'wholesaler_company_name'   => $data['wholesaler_company_name'],
            'wholesaler_nit'            => $data['wholesaler_nit'],
            'wholesaler_monthly_volume' => $data['wholesaler_monthly_volume'],
            'wholesaler_requested_at'   => now(),
        ]);

        // Campos de privilegio: SIEMPRE explícitos, jamás por mass-assignment.
        $customer->wholesaler_notes  = $data['wholesaler_notes'] ?? null;
        $customer->wholesaler_status = 'pending';
        $customer->is_wholesaler     = false;

        $customer->save();

        // Aviso al admin. En un try/catch para que un fallo de SMTP no rompa
        // la experiencia del cliente: la solicitud queda registrada igual.
        try {
            Mail::to(config('mail.admin'))->send(new WholesaleRequestReceived($customer));
        } catch (\Throwable $e) {
            Log::warning('Wholesale request email failed: '.$e->getMessage());
        }

        return redirect()
            ->route('account.wholesale.request')
            ->with('success', 'Solicitud enviada. Te contactamos en las próximas horas.');
    }
}
