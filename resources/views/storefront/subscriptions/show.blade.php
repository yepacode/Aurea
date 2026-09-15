@extends('layouts.app')

@section('title', $plan->name . ' — Ritual mensual Áurea')
@section('meta_description', 'Detalle del ritual mensual ' . $plan->name . '. Suscríbete y recibe tus productos cada ' . $plan->interval_days . ' días con descuento exclusivo.')

@push('head')
<style>
    .plan-detail{max-width:1180px;margin:0 auto;padding:clamp(56px,7vw,88px) 24px;display:grid;grid-template-columns:1.1fr 1fr;gap:56px;}
    @media(max-width:900px){.plan-detail{grid-template-columns:1fr;gap:36px;}}
    .plan-detail__cover{
        aspect-ratio:4/3;border-radius:24px;overflow:hidden;
        background:radial-gradient(circle at 30% 30%,#FDF5E5,#EAD2B6);position:relative;
    }
    .plan-detail__cover img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .plan-detail__eyebrow{font:600 11px/1 'Montserrat',sans-serif;letter-spacing:.28em;text-transform:uppercase;color:#BE9A53;margin-bottom:14px;}
    .plan-detail h1{font-family:'Playfair Display',serif;font-size:clamp(30px,4vw,44px);color:#2E2A26;margin:0 0 14px;}
    .plan-detail p.lede{color:#6B6157;font-size:16px;line-height:1.7;margin:0 0 24px;}
    .plan-detail__price{display:flex;align-items:baseline;gap:12px;margin:10px 0;}
    .plan-detail__price strong{font-family:'Playfair Display',serif;font-size:42px;color:#2E2A26;}
    .plan-detail__price s{color:#B4A99A;font-size:18px;}
    .plan-detail__price .off{background:#FBF6EC;color:#BE9A53;font-weight:700;font-size:12px;padding:6px 12px;border-radius:999px;letter-spacing:.14em;text-transform:uppercase;}
    .plan-detail__meta{color:#8B7B5C;font-size:14px;margin-bottom:22px;}
    .plan-detail__cta{
        display:inline-block;background:#2E2A26;color:#FBF8F2;text-decoration:none;
        padding:16px 28px;border-radius:999px;font:600 13px/1 'Montserrat',sans-serif;
        letter-spacing:.14em;text-transform:uppercase;transition:background .3s,transform .3s;
    }
    .plan-detail__cta:hover{background:#D9B56D;color:#3B310F;transform:translateY(-2px);}
    .plan-benefits{list-style:none;padding:0;margin:22px 0 28px;}
    .plan-benefits li{display:flex;gap:10px;padding:6px 0;font-size:14px;color:#3B310F;}
    .plan-benefits li::before{content:"✓";color:#D9B56D;font-weight:700;}

    .plan-products{max-width:1180px;margin:0 auto;padding:0 24px clamp(72px,10vw,120px);}
    .plan-products h2{font-family:'Playfair Display',serif;font-size:clamp(24px,3vw,32px);color:#2E2A26;margin:0 0 24px;text-align:center;}
    .plan-products__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;}
    .plan-product{background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:16px;padding:16px;text-align:center;}
    .plan-product img{max-width:100%;height:160px;object-fit:contain;margin-bottom:10px;}
    .plan-product h4{font-family:'Playfair Display',serif;font-size:16px;color:#2E2A26;margin:0 0 4px;}
    .plan-product .qty{font-size:12px;color:#8B7B5C;letter-spacing:.1em;text-transform:uppercase;}
</style>
@endpush

@section('content')
    @php
        $off = $plan->computedDiscountPercent();
        $img = $plan->image ? asset('storage/'.$plan->image) : null;
    @endphp
    <section class="plan-detail">
        <div class="plan-detail__cover">
            @if($img)<img src="{{ $img }}" alt="{{ $plan->name }}">@endif
        </div>
        <div>
            <p class="plan-detail__eyebrow">Ritual mensual · Suscripción</p>
            <h1>{{ $plan->name }}</h1>
            @if($plan->description)
                <p class="lede">{{ $plan->description }}</p>
            @endif

            <div class="plan-detail__price">
                <strong>${{ number_format((float) $plan->base_price, 0, ',', '.') }}</strong>
                @if((float) $plan->regular_price > (float) $plan->base_price)
                    <s>${{ number_format((float) $plan->regular_price, 0, ',', '.') }}</s>
                @endif
                @if($off > 0)<span class="off">-{{ $off }}%</span>@endif
            </div>
            <p class="plan-detail__meta">
                Entrega cada {{ $plan->interval_days }} días
                @if($plan->delivery_days_message) · {{ $plan->delivery_days_message }}@endif
            </p>

            <ul class="plan-benefits">
                <li>Descuento exclusivo de suscriptora</li>
                <li>Entrega automática puntual</li>
                <li>Pausa o cancela cuando quieras — sin permanencia</li>
                <li>Cambia tu dirección de entrega sin líos</li>
            </ul>

            <a href="{{ route('subscriptions.subscribe', $plan) }}" class="plan-detail__cta">Comenzar mi ritual</a>
        </div>
    </section>

    @if($plan->products->isNotEmpty())
        <section class="plan-products">
            <h2>Este ritual incluye</h2>
            <div class="plan-products__grid">
                @foreach($plan->products as $p)
                    @php $pImg = ($p->images[0] ?? null) ? asset('storage/'.$p->images[0]) : null; @endphp
                    <div class="plan-product">
                        @if($pImg)<img src="{{ $pImg }}" alt="{{ $p->name }}">@endif
                        <h4>{{ $p->name }}</h4>
                        <p class="qty">× {{ (int) $p->pivot->quantity }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
