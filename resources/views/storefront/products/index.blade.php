@extends('layouts.app')

{{-- Si se filtra por una categoría con SEO propio, sus overrides mandan sobre los de la página. --}}
@php $cs = $categorySeo ?? null; @endphp
@section('title', $cs['title'] ?? $seoSettings->meta_title ?? 'Catálogo | Belleza Áurea — Insumos y cosmética de belleza')
@section('meta_description', $cs['description'] ?? $seoSettings->meta_description ?? 'Catálogo completo Belleza Áurea: esmaltes, uñas, piel, maquillaje, pestañas, herramientas y más. Precios mayoristas y envío a toda Colombia.')
@section('robots', $cs['robots'] ?? 'index, follow')
@section('canonical', $cs['canonical'] ?? $seoSettings->canonical_url ?? route('products.index'))
@if(!empty($cs['keywords']))@section('keywords', $cs['keywords'])@endif
@section('og_type', $cs['og_type'] ?? 'website')
@section('og_title', $cs['og_title'] ?? $seoSettings->og_title ?? $seoSettings->meta_title ?? 'Catálogo | Belleza Áurea')
@section('og_description', $cs['og_description'] ?? $seoSettings->og_description ?? $seoSettings->meta_description ?? 'Insumos profesionales y cosmética de marca — uñas, piel, maquillaje y más, en un solo lugar.')
@section('twitter_card', $cs['twitter_card'] ?? 'summary_large_image')
@section('twitter_title', $cs['twitter_title'] ?? $seoSettings->twitter_title ?? $seoSettings->meta_title ?? 'Catálogo | Belleza Áurea')
@section('twitter_description', $cs['twitter_description'] ?? $seoSettings->twitter_description ?? $seoSettings->meta_description ?? 'Insumos profesionales y cosmética de marca — uñas, piel, maquillaje y más, en un solo lugar.')
@section('og_image', $cs['og_image'] ?? ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', $cs['twitter_image'] ?? ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@if(!empty($cs['custom_schema']))@section('custom_schema', $cs['custom_schema'])@endif

@push('schema')
    {!! $breadcrumbs !!}
    {{-- ItemList de productos del catálogo (keys con chr(64) para no chocar con directivas Blade) --}}
    @php
        $K_CTX = chr(64).'context'; $K_TYP = chr(64).'type';
        $catItemLd = $products->isNotEmpty()
            ? json_encode([
                $K_CTX => 'https://schema.org',
                $K_TYP => 'ItemList',
                'name' => 'Catálogo Belleza Áurea',
                'numberOfItems' => $products->count(),
                'itemListElement' => $products->take(24)->values()->map(fn ($p, $i) => [
                    $K_TYP => 'ListItem',
                    'position' => $i + 1,
                    'url' => route('products.show', ['slug' => $p->slug]),
                    'name' => $p->name,
                ])->all(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : null;
    @endphp
    @if($catItemLd)
    <script type="application/ld+json">{!! $catItemLd !!}</script>
    @endif
@endpush

@section('content')

<style>
/* ═══ Catálogo — aire elegante botánico ═══ */
.catx-head{padding:clamp(40px,6vh,76px) 24px clamp(16px,2.5vh,30px);text-align:center;position:relative;}
.catx-bc{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#B8A999;margin-bottom:16px;}
.catx-bc a{color:#B8A999;text-decoration:none;transition:color .25s;}
.catx-bc a:hover{color:#BE9A53;}
.catx-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:600;letter-spacing:.24em;
    text-transform:uppercase;color:#BE9A53;margin-bottom:14px;}
.catx-eyebrow::before,.catx-eyebrow::after{content:"";width:26px;height:1px;background:#D9B56D;}
.catx-title{font-family:'Playfair Display',serif;font-weight:600;color:#2E2A26;
    font-size:clamp(30px,4.2vw,50px);line-height:1.04;letter-spacing:-.01em;margin:0 0 10px;}
.catx-sub{font-size:14.5px;color:#6B6157;max-width:520px;margin:0 auto;line-height:1.65;}

/* Barra de filtros */
.catx-filters{background:rgba(251,248,242,.86);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);
    border-top:1px solid rgba(217,181,109,.18);border-bottom:1px solid rgba(217,181,109,.18);
    position:sticky;top:0;z-index:20;}
.filter-select{border:1px solid rgba(184,169,153,.4);border-radius:999px;padding:10px 34px 10px 16px;font-size:13px;
    color:#2E2A26;background-color:rgba(255,255,255,.7);
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23BE9A53' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 14px center;cursor:pointer;font-family:inherit;outline:none;
    appearance:none;-webkit-appearance:none;max-width:210px;transition:border-color .2s,background-color .2s;}
.filter-select:hover,.filter-select:focus{border-color:#D9B56D;background-color:#fff;}
.active-chip{display:inline-flex;align-items:center;gap:6px;background:#F1E9DA;color:#A9853C;
    font-size:12px;padding:4px 11px;border-radius:999px;white-space:nowrap;}
.active-chip a{color:#BE9A53;text-decoration:none;font-size:14px;line-height:1;display:inline-flex;
    align-items:center;justify-content:center;width:14px;height:14px;}
.active-chip a:hover{color:#2E2A26;}
@media(max-width:640px){.filter-select{flex:1 1 calc(50% - 5px);max-width:none;}}

/* Grid + tarjetas */
.catx-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(18px,2.2vw,32px);
    max-width:1280px;margin:0 auto;}
@media(max-width:1000px){.catx-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:720px){.catx-grid{grid-template-columns:repeat(2,1fr);gap:16px;}}
.pcard{position:relative;display:block;text-decoration:none;color:inherit;isolation:isolate;padding-bottom:6px;}
.pcard__img{position:relative;aspect-ratio:4/5;border-radius:16px;overflow:hidden;margin-bottom:16px;
    border:1px solid rgba(217,181,109,.2);background:linear-gradient(155deg,#FBF8F2,#F3ECDF);
    display:flex;align-items:center;justify-content:center;padding:12px;
    transition:box-shadow .5s cubic-bezier(.2,.7,.3,1),border-color .5s ease,transform .5s cubic-bezier(.2,.7,.3,1);}
.pcard:hover .pcard__img{border-color:rgba(217,181,109,.55);box-shadow:0 30px 56px -24px rgba(190,154,83,.45);transform:translateY(-6px);}
.pcard__img img{position:relative;max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;
    transition:transform 1.4s cubic-bezier(.2,.7,.3,1), filter .5s ease;}
.pcard:hover .pcard__img img{transform:scale(1.07);}
/* Calma el color chillón de las fotos de proveedor — solo en escritorio (con hover);
   en móvil/táctil se ven a todo color. Foto real al pasar el mouse. */
@media (hover:hover){
    .pcard__img img{filter:saturate(.78) brightness(1.03) contrast(.95);}
    .pcard:hover .pcard__img img{filter:none;}
}
.pcard__ph{position:absolute;inset:0;display:grid;place-items:center;text-align:center;padding:16px;
    font-family:'Playfair Display',serif;font-style:italic;color:#BE9A53;font-size:14px;}
.pcard__badge{position:absolute;top:12px;right:12px;z-index:3;background:rgba(46,42,38,.5);color:#fff;
    font-size:10px;letter-spacing:.08em;padding:4px 11px;border-radius:999px;
    -webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);}
.pcard__flag{position:absolute;top:12px;left:12px;z-index:4;display:inline-flex;align-items:center;gap:5px;
    font-size:10px;font-weight:700;letter-spacing:.03em;text-transform:uppercase;padding:5px 10px;border-radius:999px;}
.pcard__flag--best{background:linear-gradient(120deg,#E0BE77,#BE9A53);color:#fff;box-shadow:0 8px 16px -8px rgba(190,154,83,.85);}
.pcard__flag--low{background:rgba(201,123,107,.96);color:#fff;box-shadow:0 8px 16px -8px rgba(201,123,107,.7);}
.pcard-cell{display:flex;flex-direction:column;}
.pcard-cell .pcard{flex:1;}
/* Corazón favoritos — esquina inferior derecha de la imagen (evita badge y flag) */
.pcard__heart{position:absolute;bottom:12px;right:12px;z-index:5;width:34px;height:34px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;padding:0;
    background:rgba(255,255,255,.8);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);
    box-shadow:0 6px 14px -6px rgba(120,92,44,.5);transition:transform .2s ease,background .2s ease;}
.pcard__heart:hover{transform:scale(1.1);background:#fff;}
.pcard__heart svg{width:18px;height:18px;display:block;}
.pcard__heart.is-wished{background:#fff;}
.pcard__add{margin-top:6px;width:100%;border:1.5px solid #D9B56D;background:#fff;color:#BE9A53;
    font:600 11.5px/1 'Montserrat',sans-serif;letter-spacing:.08em;text-transform:uppercase;
    padding:12px 14px;border-radius:999px;cursor:pointer;transition:all .25s ease;}
.pcard__add:hover{background:linear-gradient(120deg,#E0BE77,#BE9A53);color:#fff;border-color:transparent;
    box-shadow:0 12px 24px -12px rgba(190,154,83,.8);}
.pcard__add:disabled{opacity:.5;cursor:not-allowed;}
/* Quick-view modal */
.qv-ov{position:fixed;inset:0;z-index:80;background:rgba(46,42,38,.55);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);
    display:flex;align-items:center;justify-content:center;padding:20px;}
.qv-box{background:#fff;border-radius:22px;max-width:430px;width:100%;padding:26px;position:relative;
    box-shadow:0 40px 90px -30px rgba(120,92,44,.55);}
.qv-close{position:absolute;top:14px;right:16px;background:none;border:none;font-size:22px;color:#9CA3AF;cursor:pointer;line-height:1;}
.qv-opt{border:1.5px solid #E5DCC9;background:#FBF8F2;border-radius:10px;padding:8px 12px;font-size:13px;color:#2E2A26;cursor:pointer;
    display:inline-flex;align-items:center;gap:7px;transition:all .2s ease;}
.qv-opt.on{border-color:#D9B56D;background:#FBF4E6;box-shadow:0 0 0 3px rgba(217,181,109,.18);}
.qv-opt:disabled{opacity:.4;cursor:not-allowed;text-decoration:line-through;}
.qv-swatch{width:15px;height:15px;border-radius:50%;border:1px solid rgba(0,0,0,.1);}
.qv-add{width:100%;margin-top:18px;border:none;border-radius:999px;padding:14px;color:#fff;cursor:pointer;
    font:600 12.5px/1 'Montserrat',sans-serif;letter-spacing:.1em;text-transform:uppercase;
    background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);box-shadow:0 16px 32px -12px rgba(190,154,83,.7);}
.qv-add:disabled{opacity:.55;cursor:not-allowed;}
.qv-toast{position:fixed;bottom:26px;left:50%;transform:translateX(-50%);z-index:90;background:#2E2A26;color:#fff;
    padding:13px 22px;border-radius:999px;font-size:13.5px;font-weight:500;box-shadow:0 16px 32px -12px rgba(0,0,0,.4);}
.pcard::after{content:"";position:absolute;right:-4px;bottom:0;width:34px;height:44px;z-index:0;
    background:url('{{ asset('img/patterns/leaf-corner.svg') }}') no-repeat center/contain;
    opacity:.45;transform:rotate(6deg);pointer-events:none;transition:transform .5s,opacity .5s;}
.pcard:hover::after{opacity:.75;transform:rotate(0) translateY(-3px);}
.pcard__brand{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#BE9A53;font-weight:600;margin:0 0 4px;}
.pcard__name{font-family:'Playfair Display',serif;font-size:16px;font-weight:600;color:#2E2A26;line-height:1.25;
    margin:0 0 9px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.pcard__dots{display:flex;gap:5px;flex-wrap:wrap;align-items:center;margin-bottom:9px;}
.pcard__dot{width:14px;height:14px;border-radius:50%;border:1px solid rgba(184,169,153,.45);
    box-shadow:inset 0 0 0 1px rgba(255,255,255,.5);}
.pcard__more{font-size:11px;color:#B8A999;}
.pcard__price{font-family:'Playfair Display',serif;font-size:17px;font-weight:600;color:#BE9A53;}
.pcard__compare{font-size:13px;color:#B8A999;text-decoration:line-through;margin-left:8px;}
.pcard__cta{display:inline-flex;align-items:center;gap:8px;margin-top:12px;font-family:'Montserrat',sans-serif;
    font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#2E2A26;
    border-bottom:1px solid rgba(217,181,109,.5);padding-bottom:3px;transition:color .3s,gap .3s;}
.pcard:hover .pcard__cta{color:#BE9A53;gap:12px;}
</style>

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <section class="catx-head">
        <nav class="catx-bc" aria-label="Migas de pan">
            <a href="{{ route('home') }}">Inicio</a>
            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            <span style="color:#8E7E70;">Catálogo</span>
        </nav>
        <div class="catx-eyebrow">Distribuidora oficial · Colombia</div>
        <h1 class="catx-title">{{ $lentesPage->catalog_title ?? 'Nuestro catálogo' }}</h1>
        <p class="catx-sub">{{ $lentesPage->catalog_subtitle ?? 'Insumos profesionales y cosmética de marca — uñas, piel, maquillaje y más, en un solo lugar.' }}</p>
    </section>

    {{-- ============================================================
         FILTROS · BARRA ÚNICA MINIMALISTA
         ============================================================ --}}
    @php
        $activeFilterCount = ($qFiltro ? 1 : 0) + ($catFiltro ? 1 : 0) + ($brandFiltro ? 1 : 0) + ($priceFiltro ? 1 : 0);
        $currentCat = $catFiltro ? $categoriasFiltro->firstWhere('slug', $catFiltro) : null;
        $currentBrand = $brandFiltro && $marcasFiltro->count() ? $marcasFiltro->firstWhere('slug', $brandFiltro) : null;
    @endphp
    <section class="catx-filters">
        <div style="max-width:1200px;margin:0 auto;padding:16px 24px;">

            {{-- Barra única: buscador + selects --}}
            <form method="GET" action="{{ route('products.index') }}" id="catalogFilters"
                  style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">

                {{-- Buscador --}}
                <div style="position:relative;flex:1;min-width:220px;max-width:380px;">
                    <svg style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#999;pointer-events:none;"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input type="search" name="q" value="{{ $qFiltro }}"
                           placeholder="Buscar productos…" autocomplete="off"
                           style="width:100%;border:1px solid #e5e5e5;border-radius:10px;
                                  padding:10px 14px 10px 40px;font-size:14px;background:#fafafa;
                                  color:#2E2A26;font-family:inherit;outline:none;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#D9B56D';this.style.background='#fff'"
                           onblur="this.style.borderColor='#e5e5e5';this.style.background='#fafafa'">
                </div>

                {{-- Categoría --}}
                <select name="category" onchange="document.getElementById('catalogFilters').submit()"
                        aria-label="Categoría" class="filter-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categoriasFiltro as $cat)
                        <option value="{{ $cat->slug }}" {{ $catFiltro === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                    @endforeach
                </select>

                {{-- Marca (solo si hay) --}}
                @if($marcasFiltro->count() > 0)
                <select name="brand" onchange="document.getElementById('catalogFilters').submit()"
                        aria-label="Marca" class="filter-select">
                    <option value="">Todas las marcas</option>
                    @foreach($marcasFiltro as $marca)
                        <option value="{{ $marca->slug }}" {{ $brandFiltro === $marca->slug ? 'selected' : '' }}>
                            {{ $marca->name }}
                        </option>
                    @endforeach
                </select>
                @endif

                {{-- Precio --}}
                <select name="price" onchange="document.getElementById('catalogFilters').submit()"
                        aria-label="Rango de precio" class="filter-select">
                    <option value="">Cualquier precio</option>
                    @foreach($rangosPrecios as $val => $label)
                        <option value="{{ $val }}" {{ $priceFiltro === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Spacer --}}
                <div style="flex:1;min-width:0;"></div>

                {{-- Ordenar --}}
                <select name="sort" onchange="document.getElementById('catalogFilters').submit()"
                        aria-label="Ordenar resultados" class="filter-select">
                    @foreach($opcionesOrden as $val => $label)
                        <option value="{{ $val }}" {{ $sortFiltro === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Botón aplicar (solo para search; los selects auto-envían) --}}
                <noscript>
                    <button type="submit"
                            style="background:#2E2A26;color:#fff;border:none;border-radius:10px;
                                   padding:10px 18px;font-size:14px;cursor:pointer;font-family:inherit;">
                        Aplicar
                    </button>
                </noscript>
            </form>

            {{-- Chips de filtros activos + conteo + limpiar --}}
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin-top:12px;min-height:24px;">
                <span style="font-size:13px;color:#888;">
                    {{ $products->count() }} producto{{ $products->count() !== 1 ? 's' : '' }}
                </span>

                @if($activeFilterCount > 0)
                    <span style="color:#ddd;">·</span>

                    @if($qFiltro)
                    <span class="active-chip">
                        “{{ $qFiltro }}”
                        <a href="{{ route('products.index', request()->except('q')) }}" aria-label="Quitar búsqueda">&times;</a>
                    </span>
                    @endif
                    @if($currentCat)
                    <span class="active-chip">
                        {{ $currentCat->name }}
                        <a href="{{ route('products.index', request()->except('category')) }}" aria-label="Quitar categoría">&times;</a>
                    </span>
                    @endif
                    @if($currentBrand)
                    <span class="active-chip">
                        {{ $currentBrand->name }}
                        <a href="{{ route('products.index', request()->except('brand')) }}" aria-label="Quitar marca">&times;</a>
                    </span>
                    @endif
                    @if($priceFiltro && isset($rangosPrecios[$priceFiltro]))
                    <span class="active-chip">
                        {{ $rangosPrecios[$priceFiltro] }}
                        <a href="{{ route('products.index', request()->except('price')) }}" aria-label="Quitar precio">&times;</a>
                    </span>
                    @endif

                    <a href="{{ route('products.index') }}"
                       style="font-size:12px;color:#D9B56D;text-decoration:none;margin-left:4px;"
                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        Limpiar todo
                    </a>
                @endif
            </div>
        </div>

    </section>

    {{-- ============================================================
         GRID DE PRODUCTOS
         ============================================================ --}}
    <section style="padding:clamp(28px,4vw,52px) 24px clamp(48px,6vw,84px);" x-data="catalogQuick()">
        <div style="max-width:1280px;margin:0 auto;">

            {{-- Quick-view modal --}}
            <div class="qv-ov" x-show="quick.open" x-cloak style="display:none;" @click.self="closeQuick()" x-transition.opacity>
                <div class="qv-box">
                    <button class="qv-close" @click="closeQuick()" aria-label="Cerrar">&times;</button>
                    <div style="display:flex;gap:14px;align-items:center;margin-bottom:16px;">
                        <template x-if="quick.product.image">
                            <img :src="quick.product.image" :alt="quick.product.name" style="width:78px;height:78px;object-fit:cover;border-radius:12px;border:1px solid #E5DCC9;">
                        </template>
                        <div>
                            <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#2E2A26;margin:0 0 4px;" x-text="quick.product.name"></h3>
                            <p style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#BE9A53;margin:0;" x-text="'$' + fmt(quick.currentPrice)"></p>
                        </div>
                    </div>

                    <template x-if="quick.product.variants && quick.product.variants.length">
                        <div style="margin-bottom:4px;">
                            <p style="font-size:12px;font-weight:600;color:#6B6157;margin:0 0 8px;">Elige una opción:</p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                <template x-for="v in quick.product.variants" :key="v.id">
                                    <button class="qv-opt" :class="quick.selected && quick.selected.id === v.id ? 'on' : ''"
                                            :disabled="v.stock <= 0" @click="selectVariant(v)">
                                        <template x-if="v.hex"><span class="qv-swatch" :style="'background:' + v.hex"></span></template>
                                        <span x-text="v.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Cantidad --}}
                    <div style="display:flex;align-items:center;gap:12px;margin-top:16px;">
                        <span style="font-size:12px;font-weight:600;color:#6B6157;">Cantidad</span>
                        <div style="display:flex;align-items:center;border:1.5px solid #e5e5e5;border-radius:10px;overflow:hidden;">
                            <button @click="quick.qty > 1 && quick.qty--" style="width:38px;height:38px;background:none;border:none;cursor:pointer;color:#888;font-size:18px;">−</button>
                            <input type="number" min="1" x-model.number="quick.qty"
                                   @change="quick.qty = Math.max(1, Math.min(parseInt(quick.qty)||1, quick.max || 9999))"
                                   style="width:54px;text-align:center;font-size:14px;font-weight:600;color:#2E2A26;border:none;outline:none;-moz-appearance:textfield;appearance:textfield;">
                            <button @click="quick.qty < (quick.max || 9999) && quick.qty++" style="width:38px;height:38px;background:none;border:none;cursor:pointer;color:#888;font-size:18px;">+</button>
                        </div>
                    </div>

                    <p x-show="quick.msg" x-cloak x-text="quick.msg" style="color:#C97B6B;font-size:13px;margin:12px 0 0;"></p>

                    <button class="qv-add" @click="addFromModal()"
                            :disabled="quick.adding || (quick.product.variants && quick.product.variants.length && !quick.selected)"
                            x-text="quick.adding ? 'Agregando...' : 'Agregar al carrito'"></button>
                </div>
            </div>

            {{-- Toast --}}
            <div class="qv-toast" x-show="toast" x-cloak x-transition style="display:none;" x-text="toast"></div>

            @if($products->count())
            <div class="catx-grid">
                @foreach($products as $product)
                    @php
                        $variantsInStock = $product->variants->where('is_active', true)->where('stock', '>', 0);
                        $coloresVariantes = $variantsInStock->filter(fn($v) => $v->color)->unique('color')->values();
                        $firstImage = $product->images[0] ?? null;
                        $activeVars = $product->variants->where('is_active', true);
                        $quickData = [
                            'id'    => $product->id,
                            'name'  => $product->name,
                            'price' => (float) $product->price,
                            'image' => $firstImage ? asset('storage/'.$firstImage) : null,
                            'variants' => $activeVars->map(fn($v) => [
                                'id'        => $v->id,
                                'label'     => $v->color ?: ($v->value ?: 'Opción'),
                                'hex'       => $v->color_hex,
                                'stock'     => (int) $v->stock,
                                'price_mod' => (float) $v->price_modifier,
                            ])->values()->all(),
                        ];
                    @endphp
                    <div class="pcard-cell">
                    <a href="{{ route('products.show', $product->slug) }}" class="pcard">
                        <div class="pcard__img">
                            @if($firstImage)
                                <img src="{{ asset('storage/'.$firstImage) }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <span class="pcard__ph">{{ $product->name }}</span>
                            @endif
                            @if($product->category)
                                <span class="pcard__badge">{{ $product->category->name }}</span>
                            @endif
                            @if(($bestSellerIds ?? collect())->contains($product->id))
                                <span class="pcard__flag pcard__flag--best">★ Más vendido</span>
                            @elseif($product->is_low_stock)
                                <span class="pcard__flag pcard__flag--low">¡Últimas {{ $product->low_stock_count }}!</span>
                            @endif

                            @php $isWished = ($wishlistIds ?? collect())->contains($product->id); @endphp
                            <button type="button"
                                    class="pcard__heart {{ $isWished ? 'is-wished' : '' }}"
                                    aria-label="Agregar a favoritos"
                                    onclick="toggleWishlist({{ $product->id }}, this, event)">
                                <svg viewBox="0 0 24 24" fill="{{ $isWished ? '#C97B6B' : 'none' }}"
                                     stroke="#BE9A53" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </div>

                        @if($product->brand)
                            <p class="pcard__brand">{{ $product->brand->name }}</p>
                        @endif
                        <h3 class="pcard__name">{{ $product->name }}</h3>

                        @if($coloresVariantes->count() > 0)
                        <div class="pcard__dots">
                            @foreach($coloresVariantes->take(6) as $variant)
                                @php
                                    $hex = $variant->color_hex;
                                    $isBlackDefault = $hex && strtolower($hex) === '#000000' && stripos($variant->color, 'negro') === false;
                                    if (! $hex || $isBlackDefault) { $hex = \App\Helpers\ColorHelper::hex($variant->color); }
                                @endphp
                                <span class="pcard__dot" style="background:{{ $hex }};" title="{{ $variant->color }}"></span>
                            @endforeach
                            @if($coloresVariantes->count() > 6)
                                <span class="pcard__more">+{{ $coloresVariantes->count() - 6 }}</span>
                            @endif
                        </div>
                        @endif

                        <div>
                            <span class="pcard__price">${{ number_format($product->price, 0, ',', '.') }}</span>
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="pcard__compare">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <span class="pcard__cta">Ver detalle
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </a>
                    <button type="button" class="pcard__add" @click="cardAction(@js($quickData))">
                        {{ $activeVars->count() ? 'Elegir opciones' : '＋ Agregar' }}
                    </button>
                    </div>
                @endforeach
            </div>

            {{ $products->links('pagination.aurea') }}
            @else
                {{-- Estado vacío --}}
                <div style="text-align:center;padding:64px 24px;">
                    <svg style="width:48px;height:48px;color:#ccc;margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <p style="font-size:16px;color:#888;margin-bottom:16px;">
                        No hay productos con esos filtros.
                    </p>
                    <a href="{{ route('products.index') }}" style="color:#D9B56D;font-size:14px;text-decoration:none;"
                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        Ver todos los productos
                    </a>
                </div>
            @endif

        </div>
    </section>

    {{-- ============================================================
         SECCIÓN TOALLITAS (deprecada - se quita en Belleza Áurea)
         ============================================================ --}}
    @if(false)
    <section style="background:#fff;padding:48px 24px;">
        <div style="max-width:1200px;margin:0 auto;">

            {{-- Separador --}}
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:32px;">
                <div style="flex:1;height:1px;background:#e5e5e5;"></div>
                <span style="font-size:13px;color:#aaa;white-space:nowrap;">Complementa tu compra</span>
                <div style="flex:1;height:1px;background:#e5e5e5;"></div>
            </div>

            <div class="catalog-grid" style="display:grid;gap:20px;">
                @foreach([] as $product)
                    @php $firstImage = $product->images[0] ?? null; @endphp

                    <div style="background:#fff;border-radius:12px;overflow:hidden;
                                border:0.5px solid rgba(0,0,0,0.08);cursor:pointer;
                                transition:transform .2s ease,box-shadow .2s ease;"
                         onclick="location.href='{{ route('products.show', $product->slug) }}'"
                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">

                        <div style="height:220px;position:relative;overflow:hidden;">
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage) }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div style="width:100%;height:100%;
                                    background:linear-gradient(135deg,#5D4037,#8D6E63);
                                    display:flex;align-items:center;justify-content:center;">
                                    <div style="text-align:center;">
                                        <svg style="width:48px;height:48px;color:rgba(255,255,255,0.15);margin:0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                                        </svg>
                                        <p style="margin-top:8px;font-size:11px;color:rgba(255,255,255,0.25);">{{ $product->name }}</p>
                                    </div>
                                </div>
                            @endif

                            <div style="position:absolute;top:10px;right:10px;
                                background:rgba(0,0,0,0.45);color:#fff;font-size:10px;
                                padding:3px 8px;border-radius:20px;backdrop-filter:blur(4px);">
                                Toallitas
                            </div>
                        </div>

                        <div style="padding:16px 18px 20px;">
                            <h3 style="font-size:16px;font-weight:600;color:#2E2A26;margin:0 0 10px;">
                                {{ $product->name }}
                            </h3>

                            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:14px;">
                                <span style="font-size:20px;font-weight:700;color:#2E2A26;">
                                    ${{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                @if($product->compare_price)
                                <span style="font-size:13px;color:#bbb;text-decoration:line-through;">
                                    ${{ number_format($product->compare_price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>

                            <a href="{{ route('products.show', $product->slug) }}"
                               onclick="event.stopPropagation()"
                               style="display:block;text-align:center;background:#2E2A26;
                                      color:#fff;border-radius:8px;padding:10px;font-size:14px;
                                      font-weight:500;text-decoration:none;transition:background .2s;"
                               onmouseover="this.style.background='#D9B56D'"
                               onmouseout="this.style.background='#2E2A26'">
                                Ver detalle →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection

@push('scripts')
<script>
function setFilter(key, value) {
    var params = new URLSearchParams(window.location.search);
    var validKeys = ['category', 'brand', 'price', 'sort'];
    if (validKeys.indexOf(key) === -1) return;

    if (value) {
        params.set(key, value);
    } else {
        params.delete(key);
    }
    // Resetear sort si pasa a 'relevant' (default)
    if (key === 'sort' && value === 'relevant') {
        params.delete('sort');
    }
    var qs = params.toString();
    window.location.href = '{{ route("products.index") }}' + (qs ? '?' + qs : '');
}
</script>

<style>
.catalog-grid {
    grid-template-columns: repeat(3, 1fr);
}
@media (max-width: 1024px) {
    .catalog-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    .catalog-grid {
        grid-template-columns: 1fr;
    }
    /* Mobile: show toggle bar, collapse filters */
    .filters-mobile-toggle {
        display: flex !important;
    }
    .filters-hidden-mobile {
        display: none !important;
    }
    .filters-content {
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    .filters-count-desktop {
        display: none !important;
    }
    .filters-clear-mobile {
        display: block !important;
    }
}
/* Hide scrollbar on active filter pills */
.filters-mobile-toggle div::-webkit-scrollbar {
    display: none;
}
</style>

<script>
function catalogQuick() {
    return {
        quick: { open: false, product: { variants: [] }, selected: null, qty: 1, max: 9999, currentPrice: 0, adding: false, msg: '' },
        toast: '',
        _t: null,

        fmt(n) { return Number(n).toLocaleString('es-CO'); },

        cardAction(data) {
            if (!data.variants || data.variants.length === 0) {
                this.quickAdd(data.id, null, 1, false);
            } else {
                this.openQuick(data);
            }
        },

        openQuick(data) {
            this.quick.product = data;
            this.quick.selected = null;
            this.quick.qty = 1;
            this.quick.max = 9999;
            this.quick.currentPrice = data.price;
            this.quick.msg = '';
            this.quick.adding = false;
            this.quick.open = true;
            document.body.style.overflow = 'hidden';
        },

        selectVariant(v) {
            if (v.stock <= 0) return;
            this.quick.selected = v;
            this.quick.max = v.stock;
            this.quick.qty = Math.min(this.quick.qty, Math.max(v.stock, 1));
            this.quick.currentPrice = this.quick.product.price + (v.price_mod || 0);
            this.quick.msg = '';
        },

        closeQuick() {
            this.quick.open = false;
            document.body.style.overflow = '';
        },

        addFromModal() {
            if (this.quick.product.variants && this.quick.product.variants.length && !this.quick.selected) {
                this.quick.msg = 'Por favor elige una opción.';
                return;
            }
            this.quickAdd(
                this.quick.product.id,
                this.quick.selected ? this.quick.selected.id : null,
                this.quick.qty,
                true
            );
        },

        async quickAdd(productId, variantId, qty, fromModal) {
            this.quick.adding = true;
            try {
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ product_id: productId, variant_id: variantId, qty: qty }),
                });
                const data = await res.json();
                if (!res.ok) {
                    const m = data.message || 'No se pudo agregar al carrito.';
                    if (fromModal) { this.quick.msg = m; } else { this.showToast(m); }
                    this.quick.adding = false;
                    return;
                }
                window.dispatchEvent(new CustomEvent('open-cart-drawer', { detail: data }));
                this.closeQuick();
                this.showToast('Agregado al carrito ✓');
            } catch (e) {
                if (fromModal) { this.quick.msg = 'Error al agregar. Intenta de nuevo.'; }
                else { this.showToast('Error al agregar.'); }
            }
            this.quick.adding = false;
        },

        showToast(msg) {
            this.toast = msg;
            clearTimeout(this._t);
            this._t = setTimeout(() => { this.toast = ''; }, 2600);
        },
    };
}
</script>
@endpush
