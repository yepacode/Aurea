<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cuando alguien entra al sitio con ?ref=XXX guardamos el código de referida
 * en sesión y cookie por 30 días. Al registrarse, CustomerAuthController lo
 * consume para vincular a la nueva clienta con quien la refirió.
 *
 * También intenta detectar la fuente (utm_source / referer) para llenar el
 * campo referral_source cuando esté disponible.
 */
class CaptureReferral
{
    /** Nombre común para sesión y cookie. */
    public const COOKIE_KEY = 'aurea_ref';
    public const COOKIE_SOURCE_KEY = 'aurea_ref_src';
    public const COOKIE_MINUTES = 60 * 24 * 30; // 30 días

    public function handle(Request $request, Closure $next): Response
    {
        $refCode = trim((string) $request->query('ref', ''));

        if ($refCode !== '' && strlen($refCode) <= 20) {
            // Sanea: solo A-Z 0-9 en mayúsculas.
            $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $refCode));

            if ($clean !== '') {
                // Solo lo guardamos si corresponde a un cliente real.
                $referrer = Customer::where('referral_code', $clean)->first();

                if ($referrer) {
                    $source = $this->detectSource($request);

                    // Sesión (útil dentro de la misma visita) + cookie (para volver luego).
                    $request->session()->put('referral_code', $clean);
                    $request->session()->put('referral_source', $source);

                    Cookie::queue(self::COOKIE_KEY, $clean, self::COOKIE_MINUTES, null, null, false, true);
                    Cookie::queue(self::COOKIE_SOURCE_KEY, $source, self::COOKIE_MINUTES, null, null, false, true);
                }
            }
        } else {
            // Rehidrata desde cookie si la sesión aún no la tiene (visitante que vuelve
            // días después sin el parámetro en la URL).
            if (! $request->session()->has('referral_code')) {
                $fromCookie = $request->cookie(self::COOKIE_KEY);
                if ($fromCookie) {
                    $request->session()->put('referral_code', $fromCookie);
                    $request->session()->put(
                        'referral_source',
                        $request->cookie(self::COOKIE_SOURCE_KEY) ?: 'direct'
                    );
                }
            }
        }

        return $next($request);
    }

    private function detectSource(Request $request): string
    {
        $utm = strtolower((string) $request->query('utm_source', ''));
        if ($utm !== '') {
            return substr($utm, 0, 40);
        }

        $referer = strtolower((string) $request->headers->get('referer', ''));
        return match (true) {
            str_contains($referer, 'whatsapp') || str_contains($referer, 'wa.me')  => 'whatsapp',
            str_contains($referer, 'instagram')                                    => 'instagram',
            str_contains($referer, 'facebook') || str_contains($referer, 'fb.com') => 'facebook',
            str_contains($referer, 't.co')     || str_contains($referer, 'twitter') || str_contains($referer, 'x.com') => 'twitter',
            $referer === ''                                                        => 'direct',
            default                                                                => 'link',
        };
    }
}
