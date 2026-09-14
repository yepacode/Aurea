@extends('layouts.app')

@section('title', 'Estamos actualizando — Belleza Áurea')
@section('robots', 'noindex, follow')

@section('content')
<style>
    .ba-503{min-height:62vh;display:flex;align-items:center;justify-content:center;
        padding:clamp(56px,10vh,120px) 24px;text-align:center;
        background:radial-gradient(120% 90% at 70% 10%, #FFFFFF 0%, #F7F2E9 55%, #EFE7D8 100%);}
    .ba-503__inner{max-width:540px;}
    .ba-503__code{font-family:'Playfair Display',Georgia,serif;font-weight:600;
        font-size:clamp(88px,18vw,168px);line-height:.9;letter-spacing:-.02em;
        background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);
        -webkit-background-clip:text;background-clip:text;color:transparent;margin:0;}
    .ba-503__eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:600;
        letter-spacing:.24em;text-transform:uppercase;color:#BE9A53;margin-bottom:14px;}
    .ba-503__eyebrow::before,.ba-503__eyebrow::after{content:"";width:26px;height:1px;background:#D9B56D;}
    .ba-503__title{font-family:'Playfair Display',Georgia,serif;color:#2E2A26;
        font-size:clamp(24px,3.4vw,34px);margin:8px 0 14px;font-weight:600;}
    .ba-503__text{color:#6B6157;font-size:15px;line-height:1.7;margin:0 0 30px;}
    .ba-503__actions{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;}
    .ba-503__cta{display:inline-flex;align-items:center;gap:10px;text-decoration:none;border-radius:999px;
        padding:15px 30px;font:600 12px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.14em;text-transform:uppercase;}
    .ba-503__cta--gold{color:#fff;background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);
        box-shadow:0 16px 32px -12px rgba(190,154,83,.7);transition:transform .35s ease,box-shadow .35s ease;}
    .ba-503__cta--gold:hover{transform:translateY(-3px);box-shadow:0 22px 44px -12px rgba(190,154,83,.9);}
</style>

<section class="ba-503">
    <div class="ba-503__inner">
        <span class="ba-503__eyebrow">Belleza Áurea</span>
        <p class="ba-503__code">503</p>
        <h1 class="ba-503__title">Estamos actualizando la tienda</h1>
        <p class="ba-503__text">Volvemos en unos minutos con novedades. Gracias por tu paciencia.</p>
        <div class="ba-503__actions">
            <a href="{{ url('/') }}" class="ba-503__cta ba-503__cta--gold">Reintentar</a>
        </div>
    </div>
</section>
@endsection
