<?php

namespace App\Http\Controllers;

use App\Services\MercadoLibreService;
use Illuminate\Http\Request;

/**
 * Flujo OAuth con Mercado Libre.
 *  GET /ml/conectar  → redirige al login/autorización de ML.
 *  GET /ml/callback  → ML vuelve con ?code=...; lo canjeamos por tokens.
 * Ambas rutas van detrás de auth+admin.
 */
class MercadoLibreOauthController extends Controller
{
    public function __construct(private MercadoLibreService $ml)
    {
    }

    public function connect()
    {
        if (! $this->ml->configured()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Faltan credenciales de Mercado Libre en el .env (MERCADOLIBRE_APP_ID / MERCADOLIBRE_SECRET).');
        }

        return redirect()->away($this->ml->authUrl());
    }

    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Autorización de Mercado Libre cancelada: '.$request->get('error_description', $request->get('error')));
        }

        $code = $request->get('code');
        if (! $code || ! $this->ml->exchangeCode($code)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No se pudo conectar con Mercado Libre. Revisa el App ID/Secret y la URI de redirect.');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', '¡Conectado con Mercado Libre! Ya puedes publicar con: php artisan ml:publish');
    }
}
