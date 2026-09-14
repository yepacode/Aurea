<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta (overridable per page) --}}
    @php
        $__defaultTitle = 'Belleza Áurea | Cosmética natural, elegante y atemporal';
        $__defaultDesc  = 'Belleza natural, elegante y atemporal. Skincare, fragancias y rituales de belleza premium en Belleza Áurea.';
        $__pageTitle    = trim($__env->yieldContent('title', $__defaultTitle));
        $__pageDesc     = trim($__env->yieldContent('meta_description', $__defaultDesc));
        // Si la página define og_title/og_description los usamos; si no, caen al title/description reales de la página
        // (esto evita que TODAS las páginas compartan el mismo OG genérico de marca).
        $__ogTitle       = trim($__env->yieldContent('og_title')) ?: $__pageTitle;
        $__ogDesc        = trim($__env->yieldContent('og_description')) ?: $__pageDesc;
        $__twitterTitle  = trim($__env->yieldContent('twitter_title')) ?: $__ogTitle;
        $__twitterDesc   = trim($__env->yieldContent('twitter_description')) ?: $__ogDesc;
    @endphp
    <title>{{ $__pageTitle }}</title>
    <meta name="description" content="{{ $__pageDesc }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    @hasSection('keywords')<meta name="keywords" content="@yield('keywords')">@endif

    {{-- Canonical --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $__ogTitle }}">
    <meta property="og:description" content="{{ $__ogDesc }}">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('img/brand/logo-principal.png'))">
    <meta property="og:image:secure_url" content="@yield('og_image', asset('img/brand/logo-principal.png'))">
    <meta property="og:image:width" content="@yield('og_image_width', '1200')">
    <meta property="og:image:height" content="@yield('og_image_height', '630')">
    <meta property="og:image:alt" content="@yield('og_image_alt', 'Belleza Áurea — cosmética e insumos de belleza')">
    <meta property="og:site_name" content="Belleza Áurea">
    <meta property="og:locale" content="es_CO">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="{{ $__twitterTitle }}">
    <meta name="twitter:description" content="{{ $__twitterDesc }}">
    <meta name="twitter:image" content="@yield('twitter_image', asset('img/brand/logo-principal.png'))">
    <meta name="twitter:image:alt" content="@yield('og_image_alt', 'Belleza Áurea — cosmética e insumos de belleza')">

    {{-- Theme color --}}
    <meta name="theme-color" content="#F7F3ED">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/brand/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/brand/favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/brand/favicon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    {{-- Google Fonts: Playfair Display + Montserrat (Belleza Áurea) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fondo botánico sutil (flores + hojas de marca) en todo el sitio --}}
    <style>
        :root{ --ba-botanical:url('{{ asset('img/patterns/botanical.svg') }}'); }
        body{
            background-color:#F7F3ED;
            background-image:var(--ba-botanical);
            background-repeat:repeat;
            background-size:480px;
            background-attachment:fixed;
        }
        /* Las secciones claras dejan ver el patrón; las oscuras/imagen no. */
        .ba-section--cream,
        .ba-section--cream-soft{
            background-color:transparent !important;
            background-image:none !important;
        }
        /* La franja oscura y secciones con imagen conservan su fondo sólido */
        .ba-section--ink{ background-image:none; }

        /* ─── Secciones flotantes en tarjeta ─── */
        .ba-section--float{padding:clamp(14px,3vh,30px) clamp(16px,4vw,48px)!important;}
        .ba-section--float > .ba-container{
            background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
            border:1px solid rgba(217,181,109,.16);
            border-radius:30px;
            box-shadow:0 46px 92px -52px rgba(120,92,44,.55),0 6px 22px -12px rgba(0,0,0,.06);
            padding:clamp(48px,6vw,88px) clamp(24px,5vw,64px);
            max-width:1200px;}

        /* ─── Divisor botánico con rocío brillante ─── */
        .ba-div{display:flex;align-items:center;justify-content:center;gap:20px;
            max-width:600px;margin:0 auto;padding:clamp(26px,5vh,54px) 24px;}
        .ba-div__line{height:1.5px;flex:1;max-width:200px;border-radius:2px;
            background:linear-gradient(90deg,transparent,rgba(190,154,83,.85));}
        .ba-div__line--r{background:linear-gradient(90deg,rgba(190,154,83,.85),transparent);}
        .ba-div__sprig{position:relative;flex:0 0 auto;line-height:0;}
        .ba-dew{position:absolute;width:13px;height:13px;border-radius:50%;pointer-events:none;
            background:radial-gradient(circle,#ffffff 0%,#FDF3D2 42%,rgba(217,181,109,0) 74%);
            filter:drop-shadow(0 0 6px rgba(255,250,230,1)) drop-shadow(0 0 13px rgba(232,204,146,.85)) drop-shadow(0 0 20px rgba(217,181,109,.5));
            opacity:.5;transform:scale(.6);
            animation:ba-dew 2.8s ease-in-out infinite;animation-delay:var(--d,0s);}
        /* Destello en cruz (glint de agua) */
        .ba-dew::before,.ba-dew::after{content:"";position:absolute;left:50%;top:50%;
            background:linear-gradient(currentColor,transparent);color:rgba(255,255,255,.95);}
        .ba-dew::before{width:2px;height:190%;transform:translate(-50%,-50%);
            background:linear-gradient(to bottom,rgba(255,255,255,0),rgba(255,255,255,.95),rgba(255,255,255,0));border-radius:2px;}
        .ba-dew::after{height:2px;width:190%;transform:translate(-50%,-50%);
            background:linear-gradient(to right,rgba(255,255,255,0),rgba(255,255,255,.95),rgba(255,255,255,0));border-radius:2px;}
        @keyframes ba-dew{0%,100%{opacity:.45;transform:scale(.6);}50%{opacity:1;transform:scale(1.1);}}
        @media(prefers-reduced-motion:reduce){ .ba-dew{animation:none;opacity:.85;} }

        /* ─── A11y: focus visible dorado (WCAG 2.4.7) ─── */
        input:focus,select:focus,textarea:focus,button:focus,a:focus,[role="button"]:focus{
            outline:2px solid #BE9A53 !important;
            outline-offset:2px !important;
            box-shadow:0 0 0 4px rgba(217,181,109,.25) !important;
        }
        input:focus:not(:focus-visible),select:focus:not(:focus-visible),textarea:focus:not(:focus-visible),button:focus:not(:focus-visible),a:focus:not(:focus-visible),[role="button"]:focus:not(:focus-visible){
            outline:none !important;
            box-shadow:none !important;
        }
    </style>

    {{-- Schema.org JSON-LD --}}
    @stack('schema')
    @hasSection('custom_schema')
    <script type="application/ld+json">@yield('custom_schema')</script>
    @endif

    {{-- Page-specific head content --}}
    @stack('head')

    {{-- PWA: registro del service worker (app instalable) --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
            });
        }
    </script>

    {{-- Analytics (GA4 + Meta Pixel) — solo se imprime si el admin capturó IDs y no estamos en local --}}
    @include('partials.analytics-head')
</head>

<body class="font-body min-h-screen flex flex-col antialiased @yield('body_class', 'bg-cream text-ink')">
    {{-- ============================================================
         INTRO / PRELOADER — video de marca (solo home, 1 vez por sesión)
         ============================================================ --}}
    @if(request()->routeIs('home'))
    <style>
        #ba-intro{position:fixed;inset:0;z-index:100000;background:#2E2A26;
            display:flex;align-items:center;justify-content:center;overflow:hidden;
            opacity:1;transition:opacity .8s ease;}
        #ba-intro.is-hiding{opacity:0;}
        #ba-intro.is-gone{display:none!important;}
        #ba-intro video{width:100%;height:100%;object-fit:cover;display:block;
            filter:brightness(.8) saturate(.9) contrast(.98);}
        #ba-intro__dim{position:absolute;inset:0;z-index:1;pointer-events:none;
            background:linear-gradient(180deg, rgba(46,42,38,.34) 0%, rgba(46,42,38,.42) 100%),
                       radial-gradient(120% 100% at 50% 38%, transparent 34%, rgba(46,42,38,.4) 100%);}
        #ba-intro__flash{position:absolute;inset:0;z-index:3;pointer-events:none;opacity:0;transition:opacity .45s ease;
            background:radial-gradient(circle at 50% 45%, #ffffff 0%, #F3E2B4 32%, rgba(217,181,109,.4) 55%, transparent 72%);}
        #ba-intro.is-flash #ba-intro__flash{opacity:1;}
        #ba-intro__skip{position:absolute;bottom:clamp(20px,4vw,34px);right:clamp(20px,4vw,34px);z-index:4;
            display:inline-flex;align-items:center;gap:9px;padding:12px 22px;border-radius:999px;
            border:1px solid rgba(255,255,255,.55);background:rgba(46,42,38,.4);
            -webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);color:#fff;cursor:pointer;
            font:600 11px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.16em;text-transform:uppercase;
            transition:background .3s ease,transform .3s ease;}
        #ba-intro__skip:hover{background:rgba(46,42,38,.7);transform:translateY(-2px);}
        body.ba-intro-lock{overflow:hidden;}
        @media (prefers-reduced-motion: reduce){#ba-intro{display:none!important;}}
    </style>
    <div id="ba-intro" role="dialog" aria-label="Introducción Belleza Áurea">
        <video id="ba-intro__video" muted playsinline preload="auto" autoplay aria-hidden="true">
            <source src="{{ asset('intro/intro.mp4') }}" type="video/mp4">
        </video>
        <div id="ba-intro__dim"></div>
        <div id="ba-intro__flash"></div>
        <button id="ba-intro__skip" type="button">
            Saltar intro
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>
    <script>
    (function(){
        var KEY = 'ba_intro_seen';
        var el = document.getElementById('ba-intro');
        if(!el) return;
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if(sessionStorage.getItem(KEY) || reduce){ el.parentNode && el.parentNode.removeChild(el); return; }

        var vid  = document.getElementById('ba-intro__video');
        var skip = document.getElementById('ba-intro__skip');
        document.body.classList.add('ba-intro-lock');
        var done = false;

        var SPEED = 1.8; // más rápido para que no tome tanto tiempo
        function setSpeed(){ try{ vid.playbackRate = SPEED; }catch(e){} }
        setSpeed();
        vid.addEventListener('loadedmetadata', setSpeed);
        vid.addEventListener('play', setSpeed);

        function hide(){
            if(done) return; done = true;
            sessionStorage.setItem(KEY, '1');
            el.classList.add('is-flash');
            setTimeout(function(){ el.classList.add('is-hiding'); }, 180);
            setTimeout(function(){
                el.classList.add('is-gone');
                document.body.classList.remove('ba-intro-lock');
                try{ vid.pause(); }catch(e){}
                el.parentNode && el.parentNode.removeChild(el);
            }, 950);
        }

        vid.addEventListener('timeupdate', function(){
            if(vid.duration && vid.currentTime >= vid.duration - 0.3){ hide(); }
        });
        vid.addEventListener('ended', hide);
        skip.addEventListener('click', hide);
        setTimeout(function(){ if(!done) hide(); }, 11000);
        var p = vid.play();
        if(p && p.catch){ p.catch(function(){ setTimeout(function(){ if(!done) hide(); }, 2200); }); }
    })();
    </script>
    @endif

    {{-- Skip to content (accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-gold focus:text-white focus:px-4 focus:py-2 focus:rounded">
        Saltar al contenido
    </a>

    {{-- Navigation --}}
    @include('partials.navbar')

    {{-- Banner del programa "Recomienda y gana" cuando llega con ?ref=XXX --}}
    @include('partials.referral-banner')

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition class="fixed top-20 right-4 z-50 bg-success text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition class="fixed top-20 right-4 z-50 bg-danger text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Content --}}
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- WhatsApp: widget con FAB colapsado + popup expandido (online/offline según horario) --}}
    @include('partials.whatsapp-widget')

    {{-- Activa el modo "reveal" solo cuando hay JS (evita contenido invisible si JS falla) --}}
    <script>document.documentElement.classList.add('reveal-ready');</script>

    {{-- Alpine x-reveal directive + vanilla reveal observer --}}
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('reveal', (el, { modifiers }, { cleanup }) => {
            el.classList.add('reveal');
            const observer = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        el.classList.add('visible');
                        if (modifiers.includes('once')) observer.disconnect();
                    }
                },
                { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
            );
            observer.observe(el);
            cleanup(() => observer.disconnect());
        });
    });
    /* Hero scroll-driven image gallery */
    window.heroGallery = () => ({
        current: 0,
        total: 8,
        onScroll() {
            const step = 120; // px de scroll por cambio de imagen
            this.current = Math.min(Math.floor(window.scrollY / step), this.total - 1);
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const items = document.querySelectorAll('.reveal');
        const obs = new IntersectionObserver(
            entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
            { threshold: 0.1, rootMargin: '0px 0px -30px 0px' }
        );
        items.forEach(el => obs.observe(el));
    });
    /* Failsafe: si por alguna razón el observer no dispara (crawler, no-scroll, etc.),
       todos los elementos .reveal se muestran tras unos segundos. */
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.querySelectorAll('.reveal:not(.visible)').forEach(el => el.classList.add('visible'));
        }, 1500);
    });
    </script>

    {{-- Wishlist (favoritos) — función global toggleWishlist --}}
    @include('partials.wishlist-script')

    {{-- Popup de bienvenida (cupón 10%) --}}
    @include('partials.welcome-popup')

    {{-- Prompt de suscripción a notificaciones push (aparece a los 30s) --}}
    @include('partials.push-prompt')

    @stack('scripts')
</body>
</html>
