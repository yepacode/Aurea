@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('robots', $seo['robots'])
@section('canonical', $seo['canonical'])
@if(!empty($seo['keywords']))@section('keywords', $seo['keywords'])@endif
@section('og_type', $seo['og_type'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $seo['og_image'])
@section('twitter_card', $seo['twitter_card'])
@section('twitter_title', $seo['twitter_title'])
@section('twitter_description', $seo['twitter_description'])
@section('twitter_image', $seo['twitter_image'])
@if(!empty($seo['custom_schema']))@section('custom_schema', $seo['custom_schema'])@endif

@push('schema')
<script type="application/ld+json">{!! $brandSchema !!}</script>
@endpush

@php
    $accent      = $brand->brand_color ?: '#D9B56D';
    $heroImg     = $brand->hero_image_url ?: $brand->banner_url;
    $storyImg    = $brand->story_image_url;
    $heroTitle   = $brand->hero_title ?: $brand->name;
    $storyParas  = $brand->story_content ? preg_split("/\r?\n\r?\n/", trim($brand->story_content)) : [];
    $pillars     = is_array($brand->pillars_json) ? array_values(array_filter($brand->pillars_json, fn($p) => !empty($p['title']))) : [];
@endphp

@push('head')
<style>
    :root {
        --brand-accent: {{ $accent }};
    }

    /* ── HERO ── */
    .bl-hero{position:relative;min-height:500px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#F7F3ED;}
    .bl-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;}
    .bl-hero__overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.10) 0%,transparent 40%,rgba(0,0,0,.35) 100%);}
    .bl-hero__inner{position:relative;text-align:center;padding:80px 24px;max-width:900px;}
    .bl-hero__crumb{font:500 11px/1.4 'Montserrat',sans-serif;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.75);margin:0 0 32px;}
    .bl-hero__crumb.is-light{color:#8B7355;}
    .bl-hero__crumb a{color:inherit;text-decoration:none;transition:color .3s;}
    .bl-hero__crumb a:hover{color:#fff;}
    .bl-hero__crumb.is-light a:hover{color:var(--brand-accent);}
    .bl-hero__logo{max-height:110px;max-width:80%;margin:0 auto 28px;display:block;object-fit:contain;}
    .bl-hero__logo.on-image{filter:drop-shadow(0 4px 20px rgba(0,0,0,.5));}
    .bl-hero__title{font-family:'Playfair Display',serif;font-weight:500;font-size:clamp(38px,5.5vw,68px);line-height:1.05;margin:0 0 20px;color:#fff;text-shadow:0 2px 12px rgba(0,0,0,.4);}
    .bl-hero__title.is-light{color:#2E2A26;text-shadow:none;}
    .bl-hero__tagline{font-family:'Playfair Display',serif;font-style:italic;font-weight:400;font-size:clamp(18px,2.4vw,26px);line-height:1.5;color:rgba(255,255,255,.92);text-shadow:0 2px 8px rgba(0,0,0,.4);max-width:640px;margin:0 auto;}
    .bl-hero__tagline.is-light{color:#6B6157;text-shadow:none;}
    .bl-hero__origin{margin-top:22px;font:500 10px/1 'Montserrat',sans-serif;letter-spacing:.32em;text-transform:uppercase;color:rgba(255,255,255,.7);}
    .bl-hero__origin.is-light{color:#B8A999;}

    @media (max-width: 640px){
        .bl-hero{min-height:320px;}
        .bl-hero__inner{padding:60px 20px;}
        .bl-hero__logo{max-height:80px;margin-bottom:20px;}
    }

    /* ── SECCIONES ── */
    .bl-section{padding:clamp(72px,10vw,120px) 0;}
    .bl-section--cream{background:#FBF8F2;}
    .bl-section--white{background:#FFFFFF;}
    .bl-section--dark{background:linear-gradient(180deg,#F7F0E2 0%,#EDE1CB 100%);}

    .bl-eyebrow{display:block;font:500 11px/1 'Montserrat',sans-serif;letter-spacing:.28em;text-transform:uppercase;color:var(--brand-accent);}
    .bl-h2{font-family:'Playfair Display',serif;font-weight:500;font-size:clamp(28px,4vw,44px);color:#2E2A26;margin:12px 0 0;line-height:1.15;}
    .bl-rule{width:36px;height:1px;background:var(--brand-accent);margin:24px auto 0;}
    .bl-header{text-align:center;margin-bottom:56px;}

    /* ── STORY ── */
    .bl-story{display:grid;grid-template-columns:1fr;gap:48px;align-items:center;}
    @media (min-width: 900px){
        .bl-story{grid-template-columns:1.1fr 1fr;gap:80px;}
        .bl-story.no-image{grid-template-columns:1fr;max-width:760px;margin:0 auto;}
    }
    .bl-story__title{font-family:'Playfair Display',serif;font-weight:500;font-size:clamp(26px,3.6vw,40px);color:#2E2A26;margin:0 0 24px;line-height:1.2;}
    .bl-story__title::after{content:"";display:block;width:48px;height:1px;background:var(--brand-accent);margin-top:18px;}
    .bl-story__body p{font-family:'Montserrat',sans-serif;font-size:16px;line-height:1.85;color:#3B310F;margin:0 0 20px;}
    .bl-story__image{aspect-ratio:4/5;border-radius:2px;overflow:hidden;position:relative;box-shadow:0 20px 50px -20px rgba(139,115,85,.35);}
    .bl-story__image img{width:100%;height:100%;object-fit:cover;display:block;}
    .bl-story__image::after{content:"";position:absolute;inset:0;border:1px solid rgba(217,181,109,.35);pointer-events:none;margin:12px;}

    /* ── PILARES ── */
    .bl-pillars{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;max-width:1120px;margin:0 auto;}
    .bl-pillar{background:linear-gradient(160deg,#FEFCF8,#F8F2E8);border:1px solid rgba(217,181,109,.22);border-radius:4px;padding:36px 28px;text-align:center;transition:transform .5s cubic-bezier(.16,1,.3,1), box-shadow .5s ease;box-shadow:0 4px 14px -8px rgba(139,115,85,.18);}
    .bl-pillar:hover{transform:translateY(-6px);box-shadow:0 20px 40px -20px rgba(139,115,85,.32);}
    .bl-pillar__icon{font-size:36px;line-height:1;margin-bottom:16px;display:inline-block;}
    .bl-pillar__title{font-family:'Playfair Display',serif;font-weight:500;font-size:20px;color:#2E2A26;margin:0 0 10px;}
    .bl-pillar__desc{font-family:'Montserrat',sans-serif;font-size:14px;line-height:1.65;color:#6B6157;margin:0;}

    /* ── PRODUCTOS ── */
    .bl-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:32px 24px;}
    .bl-grid--featured{grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:40px 28px;max-width:1120px;margin:0 auto;}
    .bl-card{display:block;text-decoration:none;color:inherit;position:relative;}
    .bl-card__media{aspect-ratio:4/5;background:#FBF8F2;border-radius:2px;overflow:hidden;margin-bottom:18px;position:relative;display:flex;align-items:center;justify-content:center;}
    .bl-card__media img{max-width:88%;max-height:88%;width:auto;height:auto;object-fit:contain;transition:transform 1.2s ease;}
    .bl-card:hover .bl-card__media img{transform:scale(1.04);}
    .bl-card__badge{position:absolute;top:14px;left:14px;background:rgba(255,255,255,.92);color:#3B310F;font:600 10px/1 'Montserrat',sans-serif;letter-spacing:.14em;text-transform:uppercase;padding:8px 12px;border-radius:2px;border:1px solid var(--brand-accent);z-index:2;}
    .bl-card__cat{font:500 10px/1 'Montserrat',sans-serif;letter-spacing:.22em;text-transform:uppercase;color:#B8A999;margin:0 0 6px;}
    .bl-card__name{font-family:'Playfair Display',serif;font-weight:500;font-size:18px;color:#2E2A26;margin:0 0 10px;line-height:1.25;}
    .bl-card__price{display:flex;align-items:baseline;gap:10px;font-family:'Montserrat',sans-serif;}
    .bl-card__price .now{font-size:16px;font-weight:500;color:#2E2A26;}
    .bl-card__price .was{font-size:13px;color:#B8A999;text-decoration:line-through;}
    .bl-empty{aspect-ratio:4/5;background:#FBF8F2;border-radius:2px;display:flex;align-items:center;justify-content:center;color:var(--brand-accent);font-family:'Playfair Display',serif;font-style:italic;}

    /* ── QUOTE ── */
    .bl-quote{position:relative;max-width:820px;margin:0 auto;text-align:center;padding:0 24px;}
    .bl-quote__wreath{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:min(420px,95%);opacity:.10;pointer-events:none;z-index:0;}
    .bl-quote__text{position:relative;font-family:'Playfair Display',serif;font-style:italic;font-weight:500;font-size:clamp(24px,3.4vw,38px);line-height:1.4;color:#8B7355;margin:0;z-index:1;}
    .bl-quote__mark{display:block;font-size:64px;line-height:.4;color:var(--brand-accent);opacity:.6;margin-bottom:12px;font-family:'Playfair Display',serif;}
    .bl-quote__author{position:relative;margin-top:28px;font:500 12px/1 'Montserrat',sans-serif;letter-spacing:.24em;text-transform:uppercase;color:#6B6157;z-index:1;}

    /* ── CTA ── */
    .bl-cta{text-align:center;padding:clamp(48px,7vw,80px) 24px;}
    .bl-cta__title{font-family:'Playfair Display',serif;font-weight:500;font-size:clamp(24px,3.4vw,36px);color:#2E2A26;margin:0 0 28px;}
    .bl-btn{display:inline-flex;align-items:center;gap:12px;padding:16px 40px;background:var(--brand-accent);color:#3B310F;font:600 12px/1 'Montserrat',sans-serif;letter-spacing:.18em;text-transform:uppercase;text-decoration:none;border-radius:2px;transition:transform .3s, box-shadow .3s, background .3s;box-shadow:0 4px 20px -6px rgba(139,115,85,.4);}
    .bl-btn:hover{transform:translateY(-2px);box-shadow:0 10px 30px -6px rgba(139,115,85,.5);}
    .bl-btn svg{transition:transform .3s;}
    .bl-btn:hover svg{transform:translateX(4px);}

    /* ── Fade-in al scroll ── */
    .bl-reveal{opacity:0;transform:translateY(24px);transition:opacity .9s cubic-bezier(.16,1,.3,1), transform .9s cubic-bezier(.16,1,.3,1);}
    .bl-reveal.is-visible{opacity:1;transform:none;}
    @media (prefers-reduced-motion: reduce){
        .bl-reveal{opacity:1;transform:none;transition:none;}
    }
</style>
@endpush

@section('content')
<main>

    {{-- ══════════ 1) HERO ══════════ --}}
    <section class="bl-hero" @if($heroImg) style="min-height:500px;" @endif>
        @if($heroImg)
            <div class="bl-hero__bg" style="background-image:url('{{ $heroImg }}');"></div>
            <div class="bl-hero__overlay"></div>
        @else
            <div class="bl-hero__bg" style="background:radial-gradient(circle at 30% 30%, #FBF4E6 0%, #F7F3ED 50%, #E8D1C5 100%);"></div>
        @endif

        <div class="bl-hero__inner">
            <nav class="bl-hero__crumb {{ $heroImg ? '' : 'is-light' }}">
                <a href="{{ url('/') }}">Inicio</a>
                <span style="margin:0 10px;">/</span>
                <a href="{{ route('brands.index') }}">Marcas</a>
                <span style="margin:0 10px;">/</span>
                <span>{{ $brand->name }}</span>
            </nav>

            @if($brand->logo_url)
                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}"
                     class="bl-hero__logo {{ $heroImg ? 'on-image' : '' }}"
                     @if($heroImg) style="filter:brightness(0) invert(1) drop-shadow(0 4px 20px rgba(0,0,0,.4));" @endif>
            @endif

            <h1 class="bl-hero__title {{ $heroImg ? '' : 'is-light' }}">{{ $heroTitle }}</h1>

            @if($brand->hero_tagline || $brand->short_description)
                <p class="bl-hero__tagline {{ $heroImg ? '' : 'is-light' }}">
                    {{ $brand->hero_tagline ?: $brand->short_description }}
                </p>
            @endif

            @if($brand->country_origin)
                <p class="bl-hero__origin {{ $heroImg ? '' : 'is-light' }}">Origen · {{ $brand->country_origin }}</p>
            @endif
        </div>
    </section>

    {{-- ══════════ 2) HISTORIA ══════════ --}}
    @if(!empty($storyParas))
        <section class="bl-section bl-section--white">
            <div class="ba-container">
                <div class="bl-story {{ $storyImg ? '' : 'no-image' }} bl-reveal">
                    <div>
                        <h2 class="bl-story__title">{{ $brand->story_title ?: 'Nuestra historia' }}</h2>
                        <div class="bl-story__body">
                            @foreach($storyParas as $para)
                                <p>{{ $para }}</p>
                            @endforeach
                        </div>
                    </div>
                    @if($storyImg)
                        <div class="bl-story__image">
                            <img src="{{ $storyImg }}" alt="Historia de {{ $brand->name }}" loading="lazy">
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ 3) PILARES ══════════ --}}
    @if(!empty($pillars))
        <section class="bl-section bl-section--cream">
            <div class="ba-container">
                <header class="bl-header bl-reveal">
                    <span class="bl-eyebrow">Lo que nos define</span>
                    <h2 class="bl-h2">Nuestros pilares</h2>
                    <div class="bl-rule"></div>
                </header>

                <div class="bl-pillars">
                    @foreach($pillars as $i => $p)
                        <div class="bl-pillar bl-reveal" style="transition-delay:{{ $i * 90 }}ms;">
                            @if(!empty($p['icon']))
                                <div class="bl-pillar__icon">{{ $p['icon'] }}</div>
                            @endif
                            <h3 class="bl-pillar__title">{{ $p['title'] }}</h3>
                            @if(!empty($p['description']))
                                <p class="bl-pillar__desc">{{ $p['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ 4) PRODUCTOS DESTACADOS ══════════ --}}
    @if($featuredProducts->isNotEmpty())
        <section class="bl-section bl-section--white">
            <div class="ba-container">
                <header class="bl-header bl-reveal">
                    <span class="bl-eyebrow">Elegidos por la marca</span>
                    <h2 class="bl-h2">Nuestros favoritos</h2>
                    <div class="bl-rule"></div>
                </header>

                <div class="bl-grid bl-grid--featured">
                    @foreach($featuredProducts as $p)
                        <a href="{{ route('products.show', $p->slug) }}" class="bl-card bl-reveal" style="transition-delay:{{ $loop->index * 60 }}ms;">
                            <div class="bl-card__media">
                                <span class="bl-card__badge">★ Elegido por la marca</span>
                                @if(!empty($p->images))
                                    <img src="{{ asset('storage/'.$p->images[0]) }}" alt="{{ $p->name }}" loading="lazy">
                                @else
                                    <div class="bl-empty">Próximamente</div>
                                @endif
                            </div>
                            @if($p->category)
                                <p class="bl-card__cat">{{ $p->category->name }}</p>
                            @endif
                            <h3 class="bl-card__name">{{ $p->name }}</h3>
                            <div class="bl-card__price">
                                <span class="now">${{ number_format($p->price, 0, ',', '.') }}</span>
                                @if($p->compare_price && $p->compare_price > $p->price)
                                    <span class="was">${{ number_format($p->compare_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ 5) TODOS LOS PRODUCTOS ══════════ --}}
    <section id="coleccion" class="bl-section bl-section--cream">
        <div class="ba-container">
            <header class="bl-header bl-reveal">
                <span class="bl-eyebrow">Catálogo</span>
                <h2 class="bl-h2">Toda la colección</h2>
                <div class="bl-rule"></div>
            </header>

            @if($products->isNotEmpty())
                <div class="bl-grid">
                    @foreach($products as $p)
                        <a href="{{ route('products.show', $p->slug) }}" class="bl-card bl-reveal" style="transition-delay:{{ ($loop->index % 6) * 50 }}ms;">
                            <div class="bl-card__media">
                                @if(!empty($p->images))
                                    <img src="{{ asset('storage/'.$p->images[0]) }}" alt="{{ $p->name }}" loading="lazy">
                                @else
                                    <div class="bl-empty">Próximamente</div>
                                @endif
                            </div>
                            @if($p->category)
                                <p class="bl-card__cat">{{ $p->category->name }}</p>
                            @endif
                            <h3 class="bl-card__name">{{ $p->name }}</h3>
                            <div class="bl-card__price">
                                <span class="now">${{ number_format($p->price, 0, ',', '.') }}</span>
                                @if($p->compare_price && $p->compare_price > $p->price)
                                    <span class="was">${{ number_format($p->compare_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                <div style="margin-top:56px;">{{ $products->links() }}</div>
            @else
                <p style="text-align:center;color:#6B6157;font-family:'Montserrat',sans-serif;">Próximamente catálogo de {{ $brand->name }}.</p>
            @endif
        </div>
    </section>

    {{-- ══════════ 6) QUOTE ══════════ --}}
    @if($brand->quote_text)
        <section class="bl-section bl-section--dark">
            <div class="ba-container">
                <div class="bl-quote bl-reveal">
                    <img class="bl-quote__wreath" src="{{ asset('img/patterns/wreath.svg') }}" alt="" aria-hidden="true">
                    <span class="bl-quote__mark">"</span>
                    <p class="bl-quote__text">{{ $brand->quote_text }}</p>
                    @if($brand->quote_author)
                        <p class="bl-quote__author">— {{ $brand->quote_author }}</p>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════ 7) CTA FINAL ══════════ --}}
    <section class="bl-section bl-section--white" style="padding-top:0;">
        <div class="ba-container bl-cta bl-reveal">
            <h2 class="bl-cta__title">Descubre {{ $brand->name }}</h2>
            <a href="#coleccion" class="bl-btn">
                Ver la colección
                <svg width="16" height="14" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 7h14M9 1l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>

</main>

<script>
    (function(){
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.bl-reveal').forEach(el => el.classList.add('is-visible'));
            return;
        }
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
        document.querySelectorAll('.bl-reveal').forEach(el => obs.observe(el));
    })();
</script>
@endsection
