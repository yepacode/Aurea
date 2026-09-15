<?php

/*
|--------------------------------------------------------------------------
| Helpers globales
|--------------------------------------------------------------------------
|
| Cargados vía composer.json (autoload.files).
|
*/

if (! function_exists('current_locale')) {
    /**
     * Locale activo del storefront (equivale a app()->getLocale() con guard).
     */
    function current_locale(): string
    {
        $locale = app()->getLocale();
        $available = array_keys(config('app.available_locales', ['es' => 'Español']));
        return in_array($locale, $available, true) ? $locale : (string) config('app.locale', 'es');
    }
}

if (! function_exists('is_locale')) {
    function is_locale(string $locale): bool
    {
        return current_locale() === $locale;
    }
}

if (! function_exists('locale_route')) {
    /**
     * Genera una URL con nombre de ruta respetando el idioma activo (o el
     * indicado). El default no lleva prefijo; los demás usan el nombre
     * `{locale}.{ruta}` que registramos en routes/web.php.
     *
     * Uso: <a href="{{ locale_route('products.index') }}">
     */
    function locale_route(string $name, array $params = [], ?string $locale = null): string
    {
        $locale  = $locale ?: current_locale();
        $default = (string) config('app.locale', 'es');

        $target = $locale === $default ? $name : "{$locale}.{$name}";

        // Si por algún motivo la ruta prefijada no existe (ruta que sólo
        // existe en el default), caemos con gracia al nombre plano.
        if (!\Illuminate\Support\Facades\Route::has($target)) {
            $target = $name;
        }

        return route($target, $params);
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * URL de la MISMA página en el `$locale` dado. Se usa por el switcher del
     * navbar y por los `<link rel="alternate" hreflang="..." />` del <head>.
     * Basado en la URL actual (no en el referer).
     */
    function switch_locale_url(string $locale): string
    {
        $default = (string) config('app.locale', 'es');
        $request = request();
        $path    = '/'.ltrim($request->path(), '/');

        // Quitar prefijo actual si aparece.
        foreach (array_keys(config('app.available_locales', [])) as $l) {
            if ($l === $default) continue;
            if ($path === '/'.$l || str_starts_with($path, '/'.$l.'/')) {
                $path = substr($path, strlen('/'.$l)) ?: '/';
                break;
            }
        }

        if ($locale !== $default) {
            $path = '/'.$locale.($path === '/' ? '' : $path);
        }

        $qs = $request->getQueryString();
        return $path.($qs ? '?'.$qs : '');
    }
}
