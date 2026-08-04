@extends('layouts.app')

@section('title', $seoSettings->meta_title ?? 'Rituales de belleza | Belleza Áurea')
@section('meta_description', $seoSettings->meta_description ?? 'Rituales de belleza para uñas, piel y tu tocador. Pequeños gestos que se vuelven costumbre — con productos e insumos de Belleza Áurea.')
@section('canonical', $seoSettings->canonical_url ?? route('blue-light'))
@section('og_title', $seoSettings->og_title ?? 'Rituales de belleza | Belleza Áurea')
@section('og_description', $seoSettings->og_description ?? 'Cuidar tus uñas, tu piel y tu espacio no es rutina — es un ritual.')
@section('twitter_title', $seoSettings->twitter_title ?? 'Rituales de belleza | Belleza Áurea')
@section('twitter_description', $seoSettings->twitter_description ?? 'Cuidar tus uñas, tu piel y tu espacio no es rutina — es un ritual.')
@section('og_image', ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))

@push('schema')
    @if(!empty($breadcrumbSchema)){!! $breadcrumbSchema !!}@endif
@endpush

@section('content')
<style>
/* ═══ Rituales de belleza — editorial botánico ═══ */
.rit-hero{position:relative;overflow:hidden;background:#2E2A26;text-align:center;padding:clamp(72px,12vh,150px) 24px;}
.rit-hero__glow{position:absolute;inset:0;opacity:.35;pointer-events:none;
    background:radial-gradient(ellipse at 50% 32%, #D9B56D 0%, transparent 62%);}
.rit-hero__leaf{position:absolute;z-index:0;pointer-events:none;opacity:.55;}
.rit-hero__leaf--tl{top:clamp(24px,4vh,48px);left:clamp(20px,6vw,96px);width:90px;transform:rotate(-8deg);}
.rit-hero__leaf--br{bottom:clamp(24px,4vh,48px);right:clamp(20px,6vw,96px);width:104px;transform:rotate(184deg);}
@media(max-width:760px){.rit-hero__leaf{display:none;}}
.rit-hero__eyebrow{position:relative;z-index:1;display:inline-block;font:600 11px/1 'Montserrat',system-ui,sans-serif;
    letter-spacing:.24em;text-transform:uppercase;color:#E8CC92;}
.rit-hero__title{position:relative;z-index:1;font-family:'Playfair Display',serif;color:#FBF8F2;font-weight:600;
    font-size:clamp(38px,6vw,70px);line-height:1.04;margin:18px 0 16px;}
.rit-hero__title em{font-style:italic;color:#D9B56D;}
.rit-hero__sub{position:relative;z-index:1;color:rgba(247,243,237,.62);max-width:600px;margin:0 auto;
    font-size:16px;line-height:1.72;}

.rit-row{max-width:1150px;margin:0 auto;padding:clamp(28px,4vw,56px) 24px;
    display:grid;grid-template-columns:1fr 1fr;gap:clamp(32px,5vw,72px);align-items:center;}
.rit-media{position:relative;aspect-ratio:4/3;border-radius:24px;overflow:hidden;
    box-shadow:0 44px 84px -40px rgba(120,92,44,.5);border:1px solid rgba(217,181,109,.16);
    display:grid;place-items:center;}
.rit-media img.rit-photo{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.rit-media__wreath{width:44%;opacity:.55;position:relative;z-index:1;}
.rit-row--flip .rit-media{order:2;}
.rit-num{font-family:'Playfair Display',serif;font-style:italic;font-size:13px;letter-spacing:.06em;color:#BE9A53;}
.rit-title{font-family:'Playfair Display',serif;font-size:clamp(26px,3.4vw,42px);color:#2E2A26;line-height:1.08;
    margin:10px 0 16px;font-weight:600;}
.rit-title em{font-style:italic;color:#BE9A53;}
.rit-text{color:#6B6157;font-size:15.5px;line-height:1.75;margin:0 0 20px;max-width:460px;}
.rit-tips{list-style:none;padding:0;margin:0 0 24px;display:flex;flex-direction:column;gap:11px;max-width:470px;}
.rit-tips li{position:relative;padding-left:29px;color:#4A453F;font-size:14.5px;line-height:1.55;}
.rit-tips li::before{content:"";position:absolute;left:0;top:1px;width:18px;height:18px;border-radius:50%;
    background:linear-gradient(135deg,#E0BE77,#BE9A53);}
.rit-tips li::after{content:"";position:absolute;left:5.5px;top:6px;width:6px;height:3.5px;
    border-left:1.7px solid #fff;border-bottom:1.7px solid #fff;transform:rotate(-45deg);}
.rit-tips b{color:#2E2A26;font-weight:600;}
.rit-cta{display:inline-flex;align-items:center;gap:9px;text-decoration:none;
    font:600 12px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.14em;text-transform:uppercase;color:#2E2A26;
    border-bottom:1px solid rgba(217,181,109,.5);padding-bottom:4px;transition:color .3s ease,gap .3s ease;}
.rit-cta:hover{color:#BE9A53;gap:13px;}
.rit-close{text-align:center;padding:clamp(36px,5vw,72px) 24px clamp(56px,7vw,96px);}
.rit-close__btn{display:inline-flex;align-items:center;gap:11px;text-decoration:none;border-radius:999px;
    padding:17px 36px;color:#fff;font:600 12.5px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.14em;text-transform:uppercase;
    background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);box-shadow:0 16px 32px -12px rgba(190,154,83,.72);
    transition:transform .35s cubic-bezier(.2,.7,.3,1),box-shadow .35s ease;}
.rit-close__btn:hover{transform:translateY(-3px);box-shadow:0 22px 44px -12px rgba(190,154,83,.9);}
@media(max-width:820px){.rit-row{grid-template-columns:1fr;gap:26px;}.rit-row--flip .rit-media{order:0;}}
</style>

<section class="rit-hero">
    <div class="rit-hero__glow"></div>
    {{-- Detalles botánicos dorados + destellos de rocío --}}
    <svg class="rit-hero__leaf rit-hero__leaf--tl" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".7"/>
        <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".7"/>
        <ellipse cx="34" cy="72" rx="10" ry="3.6" transform="rotate(-45 34 72)" fill="#D9B56D" opacity=".6"/>
    </svg>
    <svg class="rit-hero__leaf rit-hero__leaf--br" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40 M 38 70 Q 70 60 70 30" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".65"/>
        <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".65"/>
    </svg>
    <span class="ba-dew" style="top:22%;left:18%;z-index:1;--d:0s" aria-hidden="true"></span>
    <span class="ba-dew" style="top:30%;right:16%;z-index:1;--d:1s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:26%;left:26%;z-index:1;--d:.6s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:22%;right:24%;z-index:1;--d:1.4s" aria-hidden="true"></span>

    <span class="rit-hero__eyebrow">Belleza Áurea · Un diario sensorial</span>
    <h1 class="rit-hero__title">{{ $blueLightPage->hero_title_prefix ?? 'Rituales de ' }}<em>{{ $blueLightPage->hero_title_accent ?? 'belleza' }}</em></h1>
    <p class="rit-hero__sub">{{ $blueLightPage->hero_subtitle ?? 'Pequeños gestos que se vuelven costumbre. Cuidar tus uñas, tu piel y tu espacio no es rutina — es un ritual. Estos son los nuestros.' }}</p>
</section>

{{-- Ritual 01 — Piel --}}
<div class="rit-row">
    <div class="rit-media" style="background:linear-gradient(155deg,#F0DCCF 0%,#D8B7A6 100%);">
        <img class="rit-media__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
    </div>
    <div data-anim="fade-up">
        <span class="rit-num">— Ritual 01</span>
        <h2 class="rit-title">El ritual de <em>piel</em></h2>
        <p class="rit-text">Nutre, calma, transforma. Una piel bien cuidada es la mejor base — estos son nuestros consejos para tu rutina diaria.</p>
        <ul class="rit-tips">
            <li><b>Doble limpieza</b> en la noche: primero un aceite para retirar maquillaje y protector, luego un gel suave.</li>
            <li>Aplica el sérum e hidratante con la <b>piel aún húmeda</b> para sellar mejor la hidratación.</li>
            <li><b>Protector solar cada mañana</b>, incluso en días nublados o si trabajas en interiores.</li>
            <li>Exfolia <b>1–2 veces por semana</b> con suavidad, sin frotar de más.</li>
        </ul>
        <a href="{{ route('ritual.show', 'ritual-de-piel') }}" class="rit-cta">Ver ritual completo
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</div>

@include('partials.ba-divider')

{{-- Ritual 02 — Uñas --}}
<div class="rit-row rit-row--flip">
    <div class="rit-media" style="background:linear-gradient(155deg,#BAC3AC 0%,#8F9C7E 100%);">
        <img class="rit-media__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
    </div>
    <div data-anim="fade-up">
        <span class="rit-num">— Ritual 02</span>
        <h2 class="rit-title">La galería de <em>uñas</em></h2>
        <p class="rit-text">Color que es arte. Un buen manicure empieza mucho antes del esmalte — así logras un acabado que rinde caja tras caja.</p>
        <ul class="rit-tips">
            <li><b>Prepara la uña</b>: empuja la cutícula y desengrasa la superficie antes de esmaltar.</li>
            <li>Aplica siempre una <b>capa base</b>: protege la uña y hace que el color dure más.</li>
            <li>Trabaja en <b>capas finas</b> y <b>sella la punta</b> para evitar que se despostille.</li>
            <li>Usa <b>aceite de cutícula</b> a diario para unas uñas sanas y flexibles.</li>
        </ul>
        <a href="{{ route('ritual.show', 'ritual-de-unas') }}" class="rit-cta">Ver ritual completo
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</div>

@include('partials.ba-divider')

{{-- Ritual 03 — El tocador --}}
<div class="rit-row">
    <div class="rit-media" style="background:linear-gradient(155deg,#E8CC92 0%,#C6A052 100%);">
        <img class="rit-media__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
    </div>
    <div data-anim="fade-up">
        <span class="rit-num">— Ritual 03</span>
        <h2 class="rit-title">El <em>tocador</em></h2>
        <p class="rit-text">La precisión del lujo. Tus herramientas son la mitad del resultado — cuídalas y tu trabajo lo notará.</p>
        <ul class="rit-tips">
            <li><b>Lava tus brochas</b> cada semana con jabón suave y déjalas secar en horizontal.</li>
            <li>Guarda las herramientas <b>secas y en su lugar</b> para que duren mucho más.</li>
            <li><b>Desinfecta</b> pinzas, cortaúñas y empujadores entre cada uso.</li>
            <li>Ten a mano <b>algodón y toallitas</b> para retoques y limpieza rápida.</li>
        </ul>
        <a href="{{ route('ritual.show', 'el-tocador') }}" class="rit-cta">Ver ritual completo
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</div>

@include('partials.ba-divider')

{{-- Ritual 04 — Maquillaje --}}
<div class="rit-row rit-row--flip">
    <div class="rit-media" style="background:linear-gradient(155deg,#DABFC9 0%,#B58EA0 100%);">
        <img class="rit-media__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
    </div>
    <div data-anim="fade-up">
        <span class="rit-num">— Ritual 04</span>
        <h2 class="rit-title">El toque de <em>color</em></h2>
        <p class="rit-text">Maquillaje que realza lo que ya eres. El broche de oro de tu ritual — con estos trucos luce natural y dura todo el día.</p>
        <ul class="rit-tips">
            <li>Empieza con la <b>piel preparada</b> (limpia e hidratada) para un acabado uniforme.</li>
            <li>Usa <b>primer</b> antes de la base para que el maquillaje aguante más horas.</li>
            <li>Aplica y difumina con <b>luz natural</b> siempre que puedas.</li>
            <li><b>Menos es más</b>: construye el color por capas en vez de aplicar mucho de una vez.</li>
        </ul>
        <a href="{{ route('ritual.show', 'ritual-de-maquillaje') }}" class="rit-cta">Ver ritual completo
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</div>

<div class="rit-close">
    <a href="{{ route('products.index') }}" class="rit-close__btn">Ver catálogo completo</a>
</div>
@endsection
