@extends('layouts.app')

{{-- ================================================================
     SEO — meta + JSON-LD
     Todo el copy se hereda de SeoSetting (admin) y se complementa con
     schemas estructurados para Organization, WebSite, ItemList y FAQ.
     ================================================================ --}}
@section('title', $seoSettings->meta_title ?? 'Belleza Áurea | Cosmética natural, elegante y atemporal')
@section('meta_description', $seoSettings->meta_description ?? 'Skincare, fragancias y rituales premium con ingredientes botánicos.')
@section('og_title', $seoSettings->og_title ?? $seoSettings->meta_title ?? 'Belleza Áurea')
@section('og_description', $seoSettings->og_description ?? $seoSettings->meta_description ?? 'Belleza natural, elegante y atemporal.')
@section('twitter_title', $seoSettings->twitter_title ?? $seoSettings->meta_title ?? 'Belleza Áurea')
@section('twitter_description', $seoSettings->twitter_description ?? $seoSettings->meta_description ?? 'Belleza natural, elegante y atemporal.')
@section('og_image', ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))

@push('schema')
    {{-- Organization JSON-LD (vienen como <script>...</script> completos del SeoService) --}}
    @if(!empty($organizationSchema))
        {!! $organizationSchema !!}
    @endif

    {{-- FAQ JSON-LD --}}
    @if(!empty($faqSchema))
        {!! $faqSchema !!}
    @endif

    {{-- WebSite + SearchAction + ItemList — keys con chr(64) para evitar que
         el preprocesador de Blade interprete @context / @type como directivas. --}}
    @php
        $K_CTX = chr(64).'context';
        $K_TYP = chr(64).'type';

        $websiteLd = json_encode([
            $K_CTX => 'https://schema.org',
            $K_TYP => 'WebSite',
            'name' => 'Belleza Áurea',
            'url' => url('/'),
            'inLanguage' => 'es',
            'potentialAction' => [
                $K_TYP => 'SearchAction',
                'target' => url('/productos') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $itemListLd = $lentes->isNotEmpty()
            ? json_encode([
                $K_CTX => 'https://schema.org',
                $K_TYP => 'ItemList',
                'name' => 'Productos destacados',
                'itemListElement' => $lentes->take(8)->values()->map(fn ($p, $i) => [
                    $K_TYP => 'ListItem',
                    'position' => $i + 1,
                    'url' => route('products.show', ['slug' => $p->slug]),
                    'name' => $p->name,
                ])->all(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : null;
    @endphp
    <script type="application/ld+json">{!! $websiteLd !!}</script>
    @if($itemListLd)
    <script type="application/ld+json">{!! $itemListLd !!}</script>
    @endif
@endpush

@push('head')
<style>
    /* ============================================
       Belleza Áurea — Home rediseño minimalista
       Tokens viven en resources/css/app.css (@theme).
       Aquí solo estilos específicos del home.
       ============================================ */

    .ba-hero {
        position: relative;
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        min-height: clamp(560px, 92vh, 880px);
        background: #FFFFFF;
        overflow: hidden;
    }

    .ba-hero__left {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: clamp(48px, 8vw, 120px) clamp(24px, 6vw, 96px);
        position: relative;
        z-index: 2;
    }

    .ba-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #BE9A53;
        margin-bottom: 28px;
    }
    .ba-eyebrow::before {
        content: "";
        display: block;
        width: 32px;
        height: 1px;
        background: #D9B56D;
    }

    .ba-hero__title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(40px, 5vw, 72px);
        font-weight: 500;
        line-height: 1.04;
        letter-spacing: -0.015em;
        color: #2E2A26;
        margin: 0 0 28px;
    }
    .ba-hero__title em {
        font-style: italic;
        font-weight: 500;
        color: #D9B56D;
    }

    .ba-hero__sub {
        font-size: 16px;
        line-height: 1.65;
        color: #6B6157;
        max-width: 460px;
        margin: 0 0 36px;
    }

    .ba-hero__actions {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
        margin-bottom: 48px;
    }

    /* ─────────────────────────────────────────
       CTAs — Pill premium con shimmer y arrow micro-anim
       Estilo Aesop / Le Labo / NET-A-PORTER
       ───────────────────────────────────────── */
    .ba-btn-primary,
    .ba-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        text-decoration: none;
        transition: all .45s cubic-bezier(.2,.7,.3,1);
        font-family: 'Montserrat', sans-serif;
        position: relative;
        white-space: nowrap;
    }

    /* Primary: pill negra → gold al hover, con shimmer diagonal */
    .ba-btn-primary {
        background: #2E2A26;
        color: #FFFFFF;
        padding: 18px 36px;
        border-radius: 999px;
        overflow: hidden;
        box-shadow: 0 8px 24px -8px rgba(46, 42, 38, 0.4);
    }
    .ba-btn-primary::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(110deg, #D9B56D 0%, #E8CC92 50%, #D9B56D 100%);
        transform: translateX(-101%);
        transition: transform .55s cubic-bezier(.2,.7,.3,1);
        z-index: 0;
    }
    .ba-btn-primary > * { position: relative; z-index: 1; }
    .ba-btn-primary:hover {
        color: #2E2A26;
        box-shadow: 0 14px 32px -10px rgba(217, 181, 109, 0.55);
        transform: translateY(-2px);
    }
    .ba-btn-primary:hover::before { transform: translateX(0); }
    .ba-btn-primary svg { transition: transform .35s cubic-bezier(.2,.7,.3,1); }
    .ba-btn-primary:hover svg { transform: translateX(4px); }

    /* Secondary: pill outline taupe → gold + ink al hover */
    .ba-btn-ghost {
        color: #2E2A26;
        padding: 18px 32px;
        border-radius: 999px;
        border: 1px solid rgba(184, 169, 153, 0.6);
        background: transparent;
    }
    .ba-btn-ghost:hover {
        color: #2E2A26;
        border-color: #D9B56D;
        background: rgba(217, 181, 109, 0.08);
    }
    .ba-btn-ghost::after {
        content: "";
        display: inline-block;
        width: 18px;
        height: 1px;
        background: currentColor;
        transition: width .35s cubic-bezier(.2,.7,.3,1);
        margin-left: 2px;
    }
    .ba-btn-ghost:hover::after { width: 28px; background: #D9B56D; }

    .ba-hero__meta {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }
    .ba-hero__meta-item {
        display: flex;
        flex-direction: column;
    }
    .ba-hero__meta-num {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 600;
        color: #2E2A26;
        line-height: 1;
    }
    .ba-hero__meta-lbl {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #B8A999;
        margin-top: 6px;
    }

    .ba-hero__right {
        position: relative;
        background: radial-gradient(circle at 30% 25%, #FBF4E6 0%, #F7F3ED 50%, #E8D1C5 100%);
        overflow: hidden;
    }
    .ba-hero__right::before,
    .ba-hero__right::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .55;
        pointer-events: none;
    }
    .ba-hero__right::before {
        width: 340px; height: 340px;
        top: -80px; right: -80px;
        background: rgba(217,181,109,.35);
    }
    .ba-hero__right::after {
        width: 280px; height: 280px;
        bottom: -60px; left: -40px;
        background: rgba(168,178,154,.35);
    }
    .ba-hero__product {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px;
    }
    /* Si hay video, quitar padding para fullbleed cinematográfico */
    .ba-hero__product:has(video) { padding: 0; }
    .ba-hero__product img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 40px 80px rgba(190,154,83,.25));
        transition: transform 1.2s cubic-bezier(.2,.7,.3,1);
    }
    .ba-hero__product img:hover { transform: scale(1.03); }
    /* Video b-roll fullbleed dentro del panel derecho */
    .ba-hero__video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0;
        display: block;
        /* Fondo durante la carga del video — gradiente áureo en vez de imagen flash */
        background: linear-gradient(135deg, #F7F3ED 0%, #E8D1C5 100%);
    }

    .ba-hero__leaf {
        position: absolute;
        opacity: .4;
        pointer-events: none;
    }

    /* Marquee trust */
    .ba-marquee {
        background: #F7F3ED;
        padding: 22px 0;
        overflow: hidden;
        border-top: 1px solid rgba(184,169,153,.18);
        border-bottom: 1px solid rgba(184,169,153,.18);
    }
    .ba-marquee__track {
        display: flex;
        gap: 64px;
        white-space: nowrap;
        animation: ba-marquee 38s linear infinite;
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        color: #B8A999;
        font-style: italic;
        letter-spacing: 0.04em;
    }
    .ba-marquee:hover .ba-marquee__track { animation-play-state: paused; }
    .ba-marquee__item { display: inline-flex; align-items: center; gap: 12px; }
    .ba-marquee__dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #D9B56D; flex-shrink: 0;
    }
    @keyframes ba-marquee {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }

    /* Containers */
    .ba-container {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 clamp(24px, 5vw, 80px);
    }

    .ba-section {
        padding: clamp(72px, 10vw, 140px) 0;
    }
    .ba-section--cream { background: #F7F3ED; }
    .ba-section--ink   { background: #2E2A26; color: #F7F3ED; }
    .ba-section--cream-soft { background: #FBF8F2; }

    /* Section heads */
    .ba-section-head {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: clamp(48px, 7vw, 88px);
    }
    .ba-section-head__label {
        font-size: 11px;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: #BE9A53;
        margin-bottom: 16px;
        font-weight: 500;
    }
    .ba-section-head__title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(30px, 4vw, 52px);
        font-weight: 500;
        line-height: 1.1;
        letter-spacing: -0.01em;
        color: inherit;
        margin: 0 0 16px;
        max-width: 720px;
    }
    .ba-section-head__sub {
        font-size: 16px;
        line-height: 1.7;
        color: #6B6157;
        max-width: 560px;
        margin: 0;
    }
    .ba-section--ink .ba-section-head__title { color: #FBF8F2; }
    .ba-section--ink .ba-section-head__sub   { color: rgba(247,243,237,.65); }

    .ba-divider {
        width: 36px;
        height: 1px;
        background: #D9B56D;
        margin: 24px auto 0;
    }

    /* ─────────────────────────────────────────
       Categories — Magazine grid premium
       Layout asimétrico: primera card 2x, resto 1x
       Hover sofisticado: zoom imagen + lift + overlay áureo
       ───────────────────────────────────────── */
    .ba-cats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: 320px;
        gap: 16px;
    }
    /* Primera card: doble ancho + alto x2 (hero de la sección) */
    .ba-cats > .ba-cat:first-child {
        grid-column: span 2;
        grid-row: span 2;
    }

    .ba-cat {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 4px;
        text-decoration: none;
        background: #EDE6D8;
        box-shadow: 0 1px 3px rgba(46,42,38,.05);
        transition: transform .55s cubic-bezier(.2,.7,.3,1),
                    box-shadow .55s cubic-bezier(.2,.7,.3,1);
        isolation: isolate;
    }
    .ba-cat:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 50px -16px rgba(190,154,83,.35);
    }
    .ba-cat__bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transform: scale(1.02);
        transition: transform 1.4s cubic-bezier(.2,.7,.3,1), filter .55s ease;
        z-index: 0;
    }
    .ba-cat:hover .ba-cat__bg { transform: scale(1.12); }

    /* Overlay base — sutil para legibilidad */
    .ba-cat__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg,
            rgba(46,42,38,0) 0%,
            rgba(46,42,38,.15) 45%,
            rgba(46,42,38,.78) 100%);
        z-index: 1;
        transition: background .55s ease;
    }
    .ba-cat:hover .ba-cat__overlay {
        background: linear-gradient(180deg,
            rgba(190,154,83,.05) 0%,
            rgba(46,42,38,.30) 45%,
            rgba(46,42,38,.88) 100%);
    }

    /* Marco dorado interior aparece al hover */
    .ba-cat::after {
        content: "";
        position: absolute;
        inset: 12px;
        border: 1px solid transparent;
        border-radius: 2px;
        transition: border-color .45s ease;
        z-index: 2;
        pointer-events: none;
    }
    .ba-cat:hover::after {
        border-color: rgba(217,181,109,.6);
    }

    .ba-cat__body {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 28px;
        color: #FFFFFF;
        z-index: 3;
    }
    .ba-cats > .ba-cat:first-child .ba-cat__body { padding: 40px; }

    .ba-cat__count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #D9B56D;
        font-weight: 600;
        margin-bottom: 12px;
        opacity: .95;
    }
    .ba-cat__count::before {
        content: "";
        width: 18px;
        height: 1px;
        background: #D9B56D;
    }

    .ba-cat__name {
        font-family: 'Playfair Display', serif;
        font-size: clamp(22px, 2.4vw, 28px);
        font-weight: 500;
        line-height: 1.1;
        margin: 0 0 8px;
        letter-spacing: -.005em;
        transform: translateY(0);
        transition: transform .45s cubic-bezier(.2,.7,.3,1);
    }
    .ba-cats > .ba-cat:first-child .ba-cat__name {
        font-size: clamp(32px, 3.2vw, 44px);
    }

    .ba-cat__desc {
        font-size: 13px;
        line-height: 1.55;
        opacity: 0;
        max-height: 0;
        margin: 0;
        transform: translateY(8px);
        transition: opacity .45s ease, max-height .45s ease,
                    transform .45s cubic-bezier(.2,.7,.3,1), margin .45s ease;
    }
    .ba-cat:hover .ba-cat__desc {
        opacity: .85;
        max-height: 80px;
        margin: 0 0 16px;
        transform: translateY(0);
    }

    .ba-cat__cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #FFFFFF;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,.18);
        transition: color .35s ease, border-color .35s ease;
    }
    .ba-cat:hover .ba-cat__cta {
        color: #D9B56D;
        border-color: rgba(217,181,109,.45);
    }
    .ba-cat__cta svg {
        transition: transform .45s cubic-bezier(.2,.7,.3,1);
    }
    .ba-cat:hover .ba-cat__cta svg {
        transform: translateX(8px);
    }

    @media (max-width: 900px) {
        .ba-cats {
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 280px;
        }
        .ba-cats > .ba-cat:first-child {
            grid-column: span 2;
            grid-row: span 1;
        }
        .ba-cats > .ba-cat:first-child .ba-cat__name {
            font-size: clamp(28px, 6vw, 36px);
        }
    }
    @media (max-width: 540px) {
        .ba-cats {
            grid-template-columns: 1fr;
            grid-auto-rows: 240px;
        }
        .ba-cats > .ba-cat:first-child { grid-column: span 1; }
        .ba-cat__desc { opacity: .85; max-height: 80px; margin: 0 0 14px; transform: none; }
    }

    /* ─────────────────────────────────────────
       PRODUCT CARDS — Premium beauty store
       Estilo Aesop / Glossier / NET-A-PORTER
       ───────────────────────────────────────── */
    .ba-prods {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 48px 28px;
    }

    .ba-card {
        position: relative;
        display: block;
        text-decoration: none;
        color: inherit;
        isolation: isolate;
    }

    /* Imagen */
    .ba-card__img {
        position: relative;
        aspect-ratio: 4/5;
        background: linear-gradient(135deg, #FBF8F2 0%, #F7F3ED 60%, #EDE6D8 100%);
        overflow: hidden;
        border-radius: 4px;
        margin-bottom: 18px;
        transition: box-shadow .55s cubic-bezier(.2,.7,.3,1);
    }
    .ba-card:hover .ba-card__img {
        box-shadow: 0 30px 60px -20px rgba(190,154,83,.4);
    }
    .ba-card__img img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.6s cubic-bezier(.2,.7,.3,1),
                    filter .55s ease;
    }
    .ba-card:hover .ba-card__img img {
        transform: scale(1.08);
        filter: brightness(1.02);
    }

    /* Placeholder cuando no hay imagen — botánico decorativo */
    .ba-card__img--ph {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: radial-gradient(circle at 50% 35%, #FBF4E6, #F7F3ED 70%, #E8D1C5 100%);
        position: relative;
    }
    .ba-card__img--ph::before {
        content: "";
        position: absolute;
        inset: 20%;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' fill='none'%3E%3Cpath d='M20 80 Q 50 50 35 20 M 20 80 Q 60 70 70 35' stroke='%23A8B29A' stroke-width='1' stroke-linecap='round' opacity='0.45'/%3E%3Cellipse cx='42' cy='45' rx='10' ry='4' transform='rotate(-30 42 45)' fill='%23A8B29A' opacity='0.35'/%3E%3Cellipse cx='58' cy='28' rx='8' ry='3' transform='rotate(-15 58 28)' fill='%23A8B29A' opacity='0.35'/%3E%3C/svg%3E") center/contain no-repeat;
    }
    .ba-card__img--ph > span {
        position: relative;
        z-index: 1;
        color: #BE9A53;
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: 14px;
        letter-spacing: 0.04em;
        text-align: center;
    }

    /* Overlay con gradiente bottom para "Ver detalle" reveal */
    .ba-card__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 50%, rgba(46,42,38,.55) 100%);
        opacity: 0;
        transition: opacity .45s cubic-bezier(.2,.7,.3,1);
        pointer-events: none;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 18px;
    }
    .ba-card:hover .ba-card__overlay { opacity: 1; }

    .ba-card__quick {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #FFFFFF;
        background: rgba(217,181,109,.95);
        padding: 10px 18px;
        border-radius: 999px;
        transform: translateY(10px);
        opacity: 0;
        transition: transform .55s cubic-bezier(.2,.7,.3,1), opacity .35s ease;
        align-self: center;
    }
    .ba-card:hover .ba-card__quick {
        transform: translateY(0);
        opacity: 1;
        transition-delay: .08s;
    }

    /* Badges esquina superior izquierda */
    .ba-card__badge {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        padding: 5px 10px;
        border-radius: 999px;
        backdrop-filter: blur(8px);
    }
    .ba-card__badge--featured {
        background: rgba(217,181,109,.95);
        color: #2E2A26;
    }
    .ba-card__badge--new {
        background: rgba(168,178,154,.95);
        color: #FFFFFF;
    }
    .ba-card__badge--discount {
        background: rgba(201,123,107,.95);
        color: #FFFFFF;
    }
    .ba-card__badge--out {
        background: rgba(46,42,38,.85);
        color: #FFFFFF;
    }

    /* Wishlist heart top-right */
    .ba-card__wish {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,.92);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #B8A999;
        cursor: pointer;
        opacity: 0;
        transform: scale(.85);
        transition: opacity .35s ease, transform .35s ease, color .25s ease;
        backdrop-filter: blur(6px);
    }
    .ba-card:hover .ba-card__wish {
        opacity: 1;
        transform: scale(1);
    }
    .ba-card__wish:hover { color: #C97B6B; }

    /* Info debajo de la imagen */
    .ba-card__brand {
        font-size: 10px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        color: #BE9A53;
        font-weight: 600;
        margin: 0 0 4px;
    }
    .ba-card__cat {
        font-size: 10px;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #B8A999;
        margin: 0 0 6px;
    }
    .ba-card__name {
        font-family: 'Playfair Display', serif;
        font-size: 17px;
        font-weight: 500;
        color: #2E2A26;
        line-height: 1.25;
        margin: 0 0 10px;
        transition: color .25s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ba-card:hover .ba-card__name { color: #BE9A53; }

    .ba-card__price-row {
        display: flex;
        align-items: baseline;
        gap: 10px;
        flex-wrap: wrap;
    }
    .ba-card__price {
        font-size: 17px;
        font-weight: 600;
        color: #2E2A26;
        font-family: 'Playfair Display', serif;
    }
    .ba-card__compare {
        font-size: 13px;
        color: #B8A999;
        text-decoration: line-through;
    }
    .ba-card__discount {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: #C97B6B;
        background: #FCEFE6;
        padding: 2px 7px;
        border-radius: 4px;
    }

    /* Color dots para variantes */
    .ba-card__dots {
        display: flex;
        gap: 5px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .ba-card__dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid rgba(184,169,153,.4);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.5);
        transition: transform .25s ease;
    }
    .ba-card:hover .ba-card__dot { transform: scale(1.08); }
    .ba-card__dot--more {
        font-size: 9px;
        color: #B8A999;
        border: none;
        background: transparent;
        font-weight: 600;
        align-self: center;
        margin-left: 2px;
    }

    /* ─── Tabs filtro de productos ─── */
    .ba-tabs {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin: 0 auto 48px;
        flex-wrap: wrap;
        padding: 6px;
        background: #FFFFFF;
        border: 1px solid rgba(184,169,153,.25);
        border-radius: 999px;
        width: fit-content;
        max-width: 100%;
    }
    .ba-tab {
        padding: 10px 22px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #6B6157;
        background: transparent;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        transition: all .35s cubic-bezier(.2,.7,.3,1);
        white-space: nowrap;
    }
    .ba-tab:hover { color: #2E2A26; }
    .ba-tab.is-active {
        background: #2E2A26;
        color: #FFFFFF;
        box-shadow: 0 6px 18px -6px rgba(46,42,38,.35);
    }

    /* Animación cuando cambia el filtro */
    .ba-card[hidden] { display: none !important; }
    .ba-card.ba-filtering {
        animation: cardFadeIn .55s cubic-bezier(.2,.7,.3,1) both;
    }
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(20px) scale(.98); }
        to   { opacity: 1; transform: none; }
    }

    /* Star product split */
    .ba-star {
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        gap: 64px;
        align-items: center;
    }
    .ba-star__visual {
        position: relative;
        aspect-ratio: 4/5;
        background: radial-gradient(circle at 30% 30%, #FBF4E6, #E8D1C5 80%);
        overflow: hidden;
        border-radius: 2px;
    }
    .ba-star__visual img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .ba-star__body { padding: 16px 0; }
    .ba-star__label {
        font-size: 11px;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: #BE9A53;
        font-weight: 500;
        margin-bottom: 18px;
    }
    .ba-star__title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(28px, 3.4vw, 44px);
        font-weight: 500;
        line-height: 1.1;
        color: #2E2A26;
        margin: 0 0 24px;
    }
    .ba-star__desc {
        font-size: 16px;
        line-height: 1.75;
        color: #6B6157;
        margin: 0 0 32px;
        max-width: 480px;
    }
    .ba-star__price-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 32px;
    }
    .ba-star__price {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 500;
        color: #2E2A26;
    }
    .ba-star__note {
        font-size: 12px;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #B8A999;
    }

    /* Benefits */
    /* ───────── Benefits — rediseño editorial premium ───────── */
    .ba-benefits-wrap {
        position: relative;
        background: linear-gradient(180deg, #FBF8F2 0%, #F4EFE5 100%);
        overflow: hidden;
    }
    .ba-benefits-wrap::before,
    .ba-benefits-wrap::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
        filter: blur(80px);
    }
    .ba-benefits-wrap::before {
        top: -120px; left: -80px;
        width: 360px; height: 360px;
        background: radial-gradient(circle, rgba(217,181,109,.18), transparent 70%);
    }
    .ba-benefits-wrap::after {
        bottom: -140px; right: -100px;
        width: 420px; height: 420px;
        background: radial-gradient(circle, rgba(168,178,154,.16), transparent 70%);
    }
    .ba-benefits-wrap .ba-container { position: relative; z-index: 1; }

    .ba-benefits {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-top: 56px;
    }
    .ba-benefit {
        position: relative;
        background: #FFFFFF;
        border: 1px solid rgba(184,169,153,.18);
        border-radius: 14px;
        padding: 38px 28px 32px;
        overflow: hidden;
        transition: transform .35s cubic-bezier(.2,.7,.3,1),
                    box-shadow .35s ease,
                    border-color .35s ease;
    }
    .ba-benefit::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #D9B56D 0%, #BE9A53 50%, #A8B29A 100%);
        transform: scaleX(0);
        transform-origin: left center;
        transition: transform .55s cubic-bezier(.2,.7,.3,1);
    }
    .ba-benefit:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 56px -18px rgba(190,154,83,.28),
                    0 4px 12px rgba(46,42,38,.04);
        border-color: rgba(217,181,109,.45);
    }
    .ba-benefit:hover::before { transform: scaleX(1); }

    .ba-benefit__icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FBF4E6 0%, #F7F3ED 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #BE9A53;
        margin-bottom: 24px;
        transition: transform .4s cubic-bezier(.2,.7,.3,1),
                    background .4s ease,
                    color .4s ease;
    }
    .ba-benefit__icon svg { width: 22px; height: 22px; }
    .ba-benefit:hover .ba-benefit__icon {
        background: linear-gradient(135deg, #D9B56D 0%, #BE9A53 100%);
        color: #FFFFFF;
        transform: scale(1.08) rotate(-4deg);
    }

    .ba-benefit__num {
        font-family: 'Playfair Display', serif;
        font-size: 12px;
        letter-spacing: .16em;
        font-style: italic;
        color: #BE9A53;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .ba-benefit__title {
        font-family: 'Playfair Display', serif;
        font-size: 19px;
        font-weight: 500;
        color: #2E2A26;
        line-height: 1.3;
        margin: 0 0 14px;
    }
    .ba-benefit__desc {
        font-size: 13.5px;
        line-height: 1.7;
        color: #6B6157;
        margin: 0;
    }

    /* Editorial quote */
    /* ───────── Editorial Manifesto — rediseño ornamental ───────── */
    .ba-manifesto {
        position: relative;
        background: linear-gradient(180deg, #FBF8F2 0%, #F4EFE5 60%, #FBF8F2 100%);
        padding: clamp(72px, 9vw, 128px) 24px;
        overflow: hidden;
    }
    .ba-manifesto::before,
    .ba-manifesto::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        pointer-events: none;
        z-index: 0;
    }
    .ba-manifesto::before {
        top: -180px; left: 8%;
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(217,181,109,.20), transparent 70%);
    }
    .ba-manifesto::after {
        bottom: -180px; right: 6%;
        width: 420px; height: 420px;
        background: radial-gradient(circle, rgba(168,178,154,.16), transparent 70%);
    }
    /* Hojas decorativas botánicas en las esquinas */
    .ba-manifesto__leaf {
        position: absolute;
        z-index: 0;
        opacity: .35;
        pointer-events: none;
    }
    .ba-manifesto__leaf--tl { top: 40px; left: 40px; width: 96px; transform: rotate(8deg); }
    .ba-manifesto__leaf--br { bottom: 40px; right: 40px; width: 110px; transform: rotate(190deg) scaleX(-1); }

    .ba-quote {
        text-align: center;
        max-width: 880px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    .ba-quote__mark {
        font-family: 'Playfair Display', serif;
        font-size: clamp(90px, 12vw, 160px);
        font-style: italic;
        line-height: 1;
        color: #D9B56D;
        opacity: .25;
        margin: 0 0 -.4em;
        user-select: none;
    }
    .ba-quote__text {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: clamp(24px, 3.2vw, 40px);
        font-weight: 400;
        line-height: 1.4;
        color: #2E2A26;
        margin: 0 0 36px;
        letter-spacing: -.005em;
    }
    .ba-quote__text em {
        font-style: italic;
        background: linear-gradient(180deg, transparent 65%, rgba(217,181,109,.22) 65%, rgba(217,181,109,.22) 95%, transparent 95%);
        padding: 0 2px;
    }
    /* Ornamento central: línea — diamante — línea */
    .ba-quote__ornament {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin: 0 0 22px;
    }
    .ba-quote__ornament-line {
        width: 56px;
        height: 1px;
        background: linear-gradient(90deg, transparent, #D9B56D 50%, transparent);
    }
    .ba-quote__ornament-dot {
        width: 8px; height: 8px;
        background: #D9B56D;
        transform: rotate(45deg);
        box-shadow: 0 0 0 4px rgba(217,181,109,.18);
    }
    .ba-quote__author {
        font-size: 11px;
        letter-spacing: 0.32em;
        text-transform: uppercase;
        color: #BE9A53;
        font-weight: 600;
    }

    /* Sets split */
    .ba-sets {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 64px;
        align-items: center;
    }
    .ba-sets__list {
        list-style: none;
        padding: 0;
        margin: 28px 0 36px;
    }
    .ba-sets__list li {
        display: flex;
        align-items: baseline;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(184,169,153,.22);
        font-size: 14px;
        color: #2E2A26;
    }
    .ba-sets__list li::before {
        content: "—";
        color: #D9B56D;
        font-weight: 600;
    }
    .ba-sets__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* ───────── Comparison — rediseño editorial con jerarquía ───────── */
    .ba-compare-wrap {
        position: relative;
        overflow: hidden;
    }
    .ba-compare {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 28px;
        max-width: 1080px;
        margin: 56px auto 0;
        align-items: stretch;
        position: relative;
        z-index: 1;
    }
    .ba-compare__col {
        position: relative;
        padding: 44px 38px 40px;
        border-radius: 18px;
        transition: transform .4s cubic-bezier(.2,.7,.3,1), box-shadow .4s ease;
    }
    /* Columna "sin": sobria, casi neutra — debe sentirse "muerta" */
    .ba-compare__col--without {
        background: linear-gradient(180deg, #FFFFFF 0%, #FBFAF7 100%);
        border: 1px solid rgba(184,169,153,.22);
        opacity: .92;
    }
    /* Columna "con": protagonista — sombra áurea, borde dorado sutil, lift */
    .ba-compare__col--with {
        background: linear-gradient(160deg, #FFFFFF 0%, #FBF4E6 55%, #F4E4C5 100%);
        border: 1px solid rgba(217,181,109,.45);
        box-shadow: 0 30px 70px -24px rgba(190,154,83,.32),
                    0 8px 20px -8px rgba(46,42,38,.06);
        transform: translateY(-6px);
    }
    .ba-compare__col--with:hover {
        transform: translateY(-10px);
        box-shadow: 0 38px 86px -22px rgba(190,154,83,.40),
                    0 8px 20px -8px rgba(46,42,38,.08);
    }
    .ba-compare__col--with::before {
        content: 'Recomendado';
        position: absolute;
        top: -12px; right: 24px;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #FFFFFF;
        background: linear-gradient(135deg, #D9B56D 0%, #BE9A53 100%);
        padding: 5px 14px;
        border-radius: 999px;
        box-shadow: 0 4px 12px rgba(190,154,83,.32);
    }

    /* Divisor central VS — solo en desktop */
    .ba-compare__vs {
        align-self: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 1px solid rgba(184,169,153,.32);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: 18px;
        font-weight: 600;
        color: #BE9A53;
        box-shadow: 0 8px 20px rgba(46,42,38,.06);
        position: relative;
    }
    .ba-compare__vs::before,
    .ba-compare__vs::after {
        content: '';
        position: absolute;
        width: 28px; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(217,181,109,.5));
    }
    .ba-compare__vs::before { right: 100%; }
    .ba-compare__vs::after  { left: 100%; background: linear-gradient(90deg, rgba(217,181,109,.5), transparent); }

    .ba-compare__label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 28px;
        padding: 6px 12px 6px 8px;
        border-radius: 999px;
    }
    .ba-compare__label-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .ba-compare__col--without .ba-compare__label {
        color: #8E7F6F;
        background: rgba(184,169,153,.14);
    }
    .ba-compare__col--without .ba-compare__label-dot { background: #B8A999; }
    .ba-compare__col--with .ba-compare__label {
        color: #2E2A26;
        background: rgba(217,181,109,.18);
    }
    .ba-compare__col--with .ba-compare__label-dot { background: #BE9A53; box-shadow: 0 0 0 3px rgba(217,181,109,.25); }

    .ba-compare__list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .ba-compare__list li {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        font-size: 14.5px;
        line-height: 1.55;
        border-top: 1px solid rgba(184,169,153,.16);
    }
    .ba-compare__list li:first-child { border-top: 0; padding-top: 4px; }

    .ba-compare__list li::before {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        line-height: 1;
        margin-top: 2px;
    }
    .ba-compare__col--without .ba-compare__list li {
        color: #8E7F6F;
        text-decoration: line-through;
        text-decoration-color: rgba(184,169,153,.4);
        text-decoration-thickness: 1px;
    }
    .ba-compare__col--without .ba-compare__list li::before {
        content: '×';
        background: rgba(184,169,153,.14);
        color: #8E7F6F;
    }
    .ba-compare__col--with .ba-compare__list li {
        color: #2E2A26;
        font-weight: 500;
    }
    .ba-compare__col--with .ba-compare__list li::before {
        content: '✓';
        background: linear-gradient(135deg, #D9B56D 0%, #BE9A53 100%);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(190,154,83,.35);
    }

    /* Testimonials */
    .ba-tests {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }
    .ba-test {
        padding: 36px 32px;
        background: #FFFFFF;
        border: 1px solid rgba(184,169,153,.2);
        border-radius: 2px;
        position: relative;
        transition: transform .5s cubic-bezier(.2,.7,.3,1), box-shadow .5s ease;
    }
    .ba-test:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 60px rgba(190,154,83,.1);
    }
    .ba-test__mark {
        font-family: 'Playfair Display', serif;
        font-size: 56px;
        line-height: 1;
        color: #D9B56D;
        margin-bottom: 12px;
    }
    .ba-test__body {
        font-size: 15px;
        line-height: 1.7;
        color: #2E2A26;
        font-style: italic;
        margin: 0 0 28px;
    }
    .ba-test__author {
        font-family: 'Playfair Display', serif;
        font-size: 15px;
        color: #2E2A26;
    }
    .ba-test__role {
        font-size: 11px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #B8A999;
        margin-top: 4px;
    }

    /* FAQ */
    .ba-faq {
        max-width: 820px;
        margin: 0 auto;
    }
    .ba-faq details {
        border-bottom: 1px solid rgba(184,169,153,.3);
    }
    .ba-faq details:first-of-type { border-top: 1px solid rgba(184,169,153,.3); }
    .ba-faq summary {
        list-style: none;
        cursor: pointer;
        padding: 28px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 500;
        color: #2E2A26;
        transition: color .25s ease;
    }
    .ba-faq summary::-webkit-details-marker { display: none; }
    .ba-faq summary:hover { color: #BE9A53; }
    .ba-faq summary::after {
        content: "+";
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        color: #D9B56D;
        font-weight: 500;
        transition: transform .35s ease;
    }
    .ba-faq details[open] summary::after {
        content: "−";
        transform: rotate(0);
    }
    .ba-faq__answer {
        padding: 0 0 28px;
        font-size: 15px;
        line-height: 1.75;
        color: #6B6157;
    }

    /* Final CTA */
    .ba-cta {
        text-align: center;
        max-width: 720px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .ba-cta__title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(32px, 4.5vw, 56px);
        font-weight: 500;
        line-height: 1.1;
        color: #FBF8F2;
        margin: 0 0 24px;
    }
    .ba-cta__title em {
        font-style: italic;
        color: #D9B56D;
    }
    .ba-cta__sub {
        font-size: 16px;
        line-height: 1.7;
        color: rgba(247,243,237,.7);
        margin: 0 auto 44px;
        max-width: 520px;
    }
    .ba-cta__actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 56px;
    }
    .ba-btn-gold {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #D9B56D;
        color: #2E2A26;
        padding: 18px 36px;
        border-radius: 2px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-decoration: none;
        text-transform: uppercase;
        transition: background .35s ease, transform .35s ease;
    }
    .ba-btn-gold:hover { background: #E8CC92; transform: translateY(-2px); }
    .ba-btn-outline-light {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #FBF8F2;
        padding: 18px 36px;
        border: 1px solid rgba(247,243,237,.3);
        border-radius: 2px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.06em;
        text-decoration: none;
        text-transform: uppercase;
        transition: all .35s ease;
    }
    .ba-btn-outline-light:hover { background: rgba(247,243,237,.08); border-color: #D9B56D; }
    .ba-cta__trust {
        display: flex;
        gap: 36px;
        justify-content: center;
        flex-wrap: wrap;
        font-size: 12px;
        letter-spacing: 0.1em;
        color: rgba(247,243,237,.5);
    }
    .ba-cta__trust span { display: inline-flex; align-items: center; gap: 8px; }
    .ba-cta__trust span::before {
        content: "✓";
        color: #D9B56D;
    }

    /* =====================================================
       Scroll Animations — fade-up/left/right/in + stagger
       ===================================================== */
    [data-anim] {
        opacity: 0;
        transition: opacity 1s cubic-bezier(.2,.7,.3,1),
                    transform 1s cubic-bezier(.2,.7,.3,1);
        will-change: opacity, transform;
        transition-delay: calc(var(--stagger, 0) * 90ms);
    }
    [data-anim="fade-up"]    { transform: translateY(40px); }
    [data-anim="fade-down"]  { transform: translateY(-40px); }
    [data-anim="fade-left"]  { transform: translateX(40px); }
    [data-anim="fade-right"] { transform: translateX(-40px); }
    [data-anim="scale-in"]   { transform: scale(.94); }
    [data-anim="fade-in"]    { transform: none; }
    [data-anim].is-inview {
        opacity: 1;
        transform: none;
    }

    /* Hero word reveal */
    .ba-hero__title .word {
        display: inline-block;
        overflow: hidden;
        vertical-align: bottom;
        padding-bottom: 0.08em;
        margin-right: 0.18em;
    }
    .ba-hero__title .word > span {
        display: inline-block;
        transform: translateY(100%);
        opacity: 0;
        transition: transform 1s cubic-bezier(.2,.7,.3,1), opacity 1s ease;
        transition-delay: calc(var(--w-i, 0) * 80ms);
    }
    .ba-hero__title.is-loaded .word > span {
        transform: translateY(0);
        opacity: 1;
    }

    /* ===========================
       Responsive
       =========================== */
    @media (max-width: 1024px) {
        .ba-hero { grid-template-columns: 1fr; min-height: auto; }
        .ba-hero__right { aspect-ratio: 4/3; min-height: 320px; }
        .ba-prods { grid-template-columns: repeat(3, 1fr); }
        .ba-benefits { grid-template-columns: repeat(2, 1fr); }
        .ba-cats { grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .ba-star, .ba-sets { grid-template-columns: 1fr; gap: 40px; }
    }
    @media (max-width: 720px) {
        .ba-hero__left { padding: 56px 24px 40px; }
        .ba-prods { grid-template-columns: repeat(2, 1fr); gap: 24px 12px; }
        .ba-cats { grid-template-columns: 1fr; }
        .ba-benefits { grid-template-columns: 1fr; gap: 16px; }
        .ba-benefit { padding: 30px 24px 26px; }
        .ba-tests { grid-template-columns: 1fr; }
        .ba-compare { grid-template-columns: 1fr; gap: 20px; }
        .ba-compare__vs { display: none; }
        .ba-compare__col--with { transform: none; }
        .ba-sets__grid { grid-template-columns: 1fr; }
    }

    @media (prefers-reduced-motion: reduce) {
        [data-anim], .ba-hero__title .word > span,
        .ba-marquee__track {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
            transition: none !important;
        }
    }
</style>
@endpush

@section('content')

@php
    // Trust items (string o array según seed)
    $trustItems = collect($hero->trust_items ?? [])
        ->map(fn ($i) => is_array($i) ? ($i['text'] ?? '') : $i)
        ->filter()
        ->values();

    // Hero title — split en palabras para animación
    $heroLines = array_filter([
        $hero->title_line1 ?? '',
        $hero->title_line2 ?? '',
        $hero->title_line3 ?? '',
    ]);
    $highlight = $hero->title_highlight_word ?? '';

    // Producto destacado del star section
    $starProduct = $heroProduct;
@endphp

{{-- ============================================================
     1. HERO — Asymmetric editorial
     ============================================================ --}}
@php
    // Media del hero (imagen o video)
    $videoExts = ['mp4', 'webm', 'mov'];
    $heroMedia = $hero->media_path ?? null;
    $heroExt = $heroMedia ? strtolower(pathinfo($heroMedia, PATHINFO_EXTENSION)) : null;
    $isHeroVideo = ($hero->media_type ?? null) === 'video'
        || ($heroMedia && in_array($heroExt, $videoExts, true));
    $heroVideoUrl = $isHeroVideo && $heroMedia ? asset('storage/'.$heroMedia) : null;
    $heroImageUrl = (! $isHeroVideo && $heroMedia) ? asset('storage/'.$heroMedia) : null;
    $heroImg = $heroImageUrl
        ?? (($starProduct && !empty($starProduct->images)) ? asset('storage/'.$starProduct->images[0]) : asset('img/brand/logo-transparent.png'));
@endphp

@if(! $isHeroVideo)
@push('head')
<link rel="preload" as="image" href="{{ $heroImg }}" fetchpriority="high">
@endpush
@endif

<style>
/* ═══════════════════════════════════════════════════════════
   HERO ELEGANTE BOTÁNICO — Belleza Áurea (ref. Imagen 1)
   ═══════════════════════════════════════════════════════════ */
.ae{position:relative;padding:clamp(56px,7vh,104px) 0 clamp(44px,6vh,84px);}
.ae__wrap{max-width:1300px;margin:0 auto;padding:0 clamp(24px,5vw,72px);
    display:grid;grid-template-columns:1fr 1.12fr;gap:clamp(32px,5vw,74px);align-items:center;}
.ae__eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:600;
    letter-spacing:.24em;text-transform:uppercase;color:#BE9A53;margin-bottom:24px;}
.ae__eyebrow::before{content:"";width:30px;height:1px;background:#D9B56D;}
.ae__title{font-family:'Playfair Display',Georgia,serif;color:#2E2A26;margin:0 0 22px;
    font-size:clamp(40px,5.6vw,78px);line-height:1.0;letter-spacing:-.01em;font-weight:600;text-wrap:balance;}
.ae__title .l1{display:block;text-transform:uppercase;letter-spacing:.005em;}
.ae__title .l2{display:block;font-style:italic;color:#3A352E;}
.ae__sub{max-width:412px;margin:0 0 34px;color:#6B6157;font-size:clamp(13px,1vw,14.5px);line-height:1.68;}
.ae__cta{display:inline-flex;align-items:center;gap:11px;text-decoration:none;border-radius:999px;
    padding:17px 34px;color:#fff;font:600 12.5px/1 'Montserrat',system-ui,sans-serif;
    letter-spacing:.14em;text-transform:uppercase;
    background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);
    box-shadow:0 16px 32px -12px rgba(190,154,83,.75);
    transition:transform .35s cubic-bezier(.2,.7,.3,1),box-shadow .35s ease;}
.ae__cta:hover{transform:translateY(-3px);box-shadow:0 22px 44px -12px rgba(190,154,83,.9);}
.ae__cta svg{transition:transform .35s;}
.ae__cta:hover svg{transform:translateX(5px);}

.ae__media{position:relative;}
.ae__panel{position:relative;border-radius:18px;overflow:hidden;aspect-ratio:16/12;
    background:
      radial-gradient(120% 90% at 70% 20%, #FFFFFF 0%, #F4F0E9 55%, #EAE3D6 100%);
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 44px 84px -34px rgba(120,92,44,.5), 0 10px 26px -16px rgba(0,0,0,.2);}
.ae__panel::before{content:"";position:absolute;inset:0;pointer-events:none;
    background:repeating-linear-gradient(115deg, rgba(255,255,255,0) 0 22px, rgba(200,190,170,.06) 22px 23px);}
.ae__prod{position:relative;z-index:2;max-width:62%;max-height:94%;object-fit:contain;
    filter:drop-shadow(0 30px 46px rgba(120,92,44,.32));}
.ae__panel video.ae__prod{max-width:64%;max-height:96%;border-radius:16px;
    border:4px solid #FBF8F2;box-shadow:0 26px 52px -20px rgba(120,92,44,.5);}
.ae__panel img.ae__prod{max-height:96%;}
.ae__sprig{position:absolute;z-index:1;pointer-events:none;height:66%;top:17%;}
.ae__sprig--gold{left:3%;transform:rotate(-6deg);}
.ae__sprig--olive{right:2%;transform:rotate(6deg) scaleX(-1);}

@media(max-width:900px){
    .ae__wrap{grid-template-columns:1fr;gap:40px;}
    .ae__media{order:-1;}
    .ae__panel{aspect-ratio:16/10;}
}

/* ═══════════════════════════════════════════════════════════
   CATEGORÍAS — Fotos redondas con corona de hojas (ref. Imagen circular)
   ═══════════════════════════════════════════════════════════ */
.aeg{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(20px,2.6vw,44px);}
.aeg__card{display:flex;flex-direction:column;align-items:center;gap:16px;text-decoration:none;color:inherit;}
.aeg__ring{position:relative;width:clamp(120px,13vw,172px);aspect-ratio:1;display:grid;place-items:center;
    transition:transform .5s cubic-bezier(.2,.7,.3,1);}
.aeg__circle{position:relative;z-index:1;width:76%;height:76%;border-radius:50%;overflow:hidden;
    background:linear-gradient(155deg,#BAC3AC,#9FAA92);border:3px solid #FBF8F2;
    box-shadow:0 18px 36px -16px rgba(90,80,50,.55);display:grid;place-items:center;}
.aeg__circle img{width:100%;height:100%;object-fit:cover;
    filter:saturate(.68) brightness(1.03) contrast(.95);
    transition:transform .7s cubic-bezier(.2,.7,.3,1), filter .6s ease;}
/* Velo cálido de marca en reposo → se quita al hover (color real) */
.aeg__circle::after{content:"";position:absolute;inset:0;border-radius:50%;z-index:1;pointer-events:none;
    background:linear-gradient(160deg, rgba(168,178,154,.34) 0%, rgba(217,181,109,.22) 100%);
    mix-blend-mode:soft-light;opacity:1;transition:opacity .6s ease;}
.aeg__card:hover .aeg__circle::after{opacity:0;}
.aeg__ph{font-family:'Playfair Display',Georgia,serif;font-style:italic;color:#F4F0E9;font-size:26px;}
.aeg__wreath{position:absolute;inset:0;z-index:2;width:100%;height:100%;pointer-events:none;
    transition:transform .6s cubic-bezier(.2,.7,.3,1);}
.aeg__card:hover .aeg__ring{transform:translateY(-6px);}
.aeg__card:hover .aeg__circle img{transform:scale(1.08);filter:saturate(1) brightness(1) contrast(1);}
.aeg__card:hover .aeg__wreath{transform:rotate(3deg) scale(1.02);}
.aeg__name{font-family:'Playfair Display',Georgia,serif;font-size:clamp(16px,1.5vw,20px);
    color:#2E2A26;text-align:center;line-height:1.15;}
.aeg__count{font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;color:#8E7E70;margin-top:-8px;}
@media(max-width:900px){.aeg{grid-template-columns:repeat(2,1fr);}}
</style>

<section class="ae" aria-label="Bienvenida a Belleza Áurea">
    <div class="ae__wrap">
        <div class="ae__copy" data-anim="fade-up">
            <span class="ae__eyebrow">{{ $hero->eyebrow_text ?? 'Distribuidora oficial · Colombia' }}</span>
            <h1 class="ae__title">
                <span class="l1">{{ $hero->title_line1 ?? 'Tu belleza,' }}</span>
                <span class="l2">{{ $hero->title_line2 ?? 'tu esencia.' }}</span>
            </h1>
            <p class="ae__sub">{{ $hero->subtitle ?? 'Distribución exclusiva de productos de belleza: piel, uñas, accesorios y estética profesional. Calidad garantizada.' }}</p>
            <a href="{{ $hero->btn_primary_url ?? route('products.index') }}" class="ae__cta">
                {{ $hero->btn_primary_text ?? 'Ver catálogo completo' }}
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>

        <div class="ae__media" data-anim="fade-up" style="--stagger:4;">
            <div class="ae__panel">
                {{-- Rama dorada --}}
                <svg class="ae__sprig ae__sprig--gold" viewBox="0 0 100 230" fill="none" aria-hidden="true">
                    <path d="M54 226 C 49 168 44 112 58 16" stroke="#C6A052" stroke-width="2.4" stroke-linecap="round"/>
                    <g fill="#D9B56D">
                        <ellipse cx="39" cy="158" rx="16" ry="6" transform="rotate(-34 39 158)"/>
                        <ellipse cx="67" cy="128" rx="15" ry="5.6" transform="rotate(30 67 128)"/>
                        <ellipse cx="40" cy="102" rx="14" ry="5.4" transform="rotate(-36 40 102)"/>
                        <ellipse cx="63" cy="72" rx="13" ry="5" transform="rotate(32 63 72)"/>
                        <ellipse cx="46" cy="46" rx="11" ry="4.6" transform="rotate(-38 46 46)"/>
                        <ellipse cx="58" cy="26" rx="9" ry="4" transform="rotate(26 58 26)"/>
                    </g>
                </svg>
                {{-- Rama de olivo --}}
                <svg class="ae__sprig ae__sprig--olive" viewBox="0 0 100 230" fill="none" aria-hidden="true">
                    <path d="M54 226 C 49 168 44 112 58 16" stroke="#7C8A6B" stroke-width="2.4" stroke-linecap="round"/>
                    <g fill="#8A9772">
                        <ellipse cx="39" cy="158" rx="17" ry="6.4" transform="rotate(-34 39 158)"/>
                        <ellipse cx="67" cy="128" rx="16" ry="6" transform="rotate(30 67 128)"/>
                        <ellipse cx="40" cy="102" rx="15" ry="5.8" transform="rotate(-36 40 102)"/>
                        <ellipse cx="63" cy="72" rx="14" ry="5.4" transform="rotate(32 63 72)"/>
                        <ellipse cx="46" cy="46" rx="12" ry="5" transform="rotate(-38 46 46)"/>
                        <ellipse cx="58" cy="26" rx="10" ry="4.4" transform="rotate(26 58 26)"/>
                    </g>
                </svg>

                @if($heroVideoUrl)
                    <video class="ae__prod" autoplay muted loop playsinline preload="auto" aria-hidden="true">
                        <source src="{{ $heroVideoUrl }}" type="video/{{ $heroExt === 'mov' ? 'quicktime' : $heroExt }}">
                    </video>
                @else
                    <img class="ae__prod" src="{{ $heroImg }}" alt="Belleza Áurea — cosmética e insumos de belleza" fetchpriority="high" decoding="async" width="640" height="480">
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     2. MARQUEE — Trust strip
     ============================================================ --}}
@if($trustItems->isNotEmpty())
<aside class="ba-marquee" aria-label="Beneficios principales">
    <div class="ba-marquee__track">
        @for($mIter = 0; $mIter < 2; $mIter++)
            @foreach($trustItems as $item)
                <span class="ba-marquee__item">
                    <span class="ba-marquee__dot"></span>{{ $item }}
                </span>
            @endforeach
            <span class="ba-marquee__item"><span class="ba-marquee__dot"></span>Belleza natural, elegante y atemporal</span>
        @endfor
    </div>
</aside>
@endif

{{-- ============================================================
     3. CATEGORÍAS — Magazine grid asimétrico
     Las 8 categorías top por sort_order (editable en /admin/categories).
     Primera card ocupa 2x2 (hero); las otras 6 ocupan 1x1.
     ============================================================ --}}
@if(($categories ?? collect())->isNotEmpty())
<section class="ba-section ba-section--cream-soft" aria-labelledby="cats-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">{{ $homePage->categories_label ?? 'Categorías' }}</span>
            <h2 id="cats-title" class="ba-section-head__title">{{ $homePage->categories_title ?? 'Encuentra lo que buscas' }}</h2>
            @if($homePage->categories_subtitle)
            <p class="ba-section-head__sub">{{ $homePage->categories_subtitle }}</p>
            @endif
            <div class="ba-divider"></div>
        </header>

        <div class="aeg">
            @foreach($categories->take(8) as $i => $cat)
                @php
                    $count = $cat->products_count ?? 0;
                    $catLink = route('products.index', ['category' => $cat->slug]);
                    $catImg = $cat->image ? asset('storage/'.$cat->image) : null;
                    // Fallback: si la categoría no tiene foto, usar la del primer producto (prioriza destacados)
                    if (! $catImg) {
                        $fp = $cat->products()->where('is_active', 1)
                                  ->whereNotNull('images')->where('images', '!=', '[]')
                                  ->orderByDesc('is_featured')->first();
                        if ($fp && ! empty($fp->images)) {
                            $catImg = asset('storage/'.$fp->images[0]);
                        }
                    }
                @endphp
                <a href="{{ $catLink }}"
                   class="aeg__card"
                   data-anim="fade-up"
                   style="--stagger: {{ $i }};"
                   aria-label="Explorar {{ $cat->name }} — {{ $count }} producto{{ $count === 1 ? '' : 's' }}">
                    <span class="aeg__ring">
                        <span class="aeg__circle">
                            @if($catImg)
                                <img src="{{ $catImg }}" alt="{{ $cat->name }} — insumos y cosmética de belleza | Belleza Áurea" loading="lazy" decoding="async">
                            @else
                                <span class="aeg__ph">{{ mb_substr($cat->name, 0, 1) }}</span>
                            @endif
                        </span>
                        <img class="aeg__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
                    </span>
                    <span class="aeg__name">{{ $cat->name }}</span>
                    @if($count > 0)
                    <span class="aeg__count">{{ $count }} producto{{ $count === 1 ? '' : 's' }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:52px;" data-anim="fade-up">
            <a href="{{ route('products.index') }}" class="ba-btn-ghost">Ver todas las categorías</a>
        </div>
    </div>
</section>
@endif

@include('partials.ba-divider')

{{-- ============================================================
     4. CATÁLOGO — Productos destacados con tabs filtro
     ============================================================ --}}
@php
    // Mezclamos lentes (productos principales) + toallitas (sets) para los tabs.
    $allProducts = $lentes->concat($toallitas)->take(12);

    // Cuántos están realmente en cada tab (para mostrar/ocultar tabs vacíos)
    $countAll       = $allProducts->count();
    $countFeatured  = $allProducts->where('is_featured', true)->count();
    $countDiscount  = $allProducts->filter(fn($p) => $p->compare_price && $p->compare_price > $p->price)->count();
    $countSets      = $allProducts->filter(fn($p) => collect($p->type ?? [])->contains('toallitas'))->count();
@endphp

@if($allProducts->isNotEmpty())
<style>
/* Refinamiento elegante botánico — tarjetas de producto + tabs */
.ba-card__img{border-radius:16px;border:1px solid rgba(217,181,109,.2);
    background:linear-gradient(155deg,#FBF8F2 0%,#F3ECDF 100%);}
.ba-card:hover .ba-card__img{border-color:rgba(217,181,109,.5);
    box-shadow:0 30px 56px -24px rgba(190,154,83,.45);}
.ba-card__price{color:#BE9A53;}
/* Ramito decorativo en la esquina de la tarjeta */
.ba-card::after{content:"";position:absolute;right:-6px;bottom:-4px;width:38px;height:50px;z-index:0;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    opacity:.5;transform:rotate(6deg);pointer-events:none;
    transition:transform .5s cubic-bezier(.2,.7,.3,1),opacity .5s ease;}
.ba-card:hover::after{opacity:.8;transform:rotate(0deg) translateY(-3px);}
/* Calma el color chillón de fotos de proveedor — solo escritorio (hover); móvil a todo color */
@media (hover:hover){
    .ba-card__img img{filter:saturate(.8) brightness(1.02) contrast(.96);}
    .ba-card:hover .ba-card__img img{filter:brightness(1.03);}
}
.ba-tabs{background:rgba(255,255,255,.7);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);
    border-color:rgba(217,181,109,.3);}
.ba-tab.is-active{background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);color:#3B310F;
    box-shadow:0 8px 20px -8px rgba(190,154,83,.7);}
/* Botón ghost coherente con el resto */
.ba-btn-ghost{border-radius:999px;border:1.5px solid rgba(42,38,32,.22);color:#2E2A26;
    padding:15px 30px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;transition:all .35s ease;}
.ba-btn-ghost:hover{border-color:#BE9A53;color:#A9853C;transform:translateY(-2px);}
</style>
<section class="ba-section" aria-labelledby="catalog-title"
         x-data="{ tab: 'all' }">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">{{ $homePage->catalog_label ?? 'Catálogo' }}</span>
            <h2 id="catalog-title" class="ba-section-head__title">{{ $homePage->catalog_title ?? 'Nuestros productos' }}</h2>
            @if($homePage->catalog_subtitle)
            <p class="ba-section-head__sub">{{ $homePage->catalog_subtitle }}</p>
            @endif
            <div class="ba-divider"></div>
        </header>

        {{-- Tabs filtro --}}
        <div class="ba-tabs" data-anim="fade-up">
            <button type="button"
                    class="ba-tab" :class="tab === 'all' ? 'is-active' : ''"
                    @click="tab = 'all'; refreshCards()">Todos · {{ $countAll }}</button>
            @if($countFeatured > 0)
            <button type="button"
                    class="ba-tab" :class="tab === 'featured' ? 'is-active' : ''"
                    @click="tab = 'featured'; refreshCards()">★ Destacados · {{ $countFeatured }}</button>
            @endif
            @if($countDiscount > 0)
            <button type="button"
                    class="ba-tab" :class="tab === 'discount' ? 'is-active' : ''"
                    @click="tab = 'discount'; refreshCards()">Con descuento · {{ $countDiscount }}</button>
            @endif
            @if($countSets > 0)
            <button type="button"
                    class="ba-tab" :class="tab === 'sets' ? 'is-active' : ''"
                    @click="tab = 'sets'; refreshCards()">Sets · {{ $countSets }}</button>
            @endif
        </div>

        <div class="ba-prods" id="ba-prods-grid">
            @foreach($allProducts as $i => $p)
                @php
                    // Tags para el filtro
                    $tags = ['all'];
                    if ($p->is_featured) $tags[] = 'featured';
                    if ($p->compare_price && $p->compare_price > $p->price) $tags[] = 'discount';
                    if (collect($p->type ?? [])->contains('toallitas')) $tags[] = 'sets';

                    $discountPct = ($p->compare_price && $p->compare_price > $p->price)
                        ? (int) round((($p->compare_price - $p->price) / $p->compare_price) * 100)
                        : 0;

                    // Color variants (max 4 dots)
                    $colorVariants = collect($p->variants ?? [])
                        ->filter(fn ($v) => $v->is_active && $v->color_hex)
                        ->take(4);
                    $moreColors = collect($p->variants ?? [])
                        ->filter(fn ($v) => $v->is_active && $v->color_hex)
                        ->count() - $colorVariants->count();

                    $isNew = $p->created_at && $p->created_at->gt(now()->subDays(30));
                @endphp
                <a href="{{ route('products.show', ['slug' => $p->slug]) }}"
                   class="ba-card"
                   data-tags="{{ implode(',', $tags) }}"
                   data-anim="fade-up"
                   style="--stagger: {{ $i % 4 }};">

                    {{-- Imagen + overlay + badges --}}
                    <div class="ba-card__img {{ empty($p->images) ? 'ba-card__img--ph' : '' }}">
                        @if($p->is_featured)
                            <span class="ba-card__badge ba-card__badge--featured">★ Destacado</span>
                        @elseif($isNew)
                            <span class="ba-card__badge ba-card__badge--new">Nuevo</span>
                        @elseif($discountPct >= 15)
                            <span class="ba-card__badge ba-card__badge--discount">-{{ $discountPct }}%</span>
                        @endif

                        <button type="button" class="ba-card__wish"
                                onclick="event.preventDefault();event.stopPropagation();this.style.color='#C97B6B';"
                                aria-label="Guardar en favoritos">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>

                        @if(!empty($p->images))
                            <img src="{{ asset('storage/'.$p->images[0]) }}" alt="{{ $p->name }}" loading="lazy">
                        @else
                            <span>Próximamente</span>
                        @endif

                        <div class="ba-card__overlay">
                            <span class="ba-card__quick">
                                Ver detalle
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </div>
                    </div>

                    {{-- Info textual --}}
                    @if($p->brand)
                        <p class="ba-card__brand">{{ $p->brand->name }}</p>
                    @elseif($p->category)
                        <p class="ba-card__cat">{{ $p->category->name }}</p>
                    @endif

                    <h3 class="ba-card__name">{{ $p->name }}</h3>

                    <div class="ba-card__price-row">
                        <span class="ba-card__price">${{ number_format($p->price, 0, ',', '.') }}</span>
                        @if($p->compare_price && $p->compare_price > $p->price)
                            <span class="ba-card__compare">${{ number_format($p->compare_price, 0, ',', '.') }}</span>
                            @if($discountPct >= 5)
                                <span class="ba-card__discount">-{{ $discountPct }}%</span>
                            @endif
                        @endif
                    </div>

                    {{-- Color dots --}}
                    @if($colorVariants->isNotEmpty())
                    <div class="ba-card__dots">
                        @foreach($colorVariants as $cv)
                            <span class="ba-card__dot" style="background:{{ $cv->color_hex }};" title="{{ $cv->value }}"></span>
                        @endforeach
                        @if($moreColors > 0)
                            <span class="ba-card__dot--more">+{{ $moreColors }}</span>
                        @endif
                    </div>
                    @endif
                </a>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:64px;" data-anim="fade-up">
            <a href="{{ route('products.index') }}" class="ba-btn-ghost">Ver catálogo completo</a>
        </div>
    </div>

    {{-- Filtrado Alpine + reanimación --}}
    <script>
        function refreshCards() {
            // No-op placeholder; el binding x-effect abajo hace el trabajo.
            // Mantenido por compatibilidad con @click.
        }
    </script>
    <div x-effect="
        const grid = document.getElementById('ba-prods-grid');
        if (!grid) return;
        const cards = grid.querySelectorAll('.ba-card');
        cards.forEach((c, i) => {
            const tags = (c.dataset.tags || '').split(',');
            const match = tab === 'all' || tags.includes(tab);
            if (match) {
                c.hidden = false;
                c.classList.remove('ba-filtering');
                // Force reflow then add class for re-animation
                void c.offsetWidth;
                c.classList.add('ba-filtering');
                c.style.animationDelay = (i % 4) * 60 + 'ms';
            } else {
                c.hidden = true;
                c.classList.remove('ba-filtering');
            }
        });
    "></div>
</section>
@endif

@include('partials.ba-divider')

{{-- ============================================================
     5. STAR PRODUCT — Split layout editorial
     ============================================================ --}}
@if($starProduct)
<style>
/* Refinamiento elegante botánico — Producto estrella + botón primario global */
.ba-star{position:relative;}
.ba-star__visual{border-radius:20px;border:5px solid #FBF8F2;
    box-shadow:0 44px 84px -34px rgba(120,92,44,.5),0 10px 26px -16px rgba(0,0,0,.18);}
.ba-star__price{color:#BE9A53;}
.ba-star__visual::after{content:"";position:absolute;top:-20px;left:-16px;width:74px;height:96px;z-index:3;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    transform:rotate(-14deg);opacity:.9;pointer-events:none;
    filter:drop-shadow(0 6px 10px rgba(120,92,44,.18));}

/* Botón primario unificado a dorado (antes negro) — coherencia en todo el sitio */
.ba-btn-primary{background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);color:#3B310F;
    box-shadow:0 16px 32px -12px rgba(190,154,83,.72);}
.ba-btn-primary::before{opacity:0 !important;}
.ba-btn-primary:hover{transform:translateY(-3px);color:#3B310F;
    box-shadow:0 22px 44px -12px rgba(190,154,83,.9);}
.ba-section--ink .ba-btn-primary{box-shadow:0 16px 32px -12px rgba(0,0,0,.4);}
</style>
<section class="ba-section ba-section--cream ba-section--float" aria-labelledby="star-title">
    <div class="ba-container">
        <div class="ba-star">
            <div class="ba-star__visual" data-anim="fade-right">
                @if(!empty($starProduct->images))
                <img src="{{ asset('storage/'.$starProduct->images[0]) }}" alt="{{ $starProduct->name }}">
                @else
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ asset('img/brand/logo-transparent.png') }}" alt="Belleza Áurea" style="width:55%;opacity:.85;">
                </div>
                @endif
            </div>
            <div class="ba-star__body" data-anim="fade-left" style="--stagger: 2;">
                <span class="ba-star__label">{{ $homePage->promo_label ?: 'Producto estrella' }}</span>
                <h2 id="star-title" class="ba-star__title">{{ $starProduct->name }}</h2>
                <p class="ba-star__desc">{{ \Illuminate\Support\Str::limit(strip_tags($starProduct->description), 220) }}</p>
                <div class="ba-star__price-block">
                    <span class="ba-star__price">${{ number_format($starProduct->price, 0, ',', '.') }}</span>
                    @if($homePage->promo_price_note)
                    <span class="ba-star__note">{{ $homePage->promo_price_note }}</span>
                    @endif
                </div>
                <a href="{{ route('products.show', ['slug' => $starProduct->slug]) }}" class="ba-btn-primary">
                    <span>{{ $homePage->promo_btn_text ?: 'Ver producto' }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

@include('partials.ba-divider')

{{-- ============================================================
     6. BENEFITS — Belleza con propósito
     ============================================================ --}}
@php
    $benefitsCards = collect($homePage->benefits_cards ?? [])->filter(fn ($b) => !empty($b['title'] ?? ''));
@endphp
@if($benefitsCards->isNotEmpty())
@php
    // Set de iconos editoriales para cada card. Se mapean por keyword del título;
    // si no matchea, cae a un icono "spark" neutro.
    $benefitIcons = [
        'precio'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7" cy="7" r="1.5" fill="currentColor"/></svg>',
        'lugar'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
        'calidad'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 2.39 7.36H22l-6.18 4.49 2.36 7.27L12 16.65l-6.18 4.47 2.36-7.27L2 9.36h7.61L12 2Z"/></svg>',
        'despacho' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>',
    ];
    $iconDefault = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/></svg>';

    $pickIcon = function (string $title) use ($benefitIcons, $iconDefault) {
        $t = mb_strtolower($title);
        foreach ($benefitIcons as $k => $svg) {
            if (str_contains($t, $k)) return $svg;
        }
        return $iconDefault;
    };
@endphp
<style>
/* Refinamiento elegante botánico — tarjetas de beneficios con ramito */
.ba-benefit{border-radius:18px;}
.ba-benefit > *{position:relative;z-index:1;}
.ba-benefit::after{content:"";position:absolute;right:8px;bottom:6px;width:42px;height:54px;z-index:0;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    opacity:.4;transform:rotate(8deg);pointer-events:none;
    transition:transform .5s cubic-bezier(.2,.7,.3,1),opacity .5s ease;}
.ba-benefit:hover::after{opacity:.72;transform:rotate(0deg) translateY(-3px);}

/* ─── Iconos animados (al pasar el mouse por la tarjeta) ─── */
.ba-benefit__icon{position:relative;overflow:visible;}
.ba-benefit__icon svg{transition:transform .4s cubic-bezier(.2,.7,.3,1);}

/* Carrito / despacho: se desplaza como manejando */
.ba-benefit:hover .ba-benefit__icon--cart svg{animation:ba-drive 1s ease-in-out infinite;}
@keyframes ba-drive{0%,100%{transform:translateX(-2px);}50%{transform:translateX(3px) translateY(-1px);}}

/* Estrella / calidad: crece y gira un poco */
.ba-benefit:hover .ba-benefit__icon--star svg{animation:ba-starpulse 1.1s ease-in-out infinite;}
@keyframes ba-starpulse{0%,100%{transform:scale(1) rotate(0);}50%{transform:scale(1.28) rotate(10deg);}}

/* Etiqueta / precio: se balancea */
.ba-benefit:hover .ba-benefit__icon--tag svg{animation:ba-swing 1.1s ease-in-out infinite;transform-origin:72% 22%;}
@keyframes ba-swing{0%,100%{transform:rotate(-9deg);}50%{transform:rotate(9deg);}}

/* Grid / lugar: latido */
.ba-benefit:hover .ba-benefit__icon--grid svg{animation:ba-beat 1s ease-in-out infinite;}
@keyframes ba-beat{0%,100%{transform:scale(1);}50%{transform:scale(1.16);}}

/* Default / spark: gira */
.ba-benefit:hover .ba-benefit__icon--default svg{animation:ba-turn 2.4s linear infinite;}
@keyframes ba-turn{to{transform:rotate(360deg);}}

/* Destellitos de la estrella — titilan siempre */
.ba-spk{position:absolute;width:12px;height:12px;border-radius:50%;pointer-events:none;opacity:0;z-index:3;
    background:radial-gradient(circle,#ffffff 0%,#FBEFC8 42%,rgba(217,181,109,0) 72%);
    filter:drop-shadow(0 0 4px rgba(255,248,222,1)) drop-shadow(0 0 9px rgba(232,204,146,.85));
    animation:ba-spk 1.9s ease-in-out infinite;}
/* Glint en cruz (destello de estrella) */
.ba-spk::before,.ba-spk::after{content:"";position:absolute;left:50%;top:50%;border-radius:2px;}
.ba-spk::before{width:1.6px;height:210%;transform:translate(-50%,-50%);
    background:linear-gradient(to bottom,transparent,rgba(255,255,255,.95),transparent);}
.ba-spk::after{height:1.6px;width:210%;transform:translate(-50%,-50%);
    background:linear-gradient(to right,transparent,rgba(255,255,255,.95),transparent);}
.ba-spk--1{top:-9px;right:-6px;animation-delay:0s;}
.ba-spk--2{top:16px;right:-11px;animation-delay:.5s;}
.ba-spk--3{top:-11px;left:-3px;animation-delay:1s;}
.ba-spk--4{bottom:-8px;left:10px;animation-delay:1.4s;}
.ba-spk--5{top:6px;left:-11px;animation-delay:.8s;}
@keyframes ba-spk{0%,100%{opacity:0;transform:scale(.2);}35%{opacity:1;transform:scale(1);}60%{opacity:.85;transform:scale(1.06);}}
/* Al pasar el mouse titilan más rápido */
.ba-benefit:hover .ba-spk{animation-duration:1.1s;}

@media(prefers-reduced-motion:reduce){
    .ba-benefit__icon--cart svg,.ba-benefit__icon--star svg,.ba-benefit__icon--tag svg,
    .ba-benefit__icon--grid svg,.ba-benefit__icon--default svg,.ba-spk{animation:none!important;}
    .ba-spk{opacity:.85!important;}
}
</style>
<section class="ba-section ba-benefits-wrap" aria-labelledby="benefits-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">{{ $homePage->benefits_label ?? 'Por qué elegirnos' }}</span>
            <h2 id="benefits-title" class="ba-section-head__title">{{ $homePage->benefits_title ?? 'Belleza con propósito' }}</h2>
            @if($homePage->benefits_subtitle)
            <p class="ba-section-head__sub">{{ $homePage->benefits_subtitle }}</p>
            @endif
            <div class="ba-divider"></div>
        </header>

        <div class="ba-benefits">
            @foreach($benefitsCards->values() as $i => $b)
                @php
                    $bt = mb_strtolower($b['title']);
                    $iconType = 'default';
                    foreach (['precio' => 'tag', 'lugar' => 'grid', 'calidad' => 'star', 'despacho' => 'cart'] as $k => $type) {
                        if (str_contains($bt, $k)) { $iconType = $type; break; }
                    }
                @endphp
                <div class="ba-benefit" data-anim="fade-up" style="--stagger: {{ $i }};">
                    <div class="ba-benefit__icon ba-benefit__icon--{{ $iconType }}" aria-hidden="true">
                        {!! $pickIcon($b['title']) !!}
                        @if($iconType === 'star')
                            <span class="ba-spk ba-spk--1"></span>
                            <span class="ba-spk ba-spk--2"></span>
                            <span class="ba-spk ba-spk--3"></span>
                            <span class="ba-spk ba-spk--4"></span>
                            <span class="ba-spk ba-spk--5"></span>
                        @endif
                    </div>
                    <div class="ba-benefit__num">— {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3 class="ba-benefit__title">{{ $b['title'] }}</h3>
                    <p class="ba-benefit__desc">{{ $b['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@include('partials.ba-divider')

{{-- ============================================================
     7. EDITORIAL QUOTE — Manifesto
     ============================================================ --}}
<section class="ba-manifesto" aria-label="Filosofía de marca">
    {{-- Hojas botánicas decorativas en esquinas --}}
    <svg class="ba-manifesto__leaf ba-manifesto__leaf--tl" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40" stroke="#A8B29A" stroke-width="1" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="11" ry="4" transform="rotate(-30 48 50)" fill="#A8B29A" opacity=".6"/>
        <ellipse cx="68" cy="32" rx="9" ry="3.5" transform="rotate(-15 68 32)" fill="#A8B29A" opacity=".6"/>
    </svg>
    <svg class="ba-manifesto__leaf ba-manifesto__leaf--br" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40 M 38 70 Q 70 60 70 30" stroke="#D9B56D" stroke-width="1" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="11" ry="4" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".5"/>
        <ellipse cx="68" cy="32" rx="9" ry="3.5" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".5"/>
    </svg>

    {{-- Destellitos de rocío sobre el follaje de las esquinas --}}
    <span class="ba-dew" style="top:64px;left:120px;z-index:1;--d:0s" aria-hidden="true"></span>
    <span class="ba-dew" style="top:110px;left:58px;z-index:1;--d:1.1s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:66px;right:122px;z-index:1;--d:.6s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:112px;right:60px;z-index:1;--d:1.5s" aria-hidden="true"></span>

    <div class="ba-quote" data-anim="fade-in">
        <div class="ba-quote__mark" aria-hidden="true">&ldquo;</div>
        <p class="ba-quote__text">
            Detrás de cada manicure impecable, cada mirada perfecta y cada corte preciso hay un material <em>elegido con criterio</em>. Eso armamos para ti.
        </p>
        <div class="ba-quote__ornament" aria-hidden="true">
            <span class="ba-quote__ornament-line"></span>
            <span class="ba-quote__ornament-dot"></span>
            <span class="ba-quote__ornament-line"></span>
        </div>
        <p class="ba-quote__author">Belleza Áurea</p>
    </div>
</section>

{{-- ============================================================
     8. SETS / RITUALES — Split con sets
     ============================================================ --}}
@if($toallitas->isNotEmpty())
@include('partials.ba-divider')
<section class="ba-section ba-section--float" aria-labelledby="sets-title">
    <div class="ba-container">
        <div class="ba-sets">
            <div data-anim="fade-right">
                <span class="ba-section-head__label">{{ $homePage->wipes_label ?? 'Sets' }}</span>
                <h2 id="sets-title" class="ba-section-head__title" style="text-align:left;margin-top:16px;">{{ $homePage->wipes_title ?? 'Rituales completos' }}</h2>
                @if($homePage->wipes_description)
                <p class="ba-star__desc" style="margin-top:24px;">{{ $homePage->wipes_description }}</p>
                @endif

                @if(!empty($homePage->wipes_features))
                <ul class="ba-sets__list">
                    @foreach($homePage->wipes_features as $feature)
                    <li>{{ $feature }}</li>
                    @endforeach
                </ul>
                @endif

                <a href="{{ route('products.index', ['type' => 'toallitas']) }}" class="ba-btn-ghost">Ver todos los sets</a>
            </div>

            <div class="ba-sets__grid">
                @foreach($toallitas->take(2) as $i => $set)
                    <a href="{{ route('products.show', ['slug' => $set->slug]) }}"
                       class="ba-card"
                       data-anim="fade-left"
                       style="--stagger: {{ $i + 1 }};">
                        <div class="ba-card__img {{ empty($set->images) ? 'ba-card__img--ph' : '' }}">
                            @if(!empty($set->images))
                                <img src="{{ asset('storage/'.$set->images[0]) }}" alt="{{ $set->name }}" loading="lazy">
                            @else
                                <span>Próximamente</span>
                            @endif
                        </div>
                        <p class="ba-card__cat">Set</p>
                        <h3 class="ba-card__name">{{ $set->name }}</h3>
                        <div class="ba-card__price-row">
                            <span class="ba-card__price">${{ number_format($set->price, 0, ',', '.') }}</span>
                            @if($set->compare_price && $set->compare_price > $set->price)
                            <span class="ba-card__compare">${{ number_format($set->compare_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     9. COMPARISON — Con vs Sin ritual
     ============================================================ --}}
@if(!empty($homePage->comparison_without_items) || !empty($homePage->comparison_with_items))
@include('partials.ba-divider')
<style>
/* Ramito botánico en la columna ganadora de la comparativa */
.ba-compare__col--with .ba-compare__label,
.ba-compare__col--with .ba-compare__list{position:relative;z-index:1;}
.ba-compare__col--with::after{content:"";position:absolute;right:12px;bottom:10px;width:46px;height:60px;z-index:0;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    opacity:.42;transform:rotate(8deg);pointer-events:none;
    transition:transform .5s cubic-bezier(.2,.7,.3,1),opacity .5s ease;}
.ba-compare__col--with:hover::after{opacity:.7;transform:rotate(0deg) translateY(-3px);}
</style>
<section class="ba-section ba-section--cream ba-compare-wrap" aria-labelledby="compare-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">{{ $homePage->comparison_label ?? 'El antes y después' }}</span>
            <h2 id="compare-title" class="ba-section-head__title">{{ $homePage->comparison_title ?? 'Con vs. sin tu ritual áureo' }}</h2>
            @if($homePage->comparison_subtitle)
            <p class="ba-section-head__sub">{{ $homePage->comparison_subtitle }}</p>
            @endif
            <div class="ba-divider"></div>
        </header>

        <div class="ba-compare">
            <div class="ba-compare__col ba-compare__col--without" data-anim="fade-right">
                <p class="ba-compare__label">
                    <span class="ba-compare__label-dot"></span>
                    {{ $homePage->comparison_without_label ?? 'Sin ritual' }}
                </p>
                <ul class="ba-compare__list">
                    @foreach(($homePage->comparison_without_items ?? []) as $item)
                        <li>{{ is_array($item) ? ($item['text'] ?? $item[0] ?? '') : $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="ba-compare__vs" aria-hidden="true">vs</div>

            <div class="ba-compare__col ba-compare__col--with" data-anim="fade-left">
                <p class="ba-compare__label">
                    <span class="ba-compare__label-dot"></span>
                    {{ $homePage->comparison_with_label ?? 'Con Belleza Áurea' }}
                </p>
                <ul class="ba-compare__list">
                    @foreach(($homePage->comparison_with_items ?? []) as $item)
                        <li>{{ is_array($item) ? ($item['text'] ?? $item[0] ?? '') : $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     9b. AUTHORITY / SEO — Tu mayorista de belleza en Colombia
     Bloque editorial denso para SEO + GEO (citable por IAs) con stat
     cards de respaldo. Reemplaza el carrusel de marcas mientras solo
     se trabaja con un proveedor.
     ============================================================ --}}
@php
    $authStats = [
        ['num' => (\App\Models\Product::where('is_active', 1)->count() ?: 300), 'suffix' => '+', 'label' => 'Referencias activas', 'detail' => 'Esmaltes, pestañas, peluquería, decoración y herramientas, todas en stock real.'],
        ['num' => '32', 'suffix' => '', 'label' => 'Departamentos atendidos', 'detail' => 'Despacho con seguimiento a toda Colombia desde nuestra bodega.'],
        ['num' => '24', 'suffix' => 'h', 'label' => 'Respuesta WhatsApp', 'detail' => 'Resolvemos dudas, novedades y reposiciones sin esperar al lunes.'],
        ['num' => '100', 'suffix' => '%', 'label' => 'Producto original', 'detail' => 'Solo trabajamos con marcas verificadas y proveedores con trazabilidad.'],
    ];
@endphp
@include('partials.ba-divider')
<style>
/* Ramito botánico en las tarjetas de cifras */
.ba-authority__stat > *{position:relative;z-index:1;}
.ba-authority__stat::after{content:"";position:absolute;right:8px;bottom:6px;width:36px;height:46px;z-index:0;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    opacity:.34;transform:rotate(8deg);pointer-events:none;
    transition:transform .5s cubic-bezier(.2,.7,.3,1),opacity .5s ease;}
.ba-authority__stat:hover::after{opacity:.62;transform:rotate(0deg) translateY(-2px);}
</style>
<section class="ba-section ba-authority" aria-labelledby="auth-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">Mayorista en Colombia</span>
            <h2 id="auth-title" class="ba-section-head__title">{{ $homePage->authority_title ?? 'Tu distribuidora de productos profesionales de belleza' }}</h2>
            <div class="ba-divider"></div>
        </header>

        <div class="ba-authority__body" data-anim="fade-up" style="--stagger: 2;">
            <p class="ba-authority__lead">
                Belleza Áurea es la <strong>distribuidora colombiana</strong> donde manicuristas, lashistas, estilistas y dueños de salón
                arman su pedido completo en un solo lugar — esmaltes, pestañas, herramientas, equipos, peluquería y bioseguridad —
                con <strong>precios mayoristas reales</strong> y despacho con seguimiento a todo el país.
            </p>
            <p class="ba-authority__lead">
                Curamos cada referencia pensando en el rendimiento en cabina y en el margen de tu negocio. Sin pedido mínimo,
                sin letra chica y con respaldo directo por WhatsApp cuando lo necesites.
            </p>
        </div>

        <div class="ba-authority__stats">
            @foreach($authStats as $i => $s)
                <div class="ba-authority__stat" data-anim="fade-up" style="--stagger: {{ 3 + $i }};">
                    <div class="ba-authority__num">
                        <span class="ba-authority__num-val">{{ $s['num'] }}</span><span class="ba-authority__num-suf">{{ $s['suffix'] }}</span>
                    </div>
                    <h3 class="ba-authority__stat-label">{{ $s['label'] }}</h3>
                    <p class="ba-authority__stat-detail">{{ $s['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="ba-authority__cta" data-anim="fade-up" style="--stagger: 8;">
            <a href="{{ route('products.index') }}" class="ba-btn-primary">
                <span>Explorar catálogo completo</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a href="{{ \App\Models\ContactPageSetting::whatsappUrl() }}" class="ba-btn-ghost" target="_blank" rel="noopener">
                Hablar por WhatsApp
            </a>
        </div>
    </div>
</section>

<style>
    .ba-authority {
        position: relative;
        background: linear-gradient(180deg, #FFFFFF 0%, #FBF8F2 100%);
        overflow: hidden;
    }
    .ba-authority::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(217,181,109,.08), transparent 60%);
        filter: blur(60px);
        pointer-events: none;
        z-index: 0;
    }
    .ba-authority > .ba-container { position: relative; z-index: 1; }

    .ba-authority__body {
        max-width: 820px;
        margin: 0 auto 64px;
        text-align: center;
    }
    .ba-authority__lead {
        font-size: clamp(15px, 1.5vw, 17px);
        line-height: 1.75;
        color: #4A453F;
        margin: 0 0 18px;
    }
    .ba-authority__lead:last-child { margin-bottom: 0; }
    .ba-authority__lead strong {
        color: #2E2A26;
        font-weight: 600;
        background: linear-gradient(180deg, transparent 70%, rgba(217,181,109,.22) 70%);
        padding: 0 2px;
    }

    .ba-authority__stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 48px;
    }
    .ba-authority__stat {
        background: #FFFFFF;
        border: 1px solid rgba(184,169,153,.18);
        border-radius: 14px;
        padding: 32px 24px 28px;
        text-align: center;
        transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    }
    .ba-authority__stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 48px -16px rgba(190,154,83,.22);
        border-color: rgba(217,181,109,.4);
    }
    .ba-authority__num {
        display: flex;
        align-items: baseline;
        justify-content: center;
        font-family: 'Playfair Display', serif;
        line-height: 1;
        margin-bottom: 14px;
        color: #BE9A53;
    }
    .ba-authority__num-val {
        font-size: clamp(36px, 4.5vw, 56px);
        font-weight: 500;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, #D9B56D 0%, #BE9A53 70%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .ba-authority__num-suf {
        font-size: clamp(22px, 2.5vw, 32px);
        font-weight: 500;
        color: #D9B56D;
        margin-left: 2px;
    }
    .ba-authority__stat-label {
        font-family: 'Playfair Display', serif;
        font-size: 16px;
        font-weight: 500;
        color: #2E2A26;
        margin: 0 0 8px;
        line-height: 1.3;
    }
    .ba-authority__stat-detail {
        font-size: 12.5px;
        line-height: 1.6;
        color: #6B6157;
        margin: 0;
    }

    .ba-authority__cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    @media (max-width: 900px) {
        .ba-authority__stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .ba-authority__stats { grid-template-columns: 1fr; }
    }
</style>

{{-- ============================================================
     10. TESTIMONIALS — desactivado hasta tener reseñas reales del negocio.
     Solo se renderiza si el admin sube testimonios reales en la BD.
     ============================================================ --}}
@if($testimonials->count())
@include('partials.ba-divider')
<section class="ba-section" aria-labelledby="tests-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">Testimonios</span>
            <h2 id="tests-title" class="ba-section-head__title">{{ $homePage->testimonials_title ?? 'Lo que dicen nuestras clientes' }}</h2>
            <div class="ba-divider"></div>
        </header>

        <div class="ba-tests">
            @foreach($testimonials->take(3) as $i => $t)
                <article class="ba-test" data-anim="fade-up" style="--stagger: {{ $i }};">
                    <div class="ba-test__mark">"</div>
                    <p class="ba-test__body">{{ $t->body }}</p>
                    <p class="ba-test__author">{{ $t->name }}</p>
                    @if($t->role ?? null)
                    <p class="ba-test__role">{{ $t->role }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     11. FAQ — Accordion semántico (SEO)
     ============================================================ --}}
@if(!empty($homePage->faqs))
@include('partials.ba-divider')
<section class="ba-section ba-section--cream-soft ba-section--float" aria-labelledby="faq-title">
    <div class="ba-container">
        <header class="ba-section-head" data-anim="fade-up">
            <span class="ba-section-head__label">FAQ</span>
            <h2 id="faq-title" class="ba-section-head__title">{{ $homePage->faq_title ?? 'Preguntas frecuentes' }}</h2>
            <div class="ba-divider"></div>
        </header>

        <div class="ba-faq" itemscope itemtype="https://schema.org/FAQPage">
            @foreach($homePage->faqs as $i => $faq)
                <details {{ $i === 0 ? 'open' : '' }} itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" data-anim="fade-up" style="--stagger: {{ $i % 3 }};">
                    <summary itemprop="name">{{ $faq['q'] ?? '' }}</summary>
                    <div class="ba-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text">{{ $faq['a'] ?? '' }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     12. FINAL CTA — Dark ink
     ============================================================ --}}
@include('partials.ba-divider')
<style>
/* CTA final — botones pill + decoración botánica dorada sobre oscuro */
.ba-section--ink{position:relative;overflow:hidden;}
.ba-btn-gold,.ba-btn-outline-light{border-radius:999px;}
.ba-btn-gold{box-shadow:0 16px 32px -12px rgba(217,181,109,.55);}
.ba-cta{position:relative;z-index:2;}
.ba-cta-leaf{position:absolute;z-index:1;pointer-events:none;opacity:.5;}
.ba-cta-leaf--tl{top:36px;left:clamp(20px,5vw,64px);width:92px;transform:rotate(-6deg);}
.ba-cta-leaf--br{bottom:36px;right:clamp(20px,5vw,64px);width:106px;transform:rotate(184deg);}
@media(max-width:760px){.ba-cta-leaf{display:none;}}
</style>
<section class="ba-section ba-section--ink" aria-labelledby="cta-title">
    {{-- Decoración botánica dorada + destellos de rocío --}}
    <span class="ba-cta-leaf ba-cta-leaf--tl" aria-hidden="true">
        <svg viewBox="0 0 120 120" fill="none">
            <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
            <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".7"/>
            <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".7"/>
            <ellipse cx="34" cy="72" rx="10" ry="3.6" transform="rotate(-45 34 72)" fill="#D9B56D" opacity=".6"/>
        </svg>
    </span>
    <span class="ba-cta-leaf ba-cta-leaf--br" aria-hidden="true">
        <svg viewBox="0 0 120 120" fill="none">
            <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40 M 38 70 Q 70 60 70 30" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
            <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".65"/>
            <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".65"/>
        </svg>
    </span>
    <span class="ba-dew" style="top:76px;left:15%;z-index:1;--d:0s" aria-hidden="true"></span>
    <span class="ba-dew" style="top:130px;left:9%;z-index:1;--d:1s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:80px;right:15%;z-index:1;--d:.6s" aria-hidden="true"></span>
    <span class="ba-dew" style="bottom:132px;right:9%;z-index:1;--d:1.5s" aria-hidden="true"></span>

    <div class="ba-cta">
        <h2 id="cta-title" class="ba-cta__title" data-anim="fade-up">
            @php
                $ctaTitle = $homePage->cta_title ?? '¿Lista para tu ritual áureo?';
                // Hacer italic la palabra "áureo" o última palabra significativa
                if (str_contains(mb_strtolower($ctaTitle), 'áureo')) {
                    $ctaTitle = preg_replace('/(áureo|Áureo)/u', '<em>$1</em>', $ctaTitle);
                }
            @endphp
            {!! \App\Support\SafeHtml::sanitize($ctaTitle) !!}
        </h2>
        @if($homePage->cta_subtitle)
        <p class="ba-cta__sub" data-anim="fade-up" style="--stagger: 2;">{{ $homePage->cta_subtitle }}</p>
        @endif

        <div class="ba-cta__actions" data-anim="fade-up" style="--stagger: 4;">
            <a href="{{ route('products.index') }}" class="ba-btn-gold">{{ $homePage->cta_btn_primary_text ?? 'Comprar ahora' }}</a>
            <a href="{{ route('landing.quiz') }}" class="ba-btn-outline-light">{{ $homePage->cta_btn_secondary_text ?? 'Quiz de piel' }}</a>
        </div>

        @if(!empty($homePage->cta_trust_items))
        <div class="ba-cta__trust" data-anim="fade-up" style="--stagger: 6;">
            @foreach($homePage->cta_trust_items as $t)
                <span>{{ $t }}</span>
            @endforeach
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
    /* =============================================
       Belleza Áurea — Home animations on scroll
       Vanilla IntersectionObserver, sin librerías.
       ============================================= */
    (function () {
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // 1. Hero word-by-word reveal (trigger inmediato)
        const heroTitle = document.querySelector('.ba-hero__title');
        if (heroTitle) {
            requestAnimationFrame(() => heroTitle.classList.add('is-loaded'));
        }

        if (reducedMotion) {
            document.querySelectorAll('[data-anim]').forEach(el => el.classList.add('is-inview'));
            return;
        }

        // 2. Generic scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-inview');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -60px 0px',
        });

        document.querySelectorAll('[data-anim]').forEach(el => observer.observe(el));

        // Failsafe — al cabo de 2.5s mostrar todo lo que no haya entrado en vista
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.querySelectorAll('[data-anim]:not(.is-inview)').forEach(el => el.classList.add('is-inview'));
            }, 2500);
        });

        // 3. Parallax sutil en imagen del hero
        const heroImg = document.querySelector('.ba-hero__product img');
        if (heroImg) {
            let raf = null;
            const onScroll = () => {
                if (raf) return;
                raf = requestAnimationFrame(() => {
                    const y = window.scrollY;
                    if (y < 800) {
                        heroImg.style.transform = `translateY(${y * 0.08}px)`;
                    }
                    raf = null;
                });
            };
            window.addEventListener('scroll', onScroll, { passive: true });
        }
    })();
</script>
@endpush
