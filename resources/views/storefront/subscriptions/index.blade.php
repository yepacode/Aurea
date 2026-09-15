@extends('layouts.app')

@section('title', 'Rituales mensuales — Belleza Áurea')
@section('meta_description', 'Suscríbete a tus rituales de belleza y recíbelos automáticamente cada mes con descuento exclusivo de suscriptora. Pausa o cancela cuando quieras.')

@push('head')
<style>
    .subs-hero{
        text-align:center;
        padding:clamp(72px,10vw,140px) 24px clamp(48px,7vw,88px);
        background:radial-gradient(120% 90% at 50% 0%, #FDF5E5 0%, #F7F3ED 55%, transparent 100%);
    }
    .subs-hero__eyebrow{
        font:600 11px/1 'Montserrat',sans-serif;
        letter-spacing:.28em;text-transform:uppercase;color:#BE9A53;margin-bottom:18px;
    }
    .subs-hero__title{
        font-family:'Playfair Display',serif;
        font-size:clamp(34px,5vw,60px);font-weight:500;line-height:1.08;
        color:#2E2A26;margin:0 auto 18px;max-width:820px;letter-spacing:-.01em;
    }
    .subs-hero__title em{font-style:italic;color:#D9B56D;}
    .subs-hero__sub{font-size:17px;line-height:1.7;color:#6B6157;max-width:600px;margin:0 auto;}

    .subs-grid{
        max-width:1200px;margin:0 auto;padding:0 24px clamp(56px,8vw,88px);
        display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;
    }
    .plan-card{
        position:relative;
        background:#fff;
        border:1px solid rgba(217,181,109,.22);
        border-radius:22px;overflow:hidden;
        display:flex;flex-direction:column;
        transition:transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    }
    .plan-card:hover{
        transform:translateY(-4px);
        box-shadow:0 24px 60px -24px rgba(190,154,83,.45), 0 4px 12px -4px rgba(46,42,38,.08);
        border-color:rgba(217,181,109,.55);
    }
    .plan-card__cover{aspect-ratio:16/10;background:radial-gradient(circle at 30% 30%,#FDF5E5,#EAD2B6);position:relative;overflow:hidden;}
    .plan-card__cover img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .plan-card__badge{
        position:absolute;top:14px;right:14px;z-index:2;
        background:linear-gradient(135deg,#D9B56D,#BE9A53);color:#3B310F;
        padding:6px 12px;border-radius:999px;
        font:700 11px/1 'Montserrat',sans-serif;letter-spacing:.16em;text-transform:uppercase;
        box-shadow:0 6px 16px -6px rgba(190,154,83,.55);
    }
    .plan-card__body{padding:28px 26px 26px;display:flex;flex-direction:column;flex:1;}
    .plan-card__name{font-family:'Playfair Display',serif;font-size:26px;color:#2E2A26;margin:0 0 6px;line-height:1.15;}
    .plan-card__desc{font-size:14px;line-height:1.6;color:#6B6157;margin:0 0 18px;}

    .plan-card__items{margin:0 0 20px;padding:16px 18px;background:#FBF6EC;border-radius:12px;list-style:none;}
    .plan-card__items li{
        font-size:13px;color:#3B310F;padding:5px 0;display:flex;gap:8px;align-items:flex-start;
    }
    .plan-card__items li::before{content:"✓";color:#D9B56D;font-weight:700;flex-shrink:0;}

    .plan-card__price{display:flex;align-items:baseline;gap:10px;margin:8px 0 4px;}
    .plan-card__price strong{font-family:'Playfair Display',serif;font-size:32px;color:#2E2A26;}
    .plan-card__price s{color:#B4A99A;font-size:15px;}
    .plan-card__off{font-size:12px;font-weight:700;color:#BE9A53;letter-spacing:.14em;text-transform:uppercase;margin-bottom:4px;}
    .plan-card__meta{font-size:13px;color:#8B7B5C;margin-bottom:20px;}

    .plan-card__cta{
        display:inline-block;text-align:center;text-decoration:none;
        background:#2E2A26;color:#FBF8F2;
        padding:14px 22px;border-radius:999px;
        font:600 13px/1 'Montserrat',sans-serif;letter-spacing:.14em;text-transform:uppercase;
        transition:background .3s,transform .3s;
    }
    .plan-card__cta:hover{background:#D9B56D;color:#3B310F;transform:translateY(-2px);}

    .subs-how{
        max-width:1100px;margin:0 auto;padding:clamp(56px,8vw,88px) 24px;
        text-align:center;
    }
    .subs-how h2{font-family:'Playfair Display',serif;font-size:clamp(28px,4vw,42px);color:#2E2A26;margin:0 0 12px;}
    .subs-how p.lede{color:#6B6157;max-width:620px;margin:0 auto 44px;font-size:16px;line-height:1.6;}
    .subs-how__steps{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;text-align:center;}
    @media(max-width:720px){.subs-how__steps{grid-template-columns:1fr;}}
    .subs-how__step{
        background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:20px;
        padding:32px 24px 28px;
    }
    .subs-how__num{
        display:inline-flex;align-items:center;justify-content:center;
        width:52px;height:52px;border-radius:50%;
        background:linear-gradient(135deg,#D9B56D,#BE9A53);color:#3B310F;
        font-family:'Playfair Display',serif;font-size:22px;font-weight:600;margin-bottom:16px;
    }
    .subs-how__step h3{font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;margin:0 0 8px;}
    .subs-how__step p{color:#6B6157;font-size:14px;line-height:1.6;margin:0;}

    .subs-faq{
        max-width:840px;margin:0 auto;padding:0 24px clamp(72px,10vw,120px);
    }
    .subs-faq h2{font-family:'Playfair Display',serif;font-size:clamp(26px,4vw,36px);color:#2E2A26;margin:0 0 24px;text-align:center;}
    .subs-faq details{
        background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:14px;
        padding:18px 22px;margin-bottom:12px;
    }
    .subs-faq summary{
        cursor:pointer;list-style:none;font-family:'Playfair Display',serif;
        font-size:17px;color:#2E2A26;font-weight:500;
    }
    .subs-faq summary::-webkit-details-marker{display:none;}
    .subs-faq details p{margin:12px 0 0;color:#6B6157;font-size:14px;line-height:1.7;}

    .subs-empty{
        max-width:600px;margin:40px auto 80px;padding:40px 24px;text-align:center;
        background:#FBF6EC;border-radius:20px;color:#6B6157;
    }
</style>
@endpush

@section('content')
<section class="subs-hero">
    <p class="subs-hero__eyebrow">Belleza Áurea · Rituales</p>
    <h1 class="subs-hero__title">🌸 Rituales <em>Áurea</em><br>a domicilio, cada mes</h1>
    <p class="subs-hero__sub">Tu ritual favorito, siempre listo. Suscríbete y recibe tus productos con descuento exclusivo — pausa o cancela cuando quieras.</p>
</section>

<section class="subs-grid">
    @forelse($plans as $plan)
        @php
            $off = $plan->computedDiscountPercent();
            $img = $plan->image ? asset('storage/'.$plan->image) : null;
        @endphp
        <article class="plan-card">
            <div class="plan-card__cover">
                @if($off > 0)<span class="plan-card__badge">-{{ $off }}%</span>@endif
                @if($img)<img src="{{ $img }}" alt="{{ $plan->name }}" loading="lazy">@endif
            </div>
            <div class="plan-card__body">
                <h2 class="plan-card__name">{{ $plan->name }}</h2>
                @if($plan->description)
                    <p class="plan-card__desc">{{ \Illuminate\Support\Str::limit($plan->description, 140) }}</p>
                @endif

                @if($plan->products->isNotEmpty())
                    <ul class="plan-card__items">
                        @foreach($plan->products->take(5) as $p)
                            <li>{{ $p->name }} @if($p->pivot->quantity > 1)<span style="color:#8B7B5C;">× {{ $p->pivot->quantity }}</span>@endif</li>
                        @endforeach
                        @if($plan->products->count() > 5)
                            <li style="color:#8B7B5C;">+ {{ $plan->products->count() - 5 }} producto(s) más</li>
                        @endif
                    </ul>
                @endif

                @if($off > 0)<p class="plan-card__off">Ahorras {{ $off }}%</p>@endif
                <div class="plan-card__price">
                    <strong>${{ number_format((float) $plan->base_price, 0, ',', '.') }}</strong>
                    @if((float) $plan->regular_price > (float) $plan->base_price)
                        <s>${{ number_format((float) $plan->regular_price, 0, ',', '.') }}</s>
                    @endif
                </div>
                <p class="plan-card__meta">
                    Entrega cada {{ $plan->interval_days }} días
                    @if($plan->delivery_days_message) · {{ $plan->delivery_days_message }}@endif
                </p>

                <div style="margin-top:auto;display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('subscriptions.show', $plan) }}" class="plan-card__cta">Suscribirme</a>
                </div>
            </div>
        </article>
    @empty
        <div class="subs-empty">
            Aún no hay rituales disponibles. ¡Vuelve pronto! 🌷
        </div>
    @endforelse
</section>

<section class="subs-how">
    <h2>¿Cómo funciona?</h2>
    <p class="lede">Es cuidarte sin pensarlo. Nosotras nos encargamos de tener tu ritual listo cada mes.</p>
    <div class="subs-how__steps">
        <div class="subs-how__step">
            <div class="subs-how__num">1</div>
            <h3>Elige tu ritual</h3>
            <p>Selecciona el plan que se adapta a ti. Cada uno viene con una curaduría de nuestros productos favoritos.</p>
        </div>
        <div class="subs-how__step">
            <div class="subs-how__num">2</div>
            <h3>Recíbelo en casa</h3>
            <p>Enviamos tu ritual automáticamente al ritmo que elegiste — cada 30 o 60 días. Sin recordar, sin volver a pedir.</p>
        </div>
        <div class="subs-how__step">
            <div class="subs-how__num">3</div>
            <h3>Pausa o cancela</h3>
            <p>Tú tienes el control total desde Mi cuenta. Sin permanencia, sin letras chicas.</p>
        </div>
    </div>
</section>

<section class="subs-faq">
    <h2>Preguntas frecuentes</h2>
    <details>
        <summary>¿Puedo pausar mi suscripción cuando quiera?</summary>
        <p>Sí. Desde Mi cuenta puedes pausar tu ritual en un clic — no enviaremos nada hasta que la reactives.</p>
    </details>
    <details>
        <summary>¿Cómo cancelo?</summary>
        <p>Igual de fácil. Entra a Mi cuenta → Suscripciones y presiona "Cancelar". No hay permanencia ni penalizaciones.</p>
    </details>
    <details>
        <summary>¿Puedo cambiar mi dirección de entrega?</summary>
        <p>Sí, puedes actualizarla en cualquier momento y se aplicará a la próxima entrega automática.</p>
    </details>
    <details>
        <summary>¿Qué métodos de pago aceptan?</summary>
        <p>Puedes elegir pago recurrente por ePayco o pago contra entrega (efectivo) según disponibilidad.</p>
    </details>
</section>
@endsection
