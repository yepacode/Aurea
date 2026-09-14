@extends('layouts.app')

@section('title', 'Kits & Rituales — Belleza Áurea')
@section('meta_description', 'Descubre los kits y rituales de Belleza Áurea: combos curados de productos para tu piel, cabello y uñas con precios especiales y ahorro real.')

@push('head')
<style>
    .kits-hero{
        text-align:center;
        padding:clamp(72px,10vw,140px) 24px clamp(56px,8vw,96px);
        background:radial-gradient(120% 90% at 50% 0%, #FBF4E6 0%, #F7F3ED 55%, transparent 100%);
    }
    .kits-hero__eyebrow{
        font:600 11px/1 'Montserrat',sans-serif;
        letter-spacing:.28em;text-transform:uppercase;color:#BE9A53;margin-bottom:18px;
    }
    .kits-hero__title{
        font-family:'Playfair Display',serif;
        font-size:clamp(34px,5vw,58px);font-weight:500;line-height:1.08;
        color:#2E2A26;margin:0 auto 18px;max-width:820px;letter-spacing:-.01em;
    }
    .kits-hero__title em{font-style:italic;color:#D9B56D;}
    .kits-hero__sub{
        font-size:16px;line-height:1.7;color:#6B6157;max-width:560px;margin:0 auto;
    }

    .kits-grid{
        max-width:1280px;margin:0 auto;padding:0 24px clamp(72px,10vw,120px);
        display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px;
    }
    @media(max-width:960px){ .kits-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:600px){ .kits-grid{grid-template-columns:1fr;} }

    .kit-card{
        position:relative;
        background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.20);
        border-radius:20px;
        padding:0 0 28px;
        overflow:hidden;
        text-decoration:none;color:inherit;
        display:flex;flex-direction:column;
        transition:transform .45s cubic-bezier(.2,.7,.3,1),
                   box-shadow .45s cubic-bezier(.2,.7,.3,1),
                   border-color .45s ease;
        isolation:isolate;
    }
    .kit-card:hover{
        transform:translateY(-4px);
        box-shadow:0 28px 60px -20px rgba(190,154,83,.35),
                   0 8px 20px -8px rgba(46,42,38,.05);
        border-color:rgba(217,181,109,.5);
    }

    .kit-card__ribbon{
        position:absolute;top:18px;left:-6px;z-index:3;
        background:linear-gradient(135deg,#D9B56D 0%,#BE9A53 100%);
        color:#3B310F;
        padding:8px 16px 8px 18px;
        font:700 11px/1 'Montserrat',sans-serif;letter-spacing:.14em;text-transform:uppercase;
        border-radius:0 4px 4px 0;
        box-shadow:0 8px 18px -6px rgba(190,154,83,.55);
    }
    .kit-card__ribbon::after{
        content:"";position:absolute;left:0;bottom:-6px;
        border:3px solid transparent;border-top-color:#8a6c2c;border-right-color:#8a6c2c;
    }

    .kit-card__cover{
        position:relative;aspect-ratio:4/3;
        background:radial-gradient(circle at 30% 30%,#FBF4E6,#E8D1C5);
        overflow:hidden;
    }
    .kit-card__cover img{
        position:absolute;inset:0;width:100%;height:100%;object-fit:cover;
        transition:transform 1.2s cubic-bezier(.2,.7,.3,1);
    }
    .kit-card:hover .kit-card__cover img{ transform:scale(1.06); }

    /* Collage fallback (3-4 productos en composición floral) */
    .kit-collage{
        position:absolute;inset:0;display:grid;
        grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;
        gap:6px;padding:16px;
    }
    .kit-collage img{
        width:100%;height:100%;object-fit:cover;object-position:center;
        border-radius:12px;background:#fff;
        box-shadow:0 6px 18px -8px rgba(46,42,38,.18);
    }
    .kit-collage--1 img{ grid-column:span 2;grid-row:span 2; }
    .kit-collage--2 img:nth-child(1){ grid-column:span 2; }
    .kit-collage--3 img:nth-child(1){ grid-row:span 2; }
    .kit-collage__mono{
        position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
        font-family:'Playfair Display',serif;font-style:italic;
        color:#BE9A53;font-size:22px;letter-spacing:.04em;
    }

    .kit-card__body{padding:26px 26px 0;flex:1;display:flex;flex-direction:column;}
    .kit-card__title{
        font-family:'Playfair Display',serif;font-weight:500;
        font-size:24px;line-height:1.2;color:#2E2A26;
        margin:0 0 12px;letter-spacing:-.005em;
        transition:color .3s;
    }
    .kit-card:hover .kit-card__title{ color:#BE9A53; }

    .kit-card__items{
        list-style:none;padding:0;margin:0 0 20px;
        display:flex;flex-direction:column;gap:6px;
    }
    .kit-card__items li{
        display:flex;align-items:flex-start;gap:8px;
        font-size:13px;color:#6B6157;line-height:1.4;
    }
    .kit-card__items li::before{
        content:"";flex-shrink:0;width:14px;height:14px;margin-top:2px;
        background:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23BE9A53' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='20 6 9 17 4 12'/></svg>") center/contain no-repeat;
    }

    .kit-card__price-row{
        margin-top:auto;padding-top:16px;border-top:1px solid rgba(184,169,153,.18);
        display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;
    }
    .kit-card__price{
        font-family:'Playfair Display',serif;font-size:26px;font-weight:600;color:#2E2A26;
    }
    .kit-card__compare{
        font-size:15px;color:#B8A999;text-decoration:line-through;
    }
    .kit-card__pct{
        margin-left:auto;
        font:700 11px/1 'Montserrat',sans-serif;letter-spacing:.10em;
        color:#C97B6B;background:#FCEFE6;padding:5px 10px;border-radius:999px;
    }

    .kit-card__cta{
        margin:18px 26px 0;
        display:inline-flex;align-items:center;justify-content:center;gap:8px;
        padding:14px 22px;border-radius:999px;
        background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);
        color:#3B310F;
        font:600 12px/1 'Montserrat',sans-serif;letter-spacing:.16em;text-transform:uppercase;
        border:none;cursor:pointer;text-decoration:none;
        box-shadow:0 12px 28px -12px rgba(190,154,83,.55);
        transition:all .35s;
    }
    .kit-card__cta:hover{
        transform:translateY(-2px);
        box-shadow:0 20px 40px -12px rgba(190,154,83,.75);
    }

    .kits-empty{
        text-align:center;padding:80px 24px;color:#6B6157;
        max-width:520px;margin:0 auto;
    }
    .kits-empty__mark{
        font-family:'Playfair Display',serif;font-style:italic;font-size:80px;
        color:#D9B56D;opacity:.55;line-height:1;margin-bottom:16px;
    }
</style>
@endpush

@section('content')

<section class="kits-hero">
    <p class="kits-hero__eyebrow">— Kits &amp; Rituales</p>
    <h1 class="kits-hero__title">Ahorra <em>comprando en pack</em></h1>
    <p class="kits-hero__sub">
        Rituales completos curados por nuestro equipo. Productos que se potencian entre sí, a un precio pensado para que empieces a cuidarte hoy.
    </p>
</section>

@if($bundles->isEmpty())
    <div class="kits-empty">
        <div class="kits-empty__mark">✨</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:26px;color:#2E2A26;margin:0 0 12px;">Estamos preparando kits especiales</h2>
        <p style="line-height:1.7;">Muy pronto verás aquí combos con descuento pensados para tu ritual diario. Mientras tanto, explora nuestros <a href="{{ route('products.index') }}" style="color:#BE9A53;text-decoration:underline;">productos individuales</a>.</p>
    </div>
@else
    <div class="kits-grid">
        @foreach($bundles as $bundle)
            @php
                $items = $bundle->items->take(4);
                $count = $items->count();
                $collageClass = 'kit-collage kit-collage--'.$count;
            @endphp
            <a href="{{ route('bundles.show', $bundle->slug) }}" class="kit-card">
                @if($bundle->savings > 0)
                    <span class="kit-card__ribbon">
                        Ahorra ${{ number_format($bundle->savings, 0, ',', '.') }}
                    </span>
                @endif

                <div class="kit-card__cover">
                    @if($bundle->image_url)
                        <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}" loading="lazy">
                    @elseif($count > 0)
                        <div class="{{ $collageClass }}">
                            @foreach($items as $p)
                                @php $img = ($p->images[0] ?? null) ? asset('storage/'.$p->images[0]) : null; @endphp
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy">
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;background:#FBF4E6;border-radius:12px;">
                                        <span style="font-family:'Playfair Display',serif;color:#BE9A53;">{{ mb_substr($p->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="kit-collage__mono">Kit Áurea</div>
                    @endif
                </div>

                <div class="kit-card__body">
                    <h2 class="kit-card__title">{{ $bundle->name }}</h2>

                    <ul class="kit-card__items">
                        @foreach($bundle->items->take(4) as $p)
                            <li>
                                {{ $p->name }}
                                @if(($p->pivot->quantity ?? 1) > 1)
                                    <span style="color:#BE9A53;font-weight:600;">×{{ (int) $p->pivot->quantity }}</span>
                                @endif
                            </li>
                        @endforeach
                        @if($bundle->items->count() > 4)
                            <li style="color:#B8A999;font-style:italic;">+ {{ $bundle->items->count() - 4 }} más</li>
                        @endif
                    </ul>

                    <div class="kit-card__price-row">
                        <span class="kit-card__price">${{ number_format($bundle->price, 0, ',', '.') }}</span>
                        @if($bundle->compare_price > $bundle->price)
                            <span class="kit-card__compare">${{ number_format($bundle->compare_price, 0, ',', '.') }}</span>
                            <span class="kit-card__pct">-{{ $bundle->savings_percent }}%</span>
                        @endif
                    </div>
                </div>

                <span class="kit-card__cta">Ver el kit ✨</span>
            </a>
        @endforeach
    </div>
@endif

@endsection
