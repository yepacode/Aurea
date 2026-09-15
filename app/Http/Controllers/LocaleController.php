<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Cambio manual de idioma desde el switcher del navbar.
 *
 * Recibe POST /set-locale con {locale: 'es'|'en'} y:
 *   - Valida contra config('app.available_locales').
 *   - Guarda la elección en sesión + cookie 'aurea_locale' (1 año).
 *   - Redirige a la URL equivalente en el otro idioma:
 *       · ES → EN: prepende /en/ al path actual.
 *       · EN → ES: elimina el prefijo /en/ del path del referer.
 *
 * El referer se toma del header, no de instrucciones en la request, para
 * evitar redirecciones a hosts externos.
 */
class LocaleController extends Controller
{
    public function set(Request $request): RedirectResponse
    {
        $available = array_keys(config('app.available_locales', ['es' => 'Español']));
        $default   = config('app.locale', 'es');

        $locale = $request->input('locale');
        if (!in_array($locale, $available, true)) {
            $locale = $default;
        }

        // Persistir la elección.
        $request->session()->put('locale', $locale);
        Cookie::queue('aurea_locale', $locale, 60 * 24 * 365); // 1 año.

        // Reescribir el path del referer para conservar la página actual en
        // el nuevo idioma. Solo aceptamos referers del mismo host.
        $target = $this->rewritePathForLocale($request, $locale);

        return redirect($target);
    }

    private function rewritePathForLocale(Request $request, string $locale): string
    {
        $default = config('app.locale', 'es');
        $referer = (string) $request->header('referer', '');

        // Sanidad: si no hay referer o es de otro host, mandamos al home.
        if ($referer === '') {
            return $locale === $default ? '/' : '/'.$locale;
        }

        $parts = parse_url($referer);
        if (!isset($parts['host']) || $parts['host'] !== $request->getHost()) {
            return $locale === $default ? '/' : '/'.$locale;
        }

        $path  = $parts['path']  ?? '/';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';

        // Quitar prefijo actual si existe (soportamos cualquier locale no-default).
        foreach (array_keys(config('app.available_locales', [])) as $l) {
            if ($l === $default) continue;
            if ($path === '/'.$l || str_starts_with($path, '/'.$l.'/')) {
                $path = substr($path, strlen('/'.$l)) ?: '/';
                break;
            }
        }

        // Prependemos el prefijo nuevo si el destino NO es el default.
        if ($locale !== $default) {
            $path = '/'.$locale.($path === '/' ? '' : $path);
        }

        return $path.$query;
    }
}
