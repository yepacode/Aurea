{{--
    ─── Google Analytics 4 + Meta Pixel ───────────────────────────
    Se activa cuando el admin captura las IDs en /admin/analytics (o en .env).

    Cómo obtener las IDs (para el cliente):
    - GA4 Measurement ID
        → https://analytics.google.com → Admin → Streams de datos → Web → Measurement ID
        → Formato: G-XXXXXXXXXX
    - Meta Pixel ID
        → https://business.facebook.com/events_manager → Data Sources → tu Pixel → Detalles
        → Formato: 15–16 dígitos (p. ej. 123456789012345)

    Ambos IDs se pegan en:
      1) Panel de admin: /admin/analytics (recomendado, sin tocar servidor)
      2) O en el .env de producción:
             GA4_MEASUREMENT_ID=G-XXXXXXXXXX
             META_PIXEL_ID=123456789012345
         y luego `php artisan config:clear`.

    En entorno local NO se imprime nada para no ensuciar métricas con desarrollo.
--}}
@php
    $__gaId   = trim((string) config('services.analytics.ga4', ''));
    $__fbqId  = trim((string) config('services.analytics.meta_pixel', ''));
    $__isProd = ! app()->environment('local');
@endphp

@if($__isProd && $__gaId)
    {{-- Google Analytics 4 (gtag.js) --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $__gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', @json($__gaId), { anonymize_ip: true });
    </script>
@endif

@if($__isProd && $__fbqId)
    {{-- Meta Pixel (fbq) --}}
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', @json($__fbqId));
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id={{ $__fbqId }}&ev=PageView&noscript=1"
             alt="">
    </noscript>
@endif
