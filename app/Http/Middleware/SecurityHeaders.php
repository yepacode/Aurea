<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad para todas las respuestas del grupo web.
 *
 * - X-Content-Type-Options: nosniff  → evita "MIME sniffing" de assets.
 * - X-Frame-Options: SAMEORIGIN      → evita clickjacking (frame de terceros).
 * - Referrer-Policy                  → menos filtrado de URL a terceros.
 * - Permissions-Policy               → cierra APIs sensibles del browser.
 * - Content-Security-Policy          → mitiga XSS con allow-list de orígenes.
 *
 * NOTA: la CSP incluye CDNs y dominios de ePayco/GA4/Meta que ya están en uso
 * en las vistas. Si algo del panel/admin rompe, ajustar aquí.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(self)');

        // CSP conservadora: permite CDN comunes + ePayco + GA4 + Meta Pixel.
        // 'unsafe-inline' y 'unsafe-eval' se mantienen porque hoy la app usa
        // <script> inline y librerías (Alpine/Tailwind CDN) que los requieren.
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://code.jquery.com https://cdn.tailwindcss.com https://www.googletagmanager.com https://www.google-analytics.com https://connect.facebook.net https://checkout.epayco.co https://apify.epayco.co; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https: blob:; " .
               "media-src 'self' data: blob: https:; " .
               "connect-src 'self' https://www.google-analytics.com https://checkout.epayco.co https://apify.epayco.co https://secure.epayco.co wss:; " .
               "frame-src 'self' https://checkout.epayco.co https://www.youtube.com https://player.vimeo.com; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self' https://checkout.epayco.co;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
