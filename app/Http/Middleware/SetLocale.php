<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Determina el idioma activo del storefront y lo aplica a la app.
 *
 * Orden de detección (mayor a menor prioridad):
 *   1. Prefijo /en/  → 'en' (siempre gana; permite compartir enlaces en inglés)
 *   2. Sesión        → locale elegido previamente por el usuario
 *   3. Cookie        → aurea_locale (persistencia entre sesiones, 1 año)
 *   4. Accept-Language del navegador (si empieza por "en")
 *   5. Default       → 'es'
 *
 * El middleware se registra dos veces vía routes/web.php:
 *  - Grupo sin prefijo (default español)
 *  - Grupo con prefix('en') + name('en.')  (inglés, misma vista)
 *
 * En ambos casos delega en `app()->setLocale()` y __() se encarga de resolver
 * los archivos lang/{es,en}/*.php.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $forced = null): Response
    {
        $available = array_keys(config('app.available_locales', ['es' => 'Español']));

        // 1. Locale forzado por el grupo de rutas (ej: 'en' cuando la ruta lleva prefijo /en/).
        if ($forced && in_array($forced, $available, true)) {
            $locale = $forced;
        }
        // 2. Sesión — respeta la última elección explícita del usuario.
        elseif ($request->session()->has('locale')
                && in_array($request->session()->get('locale'), $available, true)) {
            $locale = $request->session()->get('locale');
        }
        // 3. Cookie — persiste entre visitas aunque expire la sesión.
        elseif ($request->cookie('aurea_locale')
                && in_array($request->cookie('aurea_locale'), $available, true)) {
            $locale = $request->cookie('aurea_locale');
        }
        // 4. Accept-Language del navegador (heurística mínima: primeros 2 chars).
        elseif ($lang = $this->detectFromBrowser($request, $available)) {
            $locale = $lang;
        }
        // 5. Default.
        else {
            $locale = config('app.locale', 'es');
        }

        app()->setLocale($locale);

        // Guardar en request para que las vistas puedan usar app()->getLocale()
        // o el helper current_locale() sin volver a resolverlo.
        $request->attributes->set('locale', $locale);

        return $next($request);
    }

    private function detectFromBrowser(Request $request, array $available): ?string
    {
        $header = strtolower((string) $request->header('Accept-Language', ''));
        if ($header === '') {
            return null;
        }
        // Ej: "en-US,en;q=0.9,es;q=0.8" — nos quedamos con el primer 2-letter code.
        $primary = substr(trim(explode(',', $header)[0]), 0, 2);

        return in_array($primary, $available, true) ? $primary : null;
    }
}
