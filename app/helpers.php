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
        // OJO: NO usar config('app.locale') porque el middleware SetLocale lo
        // muta en runtime. Ver default_storefront_locale() para el porqué.
        $default = function_exists('default_storefront_locale')
            ? default_storefront_locale()
            : (string) config('app.fallback_locale', 'es');

        $target = $locale === $default ? $name : "{$locale}.{$name}";

        // Si por algún motivo la ruta prefijada no existe (ruta que sólo
        // existe en el default), caemos con gracia al nombre plano.
        if (!\Illuminate\Support\Facades\Route::has($target)) {
            $target = $name;
        }

        return route($target, $params);
    }
}

if (! function_exists('default_storefront_locale')) {
    /**
     * Locale default del storefront (el que NO lleva prefijo de URL).
     *
     * IMPORTANTE: NO usamos `config('app.locale')` porque Laravel muta
     * ese valor en cada request cuando el middleware llama a
     * `app()->setLocale('en')`. Si lo leyéramos desde ahí, en la vista de
     * `/en/…` el "default" ya sería 'en' y el helper generaría URLs
     * absurdas tipo `/es/en/...` (bug real observado en hreflang).
     *
     * Preferimos `fallback_locale` (no se muta con setLocale) y caemos
     * duro a 'es' — este proyecto es un e-commerce colombiano en español.
     */
    function default_storefront_locale(): string
    {
        return (string) config('app.fallback_locale', 'es');
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * URL de la MISMA página en el `$locale` dado. Se usa por el switcher del
     * navbar y por los `<link rel="alternate" hreflang="..." />` del <head>.
     * Basado en la URL actual (no en el referer).
     *
     * Reglas:
     *  - Default (ES) NUNCA lleva prefijo: `/algo`
     *  - EN siempre lleva prefijo: `/en/algo`
     *  - Nunca produce doble prefijo (`/es/en/...`, `/en/en/...`) ni mete
     *    el default como prefijo aunque `config('app.locale')` haya sido
     *    mutado por SetLocale en el request en curso.
     */
    function switch_locale_url(string $locale): string
    {
        $default = default_storefront_locale();
        $request = request();
        $path    = '/'.ltrim($request->path(), '/');

        // Quitar el prefijo de idioma actual si aparece (para cualquier locale
        // no-default que exista en la config, no sólo el "actual").
        foreach (array_keys(config('app.available_locales', [])) as $l) {
            if ($l === $default) continue;
            if ($path === '/'.$l || str_starts_with($path, '/'.$l.'/')) {
                $path = substr($path, strlen('/'.$l)) ?: '/';
                break;
            }
        }

        // Recomponer con el prefijo destino (sólo si NO es el default).
        if ($locale !== $default) {
            $path = '/'.$locale.($path === '/' ? '' : $path);
        }

        $qs = $request->getQueryString();
        return $path.($qs ? '?'.$qs : '');
    }
}
