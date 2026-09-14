{{--
    Banner del programa "Recomienda y gana".
    Se muestra cuando el visitante entra con un ?ref=XXX válido y aún no ha
    iniciado sesión (a los clientes ya logueados no les aporta nada).
--}}
@php
    $refCode = session('referral_code');
    $showRefBanner = $refCode
        && ! auth('customer')->check()
        && ! request()->is('cuenta/registro')  // en el registro ya hay badge propio
        && ! request()->is('ingresar')
        && ! request()->is('admin*');

    $refReferrer = null;
    if ($showRefBanner) {
        $refReferrer = \App\Models\Customer::where('referral_code', $refCode)->first();
    }
@endphp

@if($showRefBanner && $refReferrer)
    <div role="region" aria-label="Programa de referidas"
         style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border-bottom:1px solid #E0BE77;color:#7A5E1C;">
        <div style="max-width:1200px;margin:0 auto;padding:10px 20px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:center;font-family:'Montserrat',sans-serif;font-size:13px;text-align:center;">
            <span>
                🎁 <strong>{{ $refReferrer->name ?: 'Una amiga' }}</strong> te recomendó Áurea — regístrate y recibe
                <strong>500 puntos de bienvenida</strong>.
            </span>
            <a href="{{ route('customer.register') }}"
               style="display:inline-block;padding:8px 18px;border-radius:9999px;text-decoration:none;font-weight:700;font-size:12px;color:#3B310F;background:#fff;box-shadow:0 8px 18px -12px rgba(122,94,28,.6);">
                Crear cuenta
            </a>
        </div>
    </div>
@endif
