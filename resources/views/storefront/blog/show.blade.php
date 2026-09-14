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
    {!! $schema !!}
    {!! $breadcrumbs !!}
@endpush

@section('content')
<style>
    /* ── Article typography ── */
    .article-content { font-size:16px; line-height:1.8; color:var(--color-text-primary); }
    .article-content h2 { font-size:22px; font-weight:500; margin:2rem 0 1rem; color:var(--color-text-primary); padding-bottom:8px; border-bottom:2px solid #D9B56D; display:inline-block; }
    .article-content h3 { font-size:18px; font-weight:500; margin:1.5rem 0 .75rem; color:var(--color-text-primary); }
    .article-content p { margin-bottom:1.25rem; }
    .article-content strong { font-weight:600; color:var(--color-text-primary); }
    .article-content ul, .article-content ol { margin:1rem 0 1.25rem 1.5rem; }
    .article-content li { margin-bottom:.5rem; line-height:1.7; }
    .article-content blockquote { border-left:3px solid #D9B56D; padding:12px 20px; margin:1.5rem 0; background:rgba(217,181,109,0.06); border-radius:0 8px 8px 0; font-style:italic; color:var(--color-text-secondary); }
    .article-content table { width:100%; border-collapse:collapse; margin:1.5rem 0; font-size:14px; }
    .article-content th { background:rgba(217,181,109,0.1); padding:10px 14px; text-align:left; font-weight:500; border-bottom:1px solid rgba(217,181,109,0.2); }
    .article-content td { padding:10px 14px; border-bottom:0.5px solid var(--color-border-tertiary); }
    .article-content a { color:#D9B56D; text-decoration:underline; }
    .article-content img { border-radius:12px; margin:1.5rem 0; max-width:100%; }

    /* ── Layout ── */
    .blog-body-grid { display:grid; grid-template-columns:1fr 300px; gap:40px; max-width:1100px; margin:0 auto; padding:48px 24px 48px; }
    @media(max-width:1023px){ .blog-body-grid { grid-template-columns:1fr; padding-top:40px; } }

    /* ── Sidebar ── */
    .blog-sidebar { position:sticky; top:24px; display:flex; flex-direction:column; gap:20px; align-self:start; }
    @media(max-width:1023px){ .blog-sidebar { position:static; } }
    .sidebar-card { border:0.5px solid var(--color-border-tertiary); border-radius:12px; padding:20px; }
    .sidebar-title { font-size:13px; font-weight:500; margin-bottom:12px; color:var(--color-text-primary); }
    .sidebar-row { display:flex; justify-content:space-between; font-size:13px; padding:6px 0; border-bottom:0.5px solid var(--color-border-tertiary); }
    .sidebar-row:last-child { border-bottom:none; }
    .sidebar-row span:first-child { color:var(--color-text-primary); }
    .sidebar-row span:last-child { color:var(--color-text-secondary); }

    .sidebar-product { display:flex; gap:12px; align-items:center; padding:10px 0; border-bottom:0.5px solid var(--color-border-tertiary); cursor:pointer; transition:opacity .15s; }
    .sidebar-product:last-of-type { border-bottom:none; }
    .sidebar-product:hover { opacity:.75; }
    .sidebar-product-img { width:48px; height:48px; border-radius:8px; flex-shrink:0; background:linear-gradient(135deg,#3A352E,#2E2A26); }

    .sidebar-newsletter { background:rgba(217,181,109,0.06); border:0.5px solid rgba(217,181,109,0.2); border-radius:12px; padding:20px; }
    .sidebar-newsletter input[type="email"] { width:100%; border:0.5px solid var(--color-border-secondary); border-radius:8px; padding:8px 12px; font-size:13px; margin-bottom:8px; background:var(--color-background-primary); color:var(--color-text-primary); }
    .sidebar-newsletter button { width:100%; background:#D9B56D; color:#3B310F; border:none; border-radius:8px; padding:9px; font-size:13px; cursor:pointer; transition:background .15s; }
    .sidebar-newsletter button:hover { background:#BE9A53; }

    /* ── Share bar ── */
    .share-bar { display:flex; align-items:center; gap:12px; margin-top:2rem; padding-top:1.5rem; border-top:0.5px solid var(--color-border-tertiary); flex-wrap:wrap; }
    .share-btn { border:0.5px solid var(--color-border-secondary); border-radius:8px; padding:8px 16px; font-size:13px; background:transparent; color:var(--color-text-secondary); cursor:pointer; transition:background .15s; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .share-btn:hover { background:var(--color-background-secondary); }

    /* ── Full-width sections ── */
    .fw-section { opacity:0; transform:translateY(20px); transition:opacity .5s ease, transform .5s ease; }
    .fw-section.visible { opacity:1; transform:translateY(0); }

    /* ── Related cards (mirror index) ── */
    .related-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; max-width:1100px; margin:0 auto; }
    @media(max-width:1023px){ .related-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:639px){ .related-grid { grid-template-columns:1fr; } }

    .r-card { border-radius:12px; overflow:hidden; background:var(--color-background-primary); border:0.5px solid var(--color-border-tertiary); transition:transform .25s ease, box-shadow .25s ease; cursor:pointer; }
    .r-card:hover { transform:translateY(-6px); box-shadow:0 16px 40px rgba(0,0,0,0.12); }
    .r-card:hover .r-arrow { transform:translateX(4px); }
    .r-arrow { transition:transform .2s ease; }

    /* ── Products grid ── */
    .products-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; max-width:1100px; margin:0 auto; }
    @media(max-width:1023px){ .products-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:639px){ .products-grid { grid-template-columns:1fr; } }
    .p-card { border-radius:12px; overflow:hidden; border:0.5px solid var(--color-border-tertiary); background:var(--color-background-primary); transition:transform .25s ease, box-shadow .25s ease; }
    .p-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,0.1); }

    /* ── Hero (oscuro elegante, aire de marca) ── */
    .blog-hero { position:relative; overflow:hidden; background:#2E2A26; padding:clamp(50px,8vh,110px) 0 clamp(30px,4vh,52px); }
    .blog-hero__glow { position:absolute; inset:0; pointer-events:none; opacity:.4; background:radial-gradient(ellipse at 50% 30%, #D9B56D 0%, transparent 60%); }
    .blog-hero-content { position:relative; z-index:2; width:100%; max-width:820px; margin:0 auto; padding:0 32px; text-align:center; }
    .blog-hero__leaf { position:absolute; z-index:1; pointer-events:none; opacity:.6; }
    .blog-hero__leaf--tl { top:26px; left:clamp(16px,5vw,84px); width:82px; transform:rotate(-8deg); }
    .blog-hero__leaf--br { bottom:20px; right:clamp(16px,5vw,84px); width:96px; transform:rotate(184deg); }
    .blog-cover { max-width:820px; margin:26px auto 0; border-radius:20px; overflow:hidden; aspect-ratio:16/8; box-shadow:0 40px 84px -40px rgba(0,0,0,.45); border:1px solid rgba(217,181,109,.22); }
    .blog-cover img { width:100%; height:100%; object-fit:cover; }
    @media(max-width:760px){ .blog-hero__leaf{display:none;} }
    .hero-anim { opacity:0; transform:translateY(12px); }

    @media(max-width:768px){
        .blog-hero-content { padding:0 20px!important; }
    }

    @media(prefers-reduced-motion:reduce){
        .fw-section { opacity:1!important; transform:none!important; transition:none!important; }
        .r-card, .p-card { transition:none!important; }
        .r-arrow { transition:none!important; }
        .hero-anim { opacity:1!important; transform:none!important; transition:none!important; }
    }
</style>

{{-- Reading progress bar --}}
<div id="reading-progress" style="position:fixed;top:0;left:0;z-index:9999;height:3px;width:0%;background:linear-gradient(90deg,#D9B56D,#4CC9F0);transition:width .1s linear;pointer-events:none;"></div>

{{-- ════════════════════════════════════════════════════════════════
     ZONA 1 — HERO
     ════════════════════════════════════════════════════════════════ --}}
<section class="blog-hero">
    <div class="blog-hero__glow"></div>
    {{-- Detalles botánicos + rocío --}}
    <svg class="blog-hero__leaf blog-hero__leaf--tl" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".6"/>
        <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".6"/>
    </svg>
    <svg class="blog-hero__leaf blog-hero__leaf--br" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M20 100 Q 60 60 40 20 M 20 100 Q 70 80 90 40 M 38 70 Q 70 60 70 30" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round"/>
        <ellipse cx="48" cy="50" rx="12" ry="4.2" transform="rotate(-30 48 50)" fill="#D9B56D" opacity=".55"/>
        <ellipse cx="68" cy="32" rx="10" ry="3.6" transform="rotate(-15 68 32)" fill="#D9B56D" opacity=".55"/>
    </svg>
    <span class="ba-dew" style="top:30%;left:15%;z-index:1;--d:0s" aria-hidden="true"></span>
    <span class="ba-dew" style="top:24%;right:17%;z-index:1;--d:1s" aria-hidden="true"></span>

    <div class="blog-hero-content">
        {{-- Breadcrumb --}}
        <div class="hero-anim" style="transition:opacity .5s ease,transform .5s ease;margin-bottom:16px;">
            <span style="font-size:12px;color:rgba(247,243,237,0.5);">
                <a href="{{ url('/') }}" style="color:rgba(247,243,237,0.5);text-decoration:none;" onmouseover="this.style.color='#E8CC92'" onmouseout="this.style.color='rgba(247,243,237,0.5)'">Inicio</a>
                <span style="margin:0 6px;opacity:.5;">&middot;</span>
                <a href="{{ $crumbParentUrl ?? route('blog.index') }}" style="color:rgba(247,243,237,0.5);text-decoration:none;" onmouseover="this.style.color='#E8CC92'" onmouseout="this.style.color='rgba(247,243,237,0.5)'">{{ $crumbParentLabel ?? 'Blog' }}</a>
                <span style="margin:0 6px;opacity:.5;">&middot;</span>
                <span style="color:rgba(247,243,237,0.75);">{{ Str::limit($post->title, 42) }}</span>
            </span>
        </div>

        {{-- Category badge --}}
        <div class="hero-anim" style="transition:opacity .5s ease .1s,transform .5s ease .1s;margin-bottom:16px;">
            <span style="display:inline-block;font-size:10px;font-weight:600;padding:5px 14px;border-radius:999px;background:#F1E9DA;color:#A9853C;letter-spacing:.12em;text-transform:uppercase;">{{ $post->focus_keyword ?? $post->category ?? 'Belleza' }}</span>
        </div>

        {{-- Title --}}
        <div class="hero-anim" style="transition:opacity .6s ease .2s,transform .6s ease .2s;margin-bottom:18px;">
            <h1 class="font-brand" style="font-family:'Playfair Display',serif;font-size:clamp(28px,4.4vw,46px);font-weight:600;color:#FBF8F2;line-height:1.12;max-width:760px;margin:0 auto;">{{ $post->title }}</h1>
        </div>

        {{-- Meta + share --}}
        <div class="hero-anim" style="transition:opacity .5s ease .35s,transform .5s ease .35s;display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:14px;">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:12px;color:rgba(247,243,237,0.55);">
                <span>{{ $post->reading_time ?? 1 }} min lectura</span>
                <span style="opacity:.5;">&middot;</span>
                <span>{{ $post->published_at?->translatedFormat('d M Y') ?? $post->created_at->translatedFormat('d M Y') }}</span>
                <span style="opacity:.5;">&middot;</span>
                <span>{{ $post->author_name ?? 'Belleza Áurea' }}</span>
            </div>
            <button onclick="sharePost()" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.75);border-radius:999px;padding:7px 16px;font-size:12px;cursor:pointer;transition:all .25s;-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);" onmouseover="this.style.borderColor='#E8CC92';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.75)'">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="10.5" cy="2.5" r="1.5" stroke="currentColor" stroke-width="1.2"/><circle cx="10.5" cy="10.5" r="1.5" stroke="currentColor" stroke-width="1.2"/><circle cx="2.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 5.8L9 3.2M4 7.2L9 9.8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                <span id="share-text">Compartir</span>
            </button>
        </div>

        {{-- Portada (si existe) --}}
        @if($post->image)
        <div class="blog-cover hero-anim" style="transition:opacity .6s ease .45s,transform .6s ease .45s;">
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->featured_image_alt ?? $post->title }}">
        </div>
        @endif
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════════
     ZONA 2 — BODY + SIDEBAR
     ════════════════════════════════════════════════════════════════ --}}
<div class="blog-body-grid">
    {{-- ── Main column ── --}}
    <main>
        <div class="article-content">
            {!! \App\Support\SafeHtml::sanitize($post->content) !!}
        </div>

        {{-- Share bar --}}
        <div class="share-bar">
            <span style="font-size:13px; color:var(--color-text-secondary);">¿Te fue útil? Compártelo</span>
            <button class="share-btn" id="copy-link-btn" onclick="copyLink(this)">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 0 0-5.656 0l-4 4a4 4 0 1 0 5.656 5.656l1.102-1.101m-.758-4.899a4 4 0 0 0 5.656 0l4-4a4 4 0 0 0-5.656-5.656l-1.1 1.1"/></svg>
                <span>Copiar enlace</span>
            </button>
            <a class="share-btn" href="https://wa.me/?text={{ urlencode($post->title . ' ' . url('/blog/' . $post->slug)) }}" target="_blank" rel="noopener">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.12.553 4.113 1.519 5.845L.053 23.681a.5.5 0 0 0 .611.612l5.836-1.466A11.948 11.948 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-1.97 0-3.834-.558-5.42-1.524l-.389-.233-3.462.87.87-3.462-.233-.389A9.709 9.709 0 0 1 2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
                <span>WhatsApp</span>
            </a>
        </div>
    </main>

    {{-- ── Sidebar ── --}}
    <aside class="blog-sidebar">
        {{-- Widget 1: About this article --}}
        <div class="sidebar-card">
            <p class="sidebar-title">Sobre este artículo</p>
            @if($post->reading_time)
            <div class="sidebar-row">
                <span>Tiempo de lectura</span>
                <span>{{ $post->reading_time }} min</span>
            </div>
            @endif
            @if($post->published_at)
            <div class="sidebar-row">
                <span>Publicado</span>
                <span>{{ $post->published_at->format('d M Y') }}</span>
            </div>
            @endif
            @if($post->focus_keyword)
            <div class="sidebar-row">
                <span>Tema</span>
                <span>{{ $post->focus_keyword }}</span>
            </div>
            @endif
        </div>

        {{-- Widget 2: Products --}}
        <div class="sidebar-card">
            <p class="sidebar-title">Nuestros productos</p>
            @forelse($products as $prod)
                <a href="{{ route('products.show', $prod['slug']) }}" class="sidebar-product" style="text-decoration:none; color:inherit;">
                    @if(!empty($prod['image']))
                        <div class="sidebar-product-img" style="background:#f1f5f9 url('{{ asset('storage/' . $prod['image']) }}') center/cover no-repeat;"></div>
                    @else
                        <div class="sidebar-product-img"></div>
                    @endif
                    <div>
                        <p style="font-size:13px; font-weight:500; margin:0; color:var(--color-text-primary);">{{ $prod['name'] }}</p>
                        <p style="font-size:12px; margin:2px 0 0; color:var(--color-text-secondary);">{{ $prod['type'] }} · {{ $prod['price'] }}</p>
                    </div>
                </a>
            @empty
                <p style="font-size:12px;color:var(--color-text-secondary);margin:0;">Pronto añadiremos productos.</p>
            @endforelse
            <a href="{{ route('products.index') }}" style="display:inline-block; margin-top:12px; font-size:13px; color:#D9B56D; text-decoration:none;">Ver todos los productos &rarr;</a>
        </div>

        {{-- Widget 3: Newsletter --}}
        <div class="sidebar-newsletter">
            <p style="font-size:14px; font-weight:500; color:var(--color-text-primary); margin:0 0 4px;">Recibe más consejos</p>
            <p style="font-size:12px; color:var(--color-text-secondary); margin:0 0 12px;">Tips semanales para cuidar tu belleza</p>
            <input type="email" placeholder="Tu correo electrónico" aria-label="Correo electrónico para suscribirte">
            <button type="button" aria-label="Suscribirme al newsletter">Suscribirme</button>
        </div>
    </aside>
</div>

{{-- ════════════════════════════════════════════════════════════════
     ZONA 3 — ARTÍCULOS RELACIONADOS
     ════════════════════════════════════════════════════════════════ --}}
@if($recent->isNotEmpty())
<section class="fw-section" style="background:var(--color-background-secondary); padding:48px 24px;">
    <div style="max-width:1100px; margin:0 auto;">
        <p style="font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#D9B56D; margin-bottom:8px;">SIGUE LEYENDO</p>
        <h2 class="font-brand" style="font-size:24px; font-weight:700; color:var(--color-text-primary); margin:0 0 28px;">Artículos relacionados</h2>

        <div class="related-grid">
            @foreach($recent as $related)
                @php
                    $gradients = [
                        'linear-gradient(135deg, #3A352E, #2E2A26)',
                        'linear-gradient(135deg, #3A352E, #2E2A26)',
                        'linear-gradient(135deg, #1a0a2e, #2d1b69)',
                    ];
                    $grad = $gradients[$loop->index % 3];
                @endphp
                <article class="r-card" onclick="location.href='{{ route('blog.show', $related->slug) }}'">
                    <div style="height:180px; overflow:hidden; position:relative;">
                        @if($related->image)
                            <img src="{{ asset('storage/' . $related->image) }}"
                                 alt="{{ $related->featured_image_alt ?? $related->title }}"
                                 style="width:100%; height:100%; object-fit:cover;" loading="lazy">
                        @else
                            <div style="width:100%; height:100%; background:{{ $grad }}; display:flex; align-items:center; justify-content:center;">
                                <span class="font-brand" style="font-size:72px; font-weight:700; color:rgba(255,255,255,0.06); user-select:none;">{{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        @endif
                        @if($related->focus_keyword)
                            <span style="position:absolute; top:12px; left:12px; background:rgba(217,181,109,0.85); color:#fff; font-size:10px; padding:4px 10px; border-radius:20px; font-weight:500; text-transform:capitalize;">{{ $related->focus_keyword }}</span>
                        @endif
                    </div>
                    <div style="padding:16px 18px 0;">
                        <div style="font-size:11px; color:var(--color-text-secondary); margin-bottom:8px;">
                            {{ $related->published_at?->format('d M Y') }}
                            @if($related->reading_time)
                                <span style="margin:0 4px;">&middot;</span>
                                {{ $related->reading_time }} min lectura
                            @endif
                        </div>
                        <h3 class="font-brand" style="font-size:15px; font-weight:500; line-height:1.4; color:var(--color-text-primary); margin:0 0 6px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">{{ $related->title }}</h3>
                    </div>
                    <div style="padding:0 18px 16px;">
                        <div style="border-top:0.5px solid var(--color-border-tertiary); padding-top:12px; display:flex; align-items:center; justify-content:space-between;">
                            <span style="color:#D9B56D; font-size:12px; font-weight:500; display:inline-flex; align-items:center;">
                                Leer artículo
                                <svg class="r-arrow" style="margin-left:4px;" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════════════════════════════
     ZONA 4 — PRODUCTOS DESTACADOS
     ════════════════════════════════════════════════════════════════ --}}
<section class="fw-section" style="background:var(--color-background-primary); padding:48px 24px;">
    <div style="max-width:1100px; margin:0 auto; text-align:center; margin-bottom:32px;">
        <p style="font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#D9B56D; margin-bottom:8px;">NUESTROS FAVORITOS</p>
        <h2 class="font-brand" style="font-size:24px; font-weight:700; color:var(--color-text-primary); margin:0 0 6px;">Nuestros productos</h2>
        <p style="font-size:14px; color:var(--color-text-secondary); margin:0;">Insumos y cosmética de belleza, con envío a toda Colombia</p>
    </div>

    <div class="products-grid" style="max-width:1100px; margin:0 auto;">
        @forelse($products as $i => $prod)
            @php
                $pGrads = [
                    'linear-gradient(135deg, #3A352E, #2E2A26)',
                    'linear-gradient(135deg, #3A352E, #2E2A26)',
                    'linear-gradient(135deg, #1a0a2e, #2d1b69)',
                ];
            @endphp
            <div class="p-card">
                <div style="height:180px; position:relative; overflow:hidden;">
                    @if(!empty($prod['image']))
                        <img src="{{ asset('storage/' . $prod['image']) }}"
                             alt="{{ $prod['name'] }}"
                             style="width:100%;height:100%;object-fit:cover;display:block;"
                             loading="lazy">
                    @else
                        <div style="width:100%;height:100%;background:{{ $pGrads[$i % 3] }}; display:flex; align-items:center; justify-content:center;">
                            <span class="font-brand" style="font-size:64px; font-weight:700; color:rgba(255,255,255,0.06); user-select:none;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    @endif
                    <span style="position:absolute; top:12px; left:12px; background:rgba(13,17,23,0.78); color:#fff; font-size:10px; padding:4px 10px; border-radius:20px; font-weight:500;">{{ $prod['type'] }}</span>
                </div>
                <div style="padding:16px 18px 18px;">
                    <h3 class="font-brand" style="font-size:16px; font-weight:600; color:var(--color-text-primary); margin:0 0 4px;">{{ $prod['name'] }}</h3>
                    <div style="font-size:14px; margin-bottom:14px;">
                        @if(!empty($prod['original_price']))
                            <span style="color:var(--color-text-secondary); text-decoration:line-through; margin-right:6px;">{{ $prod['original_price'] }}</span>
                        @endif
                        <span style="color:#D9B56D; font-weight:600;">{{ $prod['price'] }}</span>
                    </div>
                    <a href="{{ route('products.show', $prod['slug']) }}" style="display:block; text-align:center; background:#D9B56D; color:#3B310F; border-radius:8px; padding:10px; font-size:14px; text-decoration:none; transition:background .15s;" onmouseover="this.style.background='#BE9A53'" onmouseout="this.style.background='#D9B56D'">Ver detalle</a>
                </div>
            </div>
        @empty
            <p style="grid-column:1/-1;text-align:center;color:var(--color-text-secondary);">Aún no hay productos disponibles.</p>
        @endforelse
    </div>

    <div style="text-align:center; margin-top:28px;">
        <a href="{{ route('products.index') }}" style="display:inline-block; border:1px solid var(--color-border-secondary); border-radius:8px; padding:10px 28px; font-size:14px; color:var(--color-text-secondary); text-decoration:none; transition:background .15s;" onmouseover="this.style.background='var(--color-background-secondary)'" onmouseout="this.style.background='transparent'">Ver toda la colección</a>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════════
     ZONA 5 — CTA / CONTACTO
     ════════════════════════════════════════════════════════════════ --}}
<section class="fw-section" style="background:#2E2A26; padding:64px 24px; text-align:center;">
    <h2 class="font-brand" style="font-size:28px; font-weight:700; color:#fff; margin:0 0 10px;">¿Buscas los productos ideales para tu ritual?</h2>
    <p style="color:rgba(255,255,255,0.5); margin:0; font-size:15px;">Escríbenos por WhatsApp y te ayudamos a elegir</p>
    <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-top:24px; flex-wrap:wrap;">
        <a href="{{ \App\Models\ContactPageSetting::whatsappUrl() }}" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:8px; background:#25D366; color:#fff; border-radius:8px; padding:12px 28px; font-size:15px; font-weight:500; text-decoration:none; transition:background .15s;" onmouseover="this.style.background='#1da851'" onmouseout="this.style.background='#25D366'">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.12.553 4.113 1.519 5.845L.053 23.681a.5.5 0 0 0 .611.612l5.836-1.466A11.948 11.948 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75c-1.97 0-3.834-.558-5.42-1.524l-.389-.233-3.462.87.87-3.462-.233-.389A9.709 9.709 0 0 1 2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75z"/></svg>
            WhatsApp
        </a>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:8px; padding:12px 28px; font-size:15px; text-decoration:none; transition:border-color .15s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.3)'">Ver productos</a>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════════
     SCRIPTS
     ════════════════════════════════════════════════════════════════ --}}
<script>
// Reading progress bar
window.addEventListener('scroll', function() {
    var docH = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var scrolled = (window.scrollY / docH) * 100;
    document.getElementById('reading-progress').style.width = Math.min(scrolled, 100) + '%';
});

// Hero stagger animation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hero-anim').forEach(function(el) {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });
});

// Share button (native share API fallback to clipboard)
function sharePost() {
    var url = window.location.href;
    var title = document.querySelector('h1') ? document.querySelector('h1').textContent : '';
    if (navigator.share) {
        navigator.share({ title: title, url: url });
    } else {
        navigator.clipboard.writeText(url).then(function() {
            var btn = document.getElementById('share-text');
            btn.textContent = '¡Copiado!';
            setTimeout(function() { btn.textContent = 'Compartir'; }, 2000);
        });
    }
}

// Copy link (share bar)
function copyLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(function(){
        var span = btn.querySelector('span');
        var orig = span.textContent;
        span.textContent = '¡Copiado!';
        setTimeout(function(){ span.textContent = orig; }, 2000);
    });
}

// Intersection Observer for full-width sections
(function(){
    var sections = document.querySelectorAll('.fw-section');
    if(!sections.length) return;
    var obs = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    sections.forEach(function(s){ obs.observe(s); });
})();
</script>
@endsection
