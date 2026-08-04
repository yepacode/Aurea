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
            background:radial-gradient(circle at 35% 28%, #F5E7C6, #E4C384);
            color:#7A5F26;box-shadow:0 0 0 10px rgba(217,181,109,0.14), 0 18px 40px -18px rgba(190,154,83,0.6);
            animation:cf-pop .6s cubic-bezier(.2,.9,.3,1.4) both;
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

    {{-- Lluvia de pétalos --}}
    <div id="ba-celebrate" aria-hidden="true"></div>

    <div class="cf-wrap">
        <div class="cf-badge">
            <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m4.5 12.75 6 6 9-13.5"/>
            </svg>
        </div>

        <h1 class="cf-title">¡Pedido confirmado!</h1>
        <span class="cf-order">Pedido #{{ $order->id }}</span>

        @php $firstName = optional($order->customer)->name ? \Illuminate\Support\Str::of($order->customer->name)->before(' ') : null; @endphp
        <p class="cf-text">
            Gracias por tu compra{{ $firstName ? ', ' . $firstName : '' }}. 💛
            @if($order->payment_method === 'transfer')
                Para completar tu pedido, realiza la transferencia con los datos que encontrarás en el seguimiento y sube tu comprobante.
            @else
                Recibirás un correo de confirmación con todos los detalles de tu pedido.
            @endif
        </p>

        <div class="cf-actions">
            @if($order->tracking_token)
            <a href="{{ route('order.track', $order->tracking_token) }}" class="cf-btn cf-btn--gold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                {{ $order->payment_method === 'transfer' ? 'Completar mi transferencia' : 'Seguir mi pedido' }}
            </a>
            @endif
            <a href="{{ route('home') }}" class="cf-btn cf-btn--ghost">Volver al inicio</a>
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
