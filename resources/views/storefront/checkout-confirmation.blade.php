@extends('layouts.app')

@section('title', 'Pedido confirmado | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')

    <style>
        /* ===== Lluvia de pétalos de fiesta (colores de marca) ===== */
        #ba-celebrate{position:fixed;inset:0;pointer-events:none;z-index:60;overflow:hidden;}
        .ba-petal{
            position:absolute;top:-12vh;opacity:0;
            border-radius:100% 0 100% 0;
            box-shadow:0 1px 2px rgba(46,42,38,0.10);
            will-change:transform,opacity;
        }
        @keyframes ba-fall{
            0%{transform:translate3d(0,-12vh,0) rotate(0deg);opacity:0;}
            10%{opacity:1;}
            90%{opacity:1;}
            100%{transform:translate3d(var(--drift,30px),112vh,0) rotate(var(--spin,540deg));opacity:0;}
        }
        @media (prefers-reduced-motion: reduce){ #ba-celebrate{display:none;} }

        /* ===== Tarjeta de confirmación ===== */
        .cf-wrap{max-width:600px;margin:0 auto;padding:clamp(48px,7vw,88px) 20px clamp(72px,9vw,120px);text-align:center;}
        .cf-badge{
            width:88px;height:88px;border-radius:50%;margin:0 auto;
            display:flex;align-items:center;justify-content:center;
            animation:cf-pop .6s cubic-bezier(.2,.9,.3,1.4) both;
        }
        /* variantes */
        .cf-badge--ok{
            background:radial-gradient(circle at 35% 28%, #F5E7C6, #E4C384);
            color:#7A5F26;box-shadow:0 0 0 10px rgba(217,181,109,0.14), 0 18px 40px -18px rgba(190,154,83,0.6);
        }
        .cf-badge--aviso{
            background:radial-gradient(circle at 35% 28%, #FEF3C7, #FBBF24);
            color:#78350F;box-shadow:0 0 0 10px rgba(251,191,36,0.14), 0 18px 40px -18px rgba(251,191,36,0.6);
        }
        .cf-badge--error{
            background:radial-gradient(circle at 35% 28%, #FEE2E2, #FCA5A5);
            color:#7F1D1D;box-shadow:0 0 0 10px rgba(252,165,165,0.14), 0 18px 40px -18px rgba(239,68,68,0.6);
        }
        @keyframes cf-pop{0%{transform:scale(.4);opacity:0;}100%{transform:scale(1);opacity:1;}}
        .cf-title{font-family:'Playfair Display',serif;font-size:clamp(28px,4.5vw,40px);font-weight:700;color:#2E2A26;margin:22px 0 0;}
        .cf-order{display:inline-block;margin-top:12px;font-size:.8rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#8A6E2E;background:rgba(217,181,109,0.14);border:1px solid rgba(217,181,109,0.35);border-radius:9999px;padding:6px 16px;}
        .cf-text{margin:20px auto 0;max-width:460px;color:#6B6157;line-height:1.7;font-size:1rem;}
        .cf-actions{margin-top:34px;display:flex;flex-wrap:wrap;gap:12px;justify-content:center;}
        .cf-btn{
            display:inline-flex;align-items:center;gap:8px;padding:13px 26px;border-radius:9999px;
            font-family:'Montserrat',sans-serif;font-size:.85rem;font-weight:600;letter-spacing:.02em;
            transition:transform .2s, box-shadow .2s, filter .2s;
        }
        .cf-btn--gold{background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);color:#3B310F;box-shadow:0 10px 24px -13px rgba(190,154,83,0.85);}
        .cf-btn--gold:hover{transform:translateY(-1px);filter:brightness(1.04);}
        .cf-btn--ghost{background:transparent;color:#6B6157;border:1px solid rgba(46,42,38,0.18);}
        .cf-btn--ghost:hover{border-color:#D9B56D;color:#BE9A53;}
    </style>

    @php
        // Fix demo · antes esta vista mostraba SIEMPRE "¡Pedido confirmado!" aunque el
        //   cliente cerrara el widget ePayco sin pagar. Ahora resolvemos el estado real
        //   del pago (pagado / pendiente / rechazado / transferencia) y adaptamos título,
        //   badge, mensaje y acciones — con botón "Reintentar el pago" cuando aplica.
        $pagoOk       = $order->payment_status === 'paid';
        $pagoProceso  = in_array($order->payment_status, ['processing', 'pending_review'], true);
        $pagoFallo    = $order->payment_status === 'failed';
        $esTransfer   = $order->payment_method === 'transfer';
        $esEpayco     = $order->payment_method === 'epayco';
        // Pedido creado sin pago aún → ePayco pending sin ninguna referencia recibida.
        $pagoEsperando = ! $pagoOk && $esEpayco && $order->payment_status === 'pending';
        $celebrar      = $pagoOk || $esTransfer; // Solo animación en pagado o transferencia.

        if ($pagoOk) {
            $titulo = '¡Pedido confirmado!';
            $variante = 'ok';
        } elseif ($pagoEsperando || $pagoProceso) {
            $titulo = $pagoProceso ? 'Pago en proceso' : 'Tu pedido está creado — falta completar el pago';
            $variante = 'aviso';
        } elseif ($pagoFallo) {
            $titulo = 'El pago fue rechazado';
            $variante = 'error';
        } elseif ($esTransfer) {
            $titulo = '¡Pedido registrado!';
            $variante = 'ok';
        } else {
            $titulo = 'Tu pedido está creado';
            $variante = 'aviso';
        }
    @endphp

    {{-- Lluvia de pétalos solo cuando hay algo que celebrar --}}
    @if($celebrar)
    <div id="ba-celebrate" aria-hidden="true"></div>
    @endif

    <div class="cf-wrap">
        <div class="cf-badge cf-badge--{{ $variante }}">
            @if($variante === 'ok')
                <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
            @elseif($variante === 'aviso')
                <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 8v4m0 4h.01M4.93 19h14.14a2 2 0 0 0 1.75-2.97l-7.07-12.6a2 2 0 0 0-3.5 0L3.18 16.03A2 2 0 0 0 4.93 19Z"/>
                </svg>
            @else
                <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            @endif
        </div>

        <h1 class="cf-title">{{ $titulo }}</h1>
        <span class="cf-order">Pedido #{{ $order->id }}</span>

        @php $firstName = optional($order->customer)->name ? \Illuminate\Support\Str::of($order->customer->name)->before(' ') : null; @endphp
        <p class="cf-text">
            @if($pagoOk)
                Gracias por tu compra{{ $firstName ? ', ' . $firstName : '' }}. 💛
                Recibirás un correo de confirmación con todos los detalles de tu pedido.
            @elseif($esTransfer)
                Gracias{{ $firstName ? ', ' . $firstName : '' }}. Para completar tu pedido realiza la transferencia con los datos que encontrarás en el seguimiento y sube tu comprobante.
            @elseif($pagoEsperando)
                Guardamos tu pedido{{ $firstName ? ', ' . $firstName : '' }}, pero <strong>aún no hemos recibido el pago</strong>. Puedes retomar el pago desde el botón de abajo — es seguro, se procesa por ePayco.
            @elseif($pagoProceso)
                {{ $firstName ? $firstName . ',' : '' }} tu pago está siendo verificado. Cuando el banco confirme te avisaremos por correo — no necesitas hacer nada más.
            @elseif($pagoFallo)
                El banco rechazó el pago{{ $firstName ? ', ' . $firstName : '' }}. Puedes intentar de nuevo con otra tarjeta o con PSE, Nequi o efectivo.
            @else
                Guardamos tu pedido{{ $firstName ? ', ' . $firstName : '' }}. Estamos verificando el estado del pago.
            @endif
        </p>

        <div class="cf-actions">
            @if($esEpayco && ! $pagoOk && ! $pagoProceso)
                {{-- Botón principal: reintentar pago con ePayco --}}
                <a href="{{ route('epayco.pay', $order->id) }}" class="cf-btn cf-btn--gold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M3.75 5.25h16.5a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                    {{ $pagoFallo ? 'Reintentar el pago' : 'Completar el pago' }}
                </a>
            @endif

            @if($order->tracking_token)
                <a href="{{ route('order.track', $order->tracking_token) }}" class="cf-btn {{ ($esEpayco && ! $pagoOk && ! $pagoProceso) ? 'cf-btn--ghost' : 'cf-btn--gold' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    {{ $esTransfer ? 'Completar mi transferencia' : 'Ver mi pedido' }}
                </a>
            @endif
            <a href="{{ route('home') }}" class="cf-btn cf-btn--ghost">Volver al inicio</a>
        </div>

        {{-- ===== Bloque: Guarda este enlace (solo invitados con token) ===== --}}
        @guest('customer')
        @if($order->tracking_token)
        <div style="max-width:640px;margin:26px auto 0;padding:20px 22px;background:#FBF4E6;border:2px dashed #D9B56D;border-radius:14px;text-align:left;">
            <p style="font-size:12px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#BE9A53;margin:0 0 8px;">⚡ Importante · Guarda este enlace</p>
            <p style="font-size:14px;color:#3A352E;line-height:1.55;margin:0 0 12px;">Este es tu <strong>link personal de seguimiento</strong>. Guárdalo (o revisa tu correo, también te lo enviamos) para ver el estado de tu pedido en cualquier momento — sin necesidad de iniciar sesión.</p>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <input type="text" readonly value="{{ route('order.track', $order->tracking_token) }}" id="tracking-link-input"
                       style="flex:1;min-width:220px;padding:9px 12px;border:1px solid #E5DCC9;border-radius:8px;font-size:13px;color:#2E2A26;background:#fff;font-family:ui-monospace,monospace;">
                <button type="button" onclick="const el=document.getElementById('tracking-link-input');el.select();navigator.clipboard.writeText(el.value);this.textContent='✓ Copiado';setTimeout(()=>this.textContent='Copiar',1800);"
                        style="padding:9px 16px;background:#D9B56D;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Copiar
                </button>
            </div>
        </div>
        @endif
        @endguest

        {{-- ===== Bloque: Activa tu cuenta (invitados sin cuenta) ===== --}}
        @guest('customer')
        @if($order->customer && ! $order->customer->hasAccount())
        <div style="max-width:640px;margin:16px auto 0;padding:20px 22px;background:linear-gradient(160deg,#FEFCF8,#F8F2E8);border:1px solid rgba(217,181,109,.35);border-radius:14px;text-align:left;">
            <p style="font-family:'Playfair Display',serif;font-size:17px;font-weight:600;color:#2E2A26;margin:0 0 6px;">🌸 Crea tu cuenta en 1 clic</p>
            <p style="font-size:14px;color:#6B6157;line-height:1.55;margin:0 0 14px;">Guardamos tu perfil con este correo (<strong>{{ $order->customer->email }}</strong>). Activa tu cuenta con una contraseña para ver tu historial de pedidos, guardar direcciones y comprar más rápido.</p>
            <form method="POST" action="{{ route('customer.password.email') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="email" value="{{ $order->customer->email }}">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:8px;padding:12px 22px;background:linear-gradient(120deg,#E0BE77,#BE9A53);color:#fff;border:none;border-radius:999px;font-family:'Montserrat',sans-serif;font-size:12.5px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;box-shadow:0 12px 24px -10px rgba(190,154,83,.7);">
                    Recibir enlace para activar cuenta →
                </button>
            </form>
        </div>
        @endif
        @endguest

        {{-- ===== Bloque: Resumen de tu compra (ítems + total) ===== --}}
        <div style="max-width:640px;margin:26px auto 0;background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:22px;text-align:left;">
            <h3 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:600;color:#2E2A26;margin:0 0 14px;">Resumen de tu compra</h3>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;gap:10px;padding-bottom:8px;border-bottom:1px solid #F1EADC;">
                    <span style="font-size:13.5px;color:#3A352E;">{{ optional($item->product)->name ?? 'Producto' }} <span style="color:#B8A999;">× {{ $item->qty }}</span></span>
                    <span style="font-size:13.5px;color:#3A352E;font-variant-numeric:tabular-nums;">${{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div style="margin-top:14px;padding-top:12px;border-top:2px solid #EFE7D8;display:flex;justify-content:space-between;">
                <strong style="font-family:'Playfair Display',serif;color:#2E2A26;font-size:15px;">Total</strong>
                <strong style="font-family:'Playfair Display',serif;color:#BE9A53;font-size:16px;">${{ number_format($order->total, 0, ',', '.') }} COP</strong>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var box = document.getElementById('ba-celebrate');
        if (!box || reduce) return;

        var colors = ['#D9B56D', '#E8CC92', '#E8D1C5', '#A8B29A', '#F3E4C3', '#BE9A53'];
        var total = 46;

        for (var i = 0; i < total; i++) {
            var p = document.createElement('span');
            p.className = 'ba-petal';
            var size = 8 + Math.random() * 13;
            var color = colors[i % colors.length];
            p.style.left = (Math.random() * 100) + 'vw';
            p.style.width = size + 'px';
            p.style.height = (size * 1.35) + 'px';
            p.style.background = 'linear-gradient(135deg,' + color + ', rgba(255,255,255,0.45))';
            p.style.setProperty('--drift', (Math.random() * 260 - 130) + 'px');
            p.style.setProperty('--spin', (360 + Math.random() * 640) + 'deg');
            var dur = 4.5 + Math.random() * 4;
            var delay = Math.random() * 3.2;
            p.style.animation = 'ba-fall ' + dur.toFixed(2) + 's linear ' + delay.toFixed(2) + 's forwards';
            box.appendChild(p);
        }

        // Limpieza cuando termina la fiesta
        setTimeout(function () { if (box && box.parentNode) box.parentNode.removeChild(box); }, 13000);
    })();
</script>
@endpush
