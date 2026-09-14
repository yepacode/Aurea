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
    @if($howToSchema ?? false)
        {!! $howToSchema !!}
    @endif
@endpush

@section('content')

    @php
        // Maps color => image_path and color => hex (first variant per color wins)
        $variantImagesByColor = [];
        $variantHexByColor = [];
        $firstVariantImage = null;
        foreach ($product->variants as $v) {
            if ($v->color && $v->color_hex && ! isset($variantHexByColor[$v->color])) {
                // Skip the default #000000 that the <input type="color"> submits when untouched,
                // unless the color is actually called "Negro".
                $hexLower = strtolower($v->color_hex);
                $isBlackDefault = $hexLower === '#000000' && stripos($v->color, 'negro') === false;
                if (! $isBlackDefault) {
                    $variantHexByColor[$v->color] = $v->color_hex;
                }
            }
            if ($v->image_path) {
                if ($v->color && ! isset($variantImagesByColor[$v->color])) {
                    $variantImagesByColor[$v->color] = asset('storage/' . $v->image_path);
                }
                if (! $firstVariantImage) {
                    $firstVariantImage = $v->image_path;
                }
            }
        }

        // Build the images list used for display: product images first, else fall back to variant image
        $displayImages = ! empty($product->images) ? $product->images : ($firstVariantImage ? [$firstVariantImage] : []);

        // Stock maps for enabling/disabling buttons
        $stockByColor = $product->variants->where('is_active', true)
            ->groupBy('color')
            ->map(fn ($group) => (int) $group->sum('stock'))
            ->toArray();

        $productHasStock = $product->hasStock();
        $availableStock = $product->availableStock();
    @endphp

    {{-- ============================================================
         FICHA PRINCIPAL: IMAGEN + DATOS
         ============================================================ --}}
    <section style="background:transparent;padding:clamp(32px,5vw,64px) 24px;" x-data="productDetail()" @variant-selection-changed.window="recomputeMax(); stockError = '';">
        <div class="product-layout" style="max-width:1160px;margin:0 auto;background:linear-gradient(160deg,#FEFCF8,#F8F2E8);border:1px solid rgba(217,181,109,.16);border-radius:28px;box-shadow:0 46px 92px -52px rgba(120,92,44,.5),0 6px 22px -12px rgba(0,0,0,.05);padding:clamp(24px,4vw,52px);">

            {{-- ==================== COLUMNA IZQUIERDA: IMAGEN ==================== --}}
            <div style="position:relative;">
                @php $firstImage = $displayImages[0] ?? null; @endphp

                @if($firstImage)
                    <div class="product-zoom-container"
                         style="position:relative;border-radius:18px;overflow:hidden;cursor:zoom-in;min-height:300px;height:480px;background:linear-gradient(155deg,#FBF8F2,#F1EBDF);border:1px solid rgba(217,181,109,.22);"
                         @click="openLightbox()"
                         onmousemove="productZoomMove(event, this)"
                         onmouseleave="productZoomLeave(this)">
                        @foreach($displayImages as $i => $image)
                        <img src="{{ asset('storage/' . $image) }}"
                             alt="{{ $product->name }} - imagen {{ $i + 1 }}"
                             class="product-main-image"
                             data-image-index="{{ $i }}"
                             style="width:100%;height:100%;object-fit:contain;border-radius:16px;
                                    transition:transform .2s ease;transform-origin:center center;
                                    {{ $i > 0 ? 'position:absolute;top:0;left:0;display:none;' : '' }}"
                             x-show="activeImage === {{ $i }}"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100">
                        @endforeach

                        {{-- Variant color image overlay (plain JS controlled) --}}
                        <img id="variant-color-image"
                             src=""
                             alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:contain;border-radius:16px;
                                    transition:transform .2s ease;transform-origin:center center;
                                    position:absolute;top:0;left:0;display:none;z-index:1;">

                    </div>

                    {{-- Thumbnails --}}
                    @if(count($displayImages) > 1)
                    <div style="display:flex;gap:10px;margin-top:12px;overflow-x:auto;padding-bottom:4px;">
                        @foreach($displayImages as $i => $image)
                        <button @click="activeImage = {{ $i }}; hideVariantImage()"
                                style="flex-shrink:0;width:72px;height:72px;border-radius:10px;
                                       overflow:hidden;cursor:pointer;transition:all .2s;
                                       opacity:0.5;"
                                :style="activeImage === {{ $i }} ? 'opacity:1;box-shadow:0 0 0 2px #D9B56D;' : 'opacity:0.5;'">
                            <img src="{{ asset('storage/' . $image) }}" alt=""
                                 style="width:100%;height:100%;object-fit:cover;">
                        </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div style="width:100%;height:400px;border-radius:16px;position:relative;
                                background:linear-gradient(155deg,#FBF8F2,#F1EBDF);
                                display:flex;align-items:center;justify-content:center;
                                border:1px solid rgba(217,181,109,.2);">
                        <div style="text-align:center;padding:24px;">
                            <svg style="width:56px;height:56px;color:#D9B56D;opacity:.6;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                            <p style="font-family:'Playfair Display',serif;font-style:italic;color:#BE9A53;font-size:15px;margin:0;">{{ $product->name }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ==================== COLUMNA DERECHA: DATOS ==================== --}}
            <div>
                {{-- 1. Breadcrumb --}}
                <nav style="font-size:12px;color:#aaa;margin-bottom:20px;">
                    <a href="{{ route('home') }}" style="color:#aaa;text-decoration:none;"
                       onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='#aaa'">Inicio</a>
                    <span style="margin:0 6px;">·</span>
                    <a href="{{ route('products.index') }}" style="color:#aaa;text-decoration:none;"
                       onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='#aaa'">Productos</a>
                    @if($product->category)
                    <span style="margin:0 6px;">·</span>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                       style="color:#aaa;text-decoration:none;"
                       onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='#aaa'">{{ $product->category->name }}</a>
                    @endif
                    <span style="margin:0 6px;">·</span>
                    <span style="color:#666;">{{ $product->name }}</span>
                </nav>

                {{-- 2. Marca + Categoría --}}
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
                    @if($product->brand)
                    <span style="font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#BE9A53;">
                        {{ $product->brand->name }}
                    </span>
                    @endif
                    @if($product->brand && $product->category)
                    <span style="color:#ddd;">·</span>
                    @endif
                    @if($product->category)
                    <span style="font-size:11px;color:#888;letter-spacing:.06em;">
                        {{ $product->category->name }}
                    </span>
                    @endif
                </div>

                @if($isBestSeller ?? false)
                <div style="margin:0 0 12px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#fff;background:linear-gradient(120deg,#E0BE77,#BE9A53);padding:6px 13px;border-radius:999px;box-shadow:0 8px 16px -8px rgba(190,154,83,.8);">★ Más vendido</span>
                </div>
                @endif

                {{-- 3. Nombre --}}
                <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;
                           color:#2E2A26;margin:0 0 16px;">
                    {{ $product->name }}
                </h1>

                {{-- Código de referencia (útil para distribuidores) --}}
                @if($product->internal_code)
                <p style="font-size:12px;color:#aaa;margin:0 0 16px;">
                    Ref. <span style="font-family:'Montserrat',monospace;color:#666;">{{ $product->internal_code }}</span>
                </p>
                @endif

                {{-- 4. Precio --}}
                <div style="display:flex;align-items:baseline;gap:10px;">
                    <span id="product-current-price" style="font-family:'Playfair Display',serif;font-size:30px;font-weight:600;color:#BE9A53;">
                        ${{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                    <span style="font-size:16px;color:#bbb;text-decoration:line-through;">
                        ${{ number_format($product->compare_price, 0, ',', '.') }}
                    </span>
                    @endif
                </div>

                {{-- Favorito (wishlist) --}}
                @php $inWishlist = $inWishlist ?? false; @endphp
                <button type="button"
                        class="ba-fav-btn {{ $inWishlist ? 'is-wished' : '' }}"
                        aria-label="Agregar a favoritos"
                        onclick="toggleWishlist({{ $product->id }}, this, event)"
                        style="display:inline-flex;align-items:center;gap:9px;margin-top:16px;padding:10px 20px;
                               border:1.5px solid #D9B56D;border-radius:9999px;background:#fff;cursor:pointer;
                               font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;letter-spacing:.04em;color:#BE9A53;
                               transition:background .25s ease,box-shadow .25s ease;"
                        onmouseover="this.style.background='#FBF4E6'" onmouseout="this.style.background='#fff'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $inWishlist ? '#C97B6B' : 'none' }}"
                         stroke="#BE9A53" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>Favorito</span>
                </button>

                {{-- Urgencia honesta: stock bajo real --}}
                @if($product->is_low_stock)
                <p style="margin:12px 0 0;font-size:13.5px;font-weight:600;color:#C97B6B;display:flex;align-items:center;gap:7px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#C97B6B;display:inline-block;"></span>
                    ¡Solo quedan {{ $product->low_stock_count }} unidad{{ $product->low_stock_count == 1 ? '' : 'es' }}!
                </p>
                @endif
                {{-- 6. Selector de color --}}
                @if($colores->count() > 0)
                <div style="margin-bottom:20px;margin-top:20px;">
                    <p style="font-size:14px;font-weight:500;color:#2E2A26;margin:0 0 10px;display:flex;align-items:center;gap:8px;">
                        <span>Color: <span id="selected-color-name" style="font-weight:400;color:#666;">{{ $colores->first() }}</span></span>
                        <button type="button" id="clear-color-btn" onclick="clearColor()" style="display:none;background:none;border:none;color:#D9B56D;font-size:12px;font-weight:500;cursor:pointer;text-decoration:underline;padding:0;">Quitar</button>
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($colores as $color)
                            @php
                                $hex = $variantHexByColor[$color] ?? \App\Helpers\ColorHelper::hex($color);
                                $colorOutOfStock = ($stockByColor[$color] ?? 0) <= 0;
                            @endphp
                            <div class="color-btn"
                                 data-color="{{ $color }}"
                                 data-out-of-stock="{{ $colorOutOfStock ? '1' : '0' }}"
                                 style="position:relative;width:28px;height:28px;border-radius:50%;
                                        background-color:{{ $hex }};
                                        border:2px solid rgba(0,0,0,0.1);transition:all .15s;display:inline-block;
                                        {{ $colorOutOfStock ? 'opacity:0.4;cursor:not-allowed;' : 'cursor:pointer;' }}"
                                 title="{{ $color }}{{ $colorOutOfStock ? ' (Agotado)' : '' }}"
                                 @if(!$colorOutOfStock) onclick="selectColor('{{ $color }}')" @endif>
                                @if($colorOutOfStock)
                                    <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#dc2626;font-weight:700;font-size:18px;line-height:1;">×</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- 6b. Selectores genéricos (Tamaño · Aroma · Acabado · Estilo …) --}}
                @if(($genericVariants ?? collect())->isNotEmpty())
                    @foreach($genericVariants as $optionLabel => $variants)
                        @php
                            // Tomar la primera variante del grupo para extraer el option_type
                            $sampleType = $variants->first()->option_type ?? 'other';
                            $isColor = $sampleType === 'color';
                        @endphp
                        <div class="ba-option-group" style="margin-bottom:22px;"
                             data-option-label="{{ $optionLabel }}"
                             data-option-type="{{ $sampleType }}">
                            <p style="font-size:13px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#6B6157;margin:0 0 12px;display:flex;align-items:center;gap:8px;">
                                <span>{{ $optionLabel }}:</span>
                                <span class="ba-option-selected" style="font-weight:400;color:#2E2A26;text-transform:none;letter-spacing:0;font-size:14px;">— Selecciona</span>
                            </p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                @foreach($variants as $v)
                                    @php
                                        $vOut = ($v->stock <= 0);
                                        $vHex = $v->color_hex ?: \App\Helpers\ColorHelper::hex($v->value);
                                    @endphp
                                    <button type="button"
                                            class="ba-opt"
                                            data-variant-id="{{ $v->id }}"
                                            data-variant-value="{{ $v->value }}"
                                            data-variant-price-mod="{{ (float) $v->price_modifier }}"
                                            data-variant-image="{{ $v->image_path ? asset('storage/'.$v->image_path) : '' }}"
                                            data-out-of-stock="{{ $vOut ? '1' : '0' }}"
                                            @disabled($vOut)
                                            title="{{ $v->value }}{{ $vOut ? ' (Agotado)' : '' }}"
                                            style="
                                                @if($isColor)
                                                    position:relative;width:34px;height:34px;border-radius:50%;
                                                    background:{{ $vHex }};
                                                    border:2px solid {{ $vOut ? 'rgba(0,0,0,.1)' : 'rgba(184,169,153,.35)' }};
                                                @else
                                                    padding:9px 18px;border-radius:2px;
                                                    background:#FFFFFF;border:1px solid #D1C7BC;
                                                    font-size:13px;color:#2E2A26;
                                                @endif
                                                transition:all .25s ease;
                                                {{ $vOut ? 'opacity:.4;cursor:not-allowed;text-decoration:'.($isColor ? 'none' : 'line-through').';' : 'cursor:pointer;' }}
                                            ">
                                        @if($isColor)
                                            @if($vOut)
                                                <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#dc2626;font-weight:700;font-size:18px;line-height:1;">×</span>
                                            @endif
                                        @else
                                            {{ $v->value }}
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Descripción corta --}}
                @if($product->description)
                <p style="font-size:14px;color:#666;line-height:1.6;margin-bottom:20px;">
                    {{ $product->description }}
                </p>
                @endif

                {{-- ✨ Bundles activos que incluyen este producto --}}
                @php $productBundles = $product->activeBundles(); @endphp
                @if($productBundles->isNotEmpty())
                    @foreach($productBundles as $activeBundle)
                    <a href="{{ route('bundles.show', $activeBundle->slug) }}"
                       style="display:flex;align-items:center;gap:12px;padding:14px 16px;margin-bottom:14px;
                              background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
                              border:1px solid rgba(217,181,109,.35);border-radius:14px;
                              text-decoration:none;color:inherit;transition:all .3s;"
                       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 26px -10px rgba(190,154,83,.45)'"
                       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                        <span style="font-size:24px;">💫</span>
                        <div style="flex:1;min-width:0;">
                            <p style="margin:0;font-size:13px;color:#6B6157;line-height:1.4;">
                                Este producto está en <strong style="color:#2E2A26;font-family:'Playfair Display',serif;">{{ $activeBundle->name }}</strong>
                                @if($activeBundle->savings > 0)
                                    — ahorra <strong style="color:#C97B6B;">${{ number_format($activeBundle->savings, 0, ',', '.') }}</strong>
                                @endif
                            </p>
                        </div>
                        <span style="font:600 11px/1 'Montserrat',sans-serif;letter-spacing:.14em;text-transform:uppercase;color:#BE9A53;white-space:nowrap;">
                            Ver kit →
                        </span>
                    </a>
                    @endforeach
                @endif

                {{-- Trust badges contextuales (cruelty-free, vegan, origen) --}}
                @if($product->is_cruelty_free || $product->is_vegan || $product->country_origin)
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px;">
                    @if($product->is_cruelty_free)
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:.06em;color:#5C6B54;background:#F0F2EB;border:1px solid #A8B29A;padding:5px 10px;border-radius:999px;">
                        🐰 Cruelty-free
                    </span>
                    @endif
                    @if($product->is_vegan)
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:.06em;color:#5C6B54;background:#F0F2EB;border:1px solid #A8B29A;padding:5px 10px;border-radius:999px;">
                        🌱 Vegano
                    </span>
                    @endif
                    @if($product->country_origin)
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:.06em;color:#BE9A53;background:#FBF4E6;border:1px solid #E8CC92;padding:5px 10px;border-radius:999px;">
                        Origen · {{ $product->country_origin }}
                    </span>
                    @endif
                    @if($product->weight_value && $product->weight_unit)
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:.06em;color:#6B6157;background:#FBF8F2;border:1px solid #D1C7BC;padding:5px 10px;border-radius:999px;">
                        {{ rtrim(rtrim(number_format($product->weight_value, 2, '.', ''), '0'), '.') }} {{ $product->weight_unit }}
                    </span>
                    @endif
                </div>
                @endif

                {{-- Key features bullets — AI-friendly + visual --}}
                @if(! empty($product->key_features) && is_array($product->key_features))
                <div style="margin-bottom:20px;">
                    <p style="font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#BE9A53;margin:0 0 10px;">Características clave</p>
                    <ul style="list-style:none;padding:0;margin:0;font-size:14px;color:#2E2A26;line-height:1.65;">
                        @foreach($product->key_features as $feat)
                            <li style="display:flex;align-items:flex-start;gap:10px;padding:6px 0;">
                                <span style="color:#D9B56D;font-weight:700;flex-shrink:0;">✦</span>
                                <span>{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Recomendado para --}}
                @if($product->suitable_for)
                <div style="margin-bottom:20px;padding:12px 16px;background:#FBF4E6;border-left:3px solid #D9B56D;border-radius:4px;">
                    <p style="font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#BE9A53;margin:0 0 4px;">Recomendado para</p>
                    <p style="font-size:14px;color:#2E2A26;line-height:1.5;margin:0;">{{ $product->suitable_for }}</p>
                </div>
                @endif

                {{-- 8. Botón agregar al carrito --}}
                <div style="margin-top:8px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        {{-- Quantity --}}
                        <div style="display:flex;align-items:center;border:1.5px solid #e5e5e5;border-radius:10px;overflow:hidden;">
                            <button @click="qty > 1 && qty--"
                                    :disabled="qty <= 1"
                                    :style="{
                                        width:'40px',height:'40px',display:'flex',alignItems:'center',
                                        justifyContent:'center',background:'none',border:'none',
                                        cursor: qty <= 1 ? 'not-allowed' : 'pointer',
                                        color: qty <= 1 ? '#d1d5db' : '#888',
                                        fontSize:'18px',
                                    }">
                                −
                            </button>
                            <input type="number" x-model.number="qty" min="1" :max="currentMax"
                                   @change="qty = Math.max(1, Math.min(parseInt(qty) || 1, currentMax))"
                                   inputmode="numeric" aria-label="Cantidad"
                                   style="width:58px;text-align:center;font-size:14px;font-weight:600;color:#2E2A26;border:none;background:none;outline:none;-moz-appearance:textfield;appearance:textfield;">
                            <button @click="increaseQty()"
                                    :disabled="qty >= currentMax"
                                    :title="qty >= currentMax ? ('Máximo disponible: ' + currentMax) : ''"
                                    :style="{
                                        width:'40px',height:'40px',display:'flex',alignItems:'center',
                                        justifyContent:'center',background:'none',border:'none',
                                        cursor: qty >= currentMax ? 'not-allowed' : 'pointer',
                                        color: qty >= currentMax ? '#d1d5db' : '#888',
                                        fontSize:'18px',
                                    }">
                                +
                            </button>
                        </div>

                        {{-- Stock — oculto para el cliente; solo se avisa si intenta pedir más de lo disponible o si está agotado --}}
                        @if($productHasStock)
                        <span x-show="stockError" x-cloak x-transition style="font-size:13px;color:#dc2626;display:flex;align-items:center;gap:5px;font-weight:500;">
                            <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;flex-shrink:0;"></span>
                            <span x-text="stockError"></span>
                        </span>
                        @else
                        <span style="font-size:13px;color:#dc2626;display:flex;align-items:center;gap:5px;">
                            <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
                            Agotado
                        </span>
                        @endif
                    </div>

                    <button @click="addToCart()"
                            :disabled="adding || {{ $productHasStock ? 'false' : 'true' }}"
                            @mouseenter="hoverBtn = true" @mouseleave="hoverBtn = false"
                            :style="{
                                width: '100%',
                                background: adding ? '#BE9A53' : (hoverBtn && {{ $productHasStock ? 'true' : 'false' }} ? '#BE9A53' : '#D9B56D'),
                                color: '#fff',
                                border: 'none',
                                borderRadius: '999px',
                                padding: '16px',
                                fontSize: '13px',
                                fontWeight: '600',
                                letterSpacing: '.14em',
                                textTransform: 'uppercase',
                                boxShadow: (adding || {{ $productHasStock ? 'false' : 'true' }}) ? 'none' : '0 16px 32px -12px rgba(190,154,83,.65)',
                                cursor: adding ? 'wait' : ({{ $productHasStock ? 'true' : 'false' }} ? 'pointer' : 'not-allowed'),
                                transition: 'background .3s, box-shadow .3s',
                                fontFamily: 'inherit',
                                opacity: (adding || {{ $productHasStock ? 'false' : 'true' }}) ? '0.6' : '1',
                            }">
                        <span x-show="!adding && !added">{{ $productHasStock ? 'Agregar al carrito' : 'Agotado' }}</span>
                        <span x-show="adding" x-cloak>Agregando...</span>
                        <span x-show="added" x-cloak>✓ Agregado</span>
                    </button>

                    @unless($productHasStock)
                    {{-- Avísame cuando vuelva --}}
                    <div x-data="{ email:'', sent:false, sending:false, err:'' }" style="margin-top:14px;padding:16px;border-radius:14px;background:#FBF4E6;border:1px solid #E8CC92;">
                        <p style="font-size:13.5px;font-weight:600;color:#2E2A26;margin:0 0 9px;">🔔 ¿Lo quieres? Te avisamos cuando vuelva</p>
                        <form x-show="!sent" @submit.prevent="sending=true; err='';
                              fetch('{{ route('stock.notify') }}', {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({product_id:{{ $product->id }},email:email})})
                              .then(r=>r.json().then(d=>({ok:r.ok,d}))).then(({ok,d})=>{ if(ok){sent=true}else{err=(d.errors&&d.errors.email?d.errors.email[0]:d.message)||'Revisa el correo.'} }).catch(()=>{err='Error, intenta de nuevo.'}).finally(()=>{sending=false})"
                              style="display:flex;gap:8px;flex-wrap:wrap;">
                            <input type="email" x-model="email" required placeholder="tu@correo.com"
                                   aria-label="Correo electrónico para notificar cuando el producto vuelva"
                                   style="flex:1;min-width:170px;border:1px solid #E5DCC9;border-radius:10px;padding:10px 12px;font-size:14px;color:#2E2A26;background:#fff;">
                            <button type="submit" :disabled="sending"
                                    aria-label="Avísame cuando el producto vuelva"
                                    style="border:none;border-radius:10px;padding:10px 20px;background:linear-gradient(120deg,#E0BE77,#BE9A53);color:#3B310F;font-weight:600;font-size:12.5px;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;"
                                    x-text="sending ? 'Enviando...' : 'Avísame'"></button>
                        </form>
                        <p x-show="sent" x-cloak style="font-size:13px;color:#3F8F5B;font-weight:500;margin:0;">✓ ¡Listo! Te avisaremos por correo cuando vuelva. 💛</p>
                        <p x-show="err" x-cloak x-text="err" style="font-size:12.5px;color:#C97B6B;margin:6px 0 0;"></p>
                    </div>
                    @endunless

                </div>

                {{-- 9. Beneficios rápidos --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:24px;">
                    @php
                        $beneficios = [
                            'Envío a toda Colombia',
                            'Productos 100 % originales',
                            'Pago seguro',
                            'Soporte WhatsApp',
                        ];
                    @endphp
                    @foreach($beneficios as $b)
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;">
                        <svg style="width:14px;height:14px;color:#A8B29A;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        {{ $b }}
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================================
         CONTENIDO ENRIQUECIDO — Modo de uso + Ingredientes (AI-ready)
         Visible para humanos, indexable por LLMs (texto plano + Schema HowTo)
         ============================================================ --}}
    @if($product->how_to_use || $product->ingredients)
    <section style="background:#FBF8F2;padding:64px 24px;">
        <div style="max-width:960px;margin:0 auto;">
            <div class="pdp-howto-grid" style="display:grid;grid-template-columns:{{ ($product->how_to_use && $product->ingredients) ? '1fr 1fr' : '1fr' }};gap:48px;">

                {{-- Modo de uso --}}
                @if($product->how_to_use)
                <article>
                    <p style="font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#BE9A53;margin:0 0 14px;">Modo de uso</p>
                    <h2 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:500;color:#2E2A26;margin:0 0 24px;">Cómo aplicarlo</h2>
                    @php
                        // Acepta saltos reales, '\n' literales, o numeración '1. ', '2. '
                        $rawHow = preg_replace('/\s*(?:\\\\n|\r\n|\r|\n)\s*/u', "\n", $product->how_to_use);
                        $rawHow = preg_replace('/(?<=[.;])\s+(?=\d+\.\s)/u', "\n", $rawHow);
                        $steps = collect(preg_split('/\n+/', $rawHow))
                            ->map(fn ($l) => trim(preg_replace('/^\d+\.\s*/', '', $l)))
                            ->filter()
                            ->values()
                            ->all();
                    @endphp
                    @if(count($steps) > 1)
                        <ol style="list-style:none;counter-reset:step;padding:0;margin:0;">
                            @foreach($steps as $step)
                            <li style="counter-increment:step;display:flex;align-items:flex-start;gap:18px;padding:12px 0;border-bottom:1px solid rgba(184,169,153,.18);">
                                <span style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:#FBF4E6;border:1px solid #E8CC92;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;color:#BE9A53;font-weight:600;">{{ $loop->iteration }}</span>
                                <span style="font-size:15px;line-height:1.7;color:#2E2A26;padding-top:5px;">{{ $step }}</span>
                            </li>
                            @endforeach
                        </ol>
                    @else
                        <p style="font-size:15px;line-height:1.75;color:#2E2A26;margin:0;">{{ $product->how_to_use }}</p>
                    @endif
                </article>
                @endif

                {{-- Ingredientes --}}
                @if($product->ingredients)
                <article>
                    <p style="font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#BE9A53;margin:0 0 14px;">Composición</p>
                    <h2 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:500;color:#2E2A26;margin:0 0 24px;">Ingredientes</h2>
                    <div style="font-size:13px;line-height:1.85;color:#6B6157;font-family:'Montserrat',sans-serif;background:#FFFFFF;padding:24px;border-radius:8px;border:1px solid rgba(184,169,153,.2);">
                        {{ $product->ingredients }}
                    </div>
                    <p style="font-size:11px;color:#9CA3AF;margin-top:10px;font-style:italic;">
                        Composición declarada por el fabricante. Para alergias específicas, consulta con tu profesional.
                    </p>
                </article>
                @endif

            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         PRODUCTOS RELACIONADOS — "También te podría gustar 💛"
         Se coloca ANTES de las reseñas para retener al cliente cuando
         termina de leer contenido enriquecido y baja hacia el footer.
         ============================================================ --}}
    @if(($relatedProducts ?? collect())->count() > 0)
    <style>
        .relx-wrap{max-width:1200px;margin:0 auto;padding:clamp(48px,7vw,80px) 24px 32px;}
        .relx-title{display:flex;align-items:center;justify-content:center;gap:16px;margin:0 0 44px;text-align:center;}
        .relx-title__flower{color:#D9B56D;font-size:20px;line-height:1;opacity:.85;flex-shrink:0;
            display:inline-flex;align-items:center;}
        .relx-title__flower svg{width:26px;height:26px;}
        .relx-title h2{font-family:'Playfair Display',serif;font-size:clamp(24px,3.2vw,34px);font-weight:600;
            color:#2E2A26;margin:0;line-height:1.2;}
        .relx-title h2 .heart{color:#D9B56D;font-size:.85em;margin-left:6px;}
        .relx-title__kicker{display:block;font-size:11px;font-weight:600;letter-spacing:.28em;
            text-transform:uppercase;color:#BE9A53;margin:0 0 8px;text-align:center;}
        .relx-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(16px,2vw,26px);}
        @media(max-width:900px){
            .relx-grid{display:flex;grid-template-columns:none;gap:14px;overflow-x:auto;
                scroll-snap-type:x mandatory;padding:4px 4px 20px;
                margin:0 -16px;padding-left:16px;padding-right:16px;
                scrollbar-width:thin;scrollbar-color:#D9B56D transparent;}
            .relx-grid::-webkit-scrollbar{height:6px;}
            .relx-grid::-webkit-scrollbar-thumb{background:#D9B56D;border-radius:4px;}
            .relx-grid > *{scroll-snap-align:start;flex:0 0 62%;min-width:220px;max-width:260px;}
        }
        .relx-card{display:block;text-decoration:none;color:inherit;transition:transform .4s cubic-bezier(.2,.7,.3,1);}
        .relx-card__img{position:relative;aspect-ratio:4/5;border-radius:16px;overflow:hidden;margin-bottom:14px;
            display:flex;align-items:center;justify-content:center;padding:14px;
            background:linear-gradient(155deg,#FBF8F2,#F3ECDF);
            border:1px solid rgba(217,181,109,.22);
            transition:box-shadow .5s cubic-bezier(.2,.7,.3,1),border-color .4s,transform .5s cubic-bezier(.2,.7,.3,1);}
        .relx-card:hover .relx-card__img{border-color:rgba(217,181,109,.55);
            box-shadow:0 26px 50px -22px rgba(190,154,83,.45);transform:translateY(-6px);}
        .relx-card__img img{position:relative;max-width:100%;max-height:100%;width:auto;height:auto;
            object-fit:contain;transition:transform 1.3s cubic-bezier(.2,.7,.3,1), filter .5s ease;}
        .relx-card:hover .relx-card__img img{transform:scale(1.07);}
        @media(hover:hover){
            .relx-card__img img{filter:saturate(.85) brightness(1.02) contrast(.97);}
            .relx-card:hover .relx-card__img img{filter:none;}
        }
        .relx-card__badge{position:absolute;top:10px;left:10px;background:rgba(255,255,255,.92);
            color:#BE9A53;font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;
            border-radius:9999px;padding:4px 10px;backdrop-filter:blur(6px);
            border:1px solid rgba(217,181,109,.4);}
        .relx-card__brand{font-size:10px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;
            color:#BE9A53;margin:0 0 4px;}
        .relx-card__name{font-family:'Playfair Display',serif;font-size:15px;font-weight:600;color:#2E2A26;
            margin:0 0 8px;line-height:1.3;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;
            -webkit-box-orient:vertical;min-height:2.6em;}
        .relx-card__price{font-family:'Playfair Display',serif;font-size:15.5px;font-weight:600;color:#BE9A53;}
        .relx-card__compare{font-size:12px;color:#B8A999;text-decoration:line-through;margin-left:6px;}
        .relx-card__cta{display:inline-flex;align-items:center;gap:6px;margin-top:10px;
            font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;letter-spacing:.18em;
            text-transform:uppercase;color:#2E2A26;padding:8px 14px;border:1px solid #E5DCC9;
            border-radius:9999px;background:transparent;transition:all .3s ease;}
        .relx-card:hover .relx-card__cta{background:#2E2A26;color:#F7F3ED;border-color:#2E2A26;}
        .relx-card__cta svg{width:12px;height:12px;transition:transform .3s ease;}
        .relx-card:hover .relx-card__cta svg{transform:translateX(3px);}
    </style>
    <section style="background:transparent;">
        <div class="relx-wrap">
            <span class="relx-title__kicker">También te podría gustar</span>
            <div class="relx-title">
                <span class="relx-title__flower" aria-hidden="true">
                    {{-- Adorno floral izquierdo --}}
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2c1.3 1.6 1.3 3.9 0 5.5C10.7 5.9 10.7 3.6 12 2Zm-6.1 3.8c1.9.4 3.3 2.2 3.1 4.1C7 9.5 5.6 7.7 5.9 5.8Zm12.2 0c-.3 1.9-1.7 3.7-3.6 4.1-.1-1.9 1.3-3.7 3.6-4.1Zm-9.6 9.4C6.6 14 5 15.3 4.7 17.2c1.9.3 3.7-1 4.2-2.9.6.4 1.4.5 2.1.2-.3-2-1.9-3.5-3.9-3.8-.2 1.7 1 3.2 2.5 3.7-.4.4-1 .6-1.6.6-.6 0-1.2-.2-1.7-.5.4-.6.7-1.2 1-1.9-.2 0-.5.1-.8.2Zm7.3-.2c1.5-.5 2.7-2 2.5-3.7-2 .3-3.6 1.8-3.9 3.8.7.3 1.5.2 2.1-.2.5 1.9 2.3 3.2 4.2 2.9-.3-1.9-1.9-3.2-3.6-3.4-.3-.1-.6-.2-.8-.2.3.6.6 1.3 1 1.9-.5.3-1.1.5-1.7.5-.6 0-1.1-.2-1.6-.6-.1.2 0 .5.1.9-.8-.3-1.5-.9-1.9-1.6-.4.7-1.1 1.3-1.9 1.6.1-.4.2-.7.1-.9-.5.4-1 .6-1.6.6-.6 0-1.2-.2-1.7-.5-.4.2-.7.5-1 .8.4.6.9 1 1.5 1.3-1.1.9-2.2 1.8-3.1 2.9.6.4 1.2.8 1.9 1 .3-1.6 1.4-3 2.9-3.6-.1 1.8 1 3.5 2.7 4.2 1.7-.7 2.8-2.4 2.7-4.2 1.5.6 2.6 2 2.9 3.6.7-.2 1.3-.6 1.9-1-.9-1.1-2-2-3.1-2.9.6-.3 1.1-.7 1.5-1.3-.3-.3-.6-.6-1-.8Z" opacity=".85"/>
                        <circle cx="12" cy="12" r="1.4"/>
                    </svg>
                </span>
                <h2>También te podría gustar<span class="heart">💛</span></h2>
                <span class="relx-title__flower" aria-hidden="true">
                    {{-- Adorno floral derecho --}}
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2c1.3 1.6 1.3 3.9 0 5.5C10.7 5.9 10.7 3.6 12 2Zm-6.1 3.8c1.9.4 3.3 2.2 3.1 4.1C7 9.5 5.6 7.7 5.9 5.8Zm12.2 0c-.3 1.9-1.7 3.7-3.6 4.1-.1-1.9 1.3-3.7 3.6-4.1Zm-9.6 9.4C6.6 14 5 15.3 4.7 17.2c1.9.3 3.7-1 4.2-2.9.6.4 1.4.5 2.1.2-.3-2-1.9-3.5-3.9-3.8-.2 1.7 1 3.2 2.5 3.7-.4.4-1 .6-1.6.6-.6 0-1.2-.2-1.7-.5.4-.6.7-1.2 1-1.9-.2 0-.5.1-.8.2Zm7.3-.2c1.5-.5 2.7-2 2.5-3.7-2 .3-3.6 1.8-3.9 3.8.7.3 1.5.2 2.1-.2.5 1.9 2.3 3.2 4.2 2.9-.3-1.9-1.9-3.2-3.6-3.4-.3-.1-.6-.2-.8-.2.3.6.6 1.3 1 1.9-.5.3-1.1.5-1.7.5-.6 0-1.1-.2-1.6-.6-.1.2 0 .5.1.9-.8-.3-1.5-.9-1.9-1.6-.4.7-1.1 1.3-1.9 1.6.1-.4.2-.7.1-.9-.5.4-1 .6-1.6.6-.6 0-1.2-.2-1.7-.5-.4.2-.7.5-1 .8.4.6.9 1 1.5 1.3-1.1.9-2.2 1.8-3.1 2.9.6.4 1.2.8 1.9 1 .3-1.6 1.4-3 2.9-3.6-.1 1.8 1 3.5 2.7 4.2 1.7-.7 2.8-2.4 2.7-4.2 1.5.6 2.6 2 2.9 3.6.7-.2 1.3-.6 1.9-1-.9-1.1-2-2-3.1-2.9.6-.3 1.1-.7 1.5-1.3-.3-.3-.6-.6-1-.8Z" opacity=".85"/>
                        <circle cx="12" cy="12" r="1.4"/>
                    </svg>
                </span>
            </div>

            <div class="relx-grid">
                @foreach($relatedProducts as $rel)
                @php $rImg = $rel->images[0] ?? null; @endphp
                <a href="{{ route('products.show', $rel->slug) }}" class="relx-card">
                    <div class="relx-card__img">
                        @if($rel->compare_price && $rel->compare_price > $rel->price)
                            <span class="relx-card__badge">Oferta</span>
                        @endif
                        @if($rImg)
                            <img src="{{ asset('storage/'.$rImg) }}" alt="{{ $rel->name }}" loading="lazy">
                        @endif
                    </div>
                    @if($rel->brand)
                        <p class="relx-card__brand">{{ $rel->brand->name }}</p>
                    @endif
                    <h4 class="relx-card__name">{{ $rel->name }}</h4>
                    <div>
                        <span class="relx-card__price">${{ number_format($rel->price, 0, ',', '.') }}</span>
                        @if($rel->compare_price && $rel->compare_price > $rel->price)
                            <span class="relx-card__compare">${{ number_format($rel->compare_price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <span class="relx-card__cta">
                        Ver
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-6-6m6 6-6 6"/>
                        </svg>
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         RESEÑAS ⭐
         ============================================================ --}}
    <style>
        .rev-wrap{max-width:900px;margin:0 auto;padding:8px 20px 8px;}
        .rev-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,30px);font-weight:700;color:#2E2A26;margin:0 0 6px;}
        .rev-title span{color:#BE9A53;font-size:.7em;}
        .rev-avg{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:22px;}
        .rev-stars,.rev-stars-lg{letter-spacing:1px;white-space:nowrap;}
        .rev-stars-lg{font-size:24px;}
        .rev-avg-num{font-weight:700;color:#2E2A26;}
        .rev-avg-cnt{font-size:13px;color:#6B6157;}
        .rev-ok{background:#EAF4EA;border:1px solid #BcdCBc;color:#2f6d34;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:14px;}
        .rev-err{background:#FCEFE6;border:1px solid #E7C0B0;color:#A65A4D;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:13px;}
        .rev-item{border-bottom:1px solid #EFE7D8;padding:16px 0;}
        .rev-item-head{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .rev-item-head strong{color:#2E2A26;font-size:14px;}
        .rev-item-head time{color:#9CA3AF;font-size:12px;}
        .rev-item-title{font-weight:600;color:#2E2A26;margin:6px 0 2px;font-size:14px;}
        .rev-item-body{color:#5A5248;font-size:14px;line-height:1.6;margin:2px 0 0;}
        .rev-empty{color:#8A8073;font-style:italic;padding:8px 0 18px;}
        .rev-form{background:linear-gradient(180deg,#FCFAF5,#F6EFE1);border:1px solid rgba(217,181,109,.3);border-radius:18px;padding:22px;margin-top:26px;}
        .rev-form h3{font-family:'Playfair Display',serif;font-size:1.2rem;color:#2E2A26;margin:0 0 14px;}
        .rev-form input,.rev-form textarea{width:100%;border:1px solid #E5DCC9;background:#FFFDF9;border-radius:10px;padding:10px 12px;font-size:14px;color:#2E2A26;margin-bottom:12px;font-family:'Montserrat',sans-serif;}
        .rev-form input:focus,.rev-form textarea:focus{outline:none;border-color:#D9B56D;box-shadow:0 0 0 3px rgba(217,181,109,.18);}
        .rev-field-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
        .rev-field-row input{flex:1;min-width:200px;}
        .rev-picker{display:inline-flex;gap:2px;margin-bottom:12px;}
        .rev-picker button{background:none;border:none;cursor:pointer;font-size:26px;line-height:1;color:#E5DCC9;padding:0 1px;transition:color .15s;}
        .rev-picker button.on{color:#D9B56D;}
        .rev-submit{background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);color:#3B310F;border:none;border-radius:9999px;padding:12px 26px;font-weight:600;font-size:.85rem;cursor:pointer;font-family:'Montserrat',sans-serif;transition:filter .2s;}
        .rev-submit:hover{filter:brightness(1.04);}
    </style>
    @php $revs = $product->approvedReviews; $avg = $product->average_rating; $cnt = $product->reviews_count; @endphp
    <section id="resenas" class="rev-wrap">
        <h2 class="rev-title">Reseñas @if($cnt)<span>({{ $cnt }})</span>@endif</h2>

        @if($cnt)
        <div class="rev-avg">
            <span class="rev-stars-lg">@for($i=1;$i<=5;$i++)<span style="color:{{ $i <= round($avg) ? '#D9B56D' : '#E5DCC9' }}">★</span>@endfor</span>
            <span class="rev-avg-num">{{ number_format($avg, 1) }} / 5</span>
            <span class="rev-avg-cnt">basado en {{ $cnt }} {{ $cnt === 1 ? 'reseña' : 'reseñas' }}</span>
        </div>
        @endif

        @if(session('review_success'))<div class="rev-ok">{{ session('review_success') }}</div>@endif
        @if($errors->any())<div class="rev-err">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif

        @forelse($revs as $r)
        <div class="rev-item">
            <div class="rev-item-head">
                <span class="rev-stars">@for($i=1;$i<=5;$i++)<span style="color:{{ $i <= $r->rating ? '#D9B56D' : '#E5DCC9' }}">★</span>@endfor</span>
                <strong>{{ $r->author_name }}</strong>
                <time>{{ $r->created_at->format('d/m/Y') }}</time>
            </div>
            @if($r->title)<p class="rev-item-title">{{ $r->title }}</p>@endif
            @if($r->comment)<p class="rev-item-body">{{ $r->comment }}</p>@endif
            @if($r->image_path)
            <a href="{{ asset('storage/'.$r->image_path) }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:10px;">
                <img src="{{ asset('storage/'.$r->image_path) }}" alt="Foto de la reseña de {{ $r->author_name }}" loading="lazy" style="width:88px;height:88px;object-fit:cover;border-radius:10px;border:1px solid #E5DCC9;">
            </a>
            @endif
        </div>
        @empty
        <p class="rev-empty">Aún no hay reseñas. ¡Sé la primera persona en opinar! 🌸</p>
        @endforelse

        <form method="POST" action="{{ route('reviews.store', $product->slug) }}" enctype="multipart/form-data" class="rev-form" x-data="{ rating: {{ (int) old('rating', 5) }} }">
            @csrf
            <h3>Deja tu reseña</h3>
            <div class="rev-field-row">
                <input type="text" name="author_name" placeholder="Tu nombre *" required maxlength="80" value="{{ old('author_name') }}">
                <div class="rev-picker" role="radiogroup" aria-label="Calificación">
                    <template x-for="s in 5" :key="s">
                        <button type="button" @click="rating = s" :class="s <= rating ? 'on' : ''" x-text="'★'"></button>
                    </template>
                    <input type="hidden" name="rating" :value="rating">
                </div>
            </div>
            <input type="text" name="title" placeholder="Título (opcional)" maxlength="120" value="{{ old('title') }}">
            <textarea name="comment" rows="3" maxlength="1500" placeholder="Cuéntanos tu experiencia con el producto...">{{ old('comment') }}</textarea>
            <label style="display:block;font-size:13px;color:#6B6157;margin:-2px 0 6px;">📷 Agrega una foto (opcional)</label>
            <input type="file" name="image" accept="image/*" style="padding:8px 12px;">
            <button type="submit" class="rev-submit">Enviar reseña</button>
        </form>
    </section>

    {{-- ============================================================
         LIGHTBOX — componente propio + evento global (abre el zoom desde cualquier scope)
         ============================================================ --}}
    <style>.ba-lightbox-ov{position:fixed;inset:0;z-index:80;background:rgba(0,0,0,.92);display:flex;align-items:center;justify-content:center;}</style>
    @if(! empty($displayImages))
    <div x-data="{ open:false, activeImage:0 }" x-cloak x-show="open"
         class="ba-lightbox-ov"
         @open-lightbox.window="open=true; activeImage=($event.detail && $event.detail.index) || 0"
         @keydown.escape.window="open=false"
         @click.self="open=false">
        <button @click="open=false"
                style="position:absolute;top:16px;right:16px;background:none;border:none;
                       color:rgba(255,255,255,0.7);cursor:pointer;z-index:10;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
            <svg style="width:32px;height:32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>

        @if(count($displayImages) > 1)
        <button @click="activeImage = (activeImage - 1 + {{ count($displayImages) }}) % {{ count($displayImages) }}"
                style="position:absolute;left:16px;background:none;border:none;
                       color:rgba(255,255,255,0.7);cursor:pointer;z-index:10;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
            <svg style="width:40px;height:40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </button>
        <button @click="activeImage = (activeImage + 1) % {{ count($displayImages) }}"
                style="position:absolute;right:16px;background:none;border:none;
                       color:rgba(255,255,255,0.7);cursor:pointer;z-index:10;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
            <svg style="width:40px;height:40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
        </button>
        @endif

        @foreach($displayImages as $i => $image)
        <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}"
             style="max-height:85vh;max-width:90vw;object-fit:contain;"
             x-show="activeImage === {{ $i }}">
        @endforeach
    </div>
    @endif

@endsection

@push('scripts')
<script>
/* ── Variant images map (color → image URL) ── */
window.variantImagesByColor = @json($variantImagesByColor);

/* ── Lista de variantes activas con stock para calcular disponibilidad por color ── */
@php
    $variantStockData = $product->variants->where('is_active', true)->map(fn ($v) => [
        'color' => $v->color,
        'stock' => (int) $v->stock,
    ])->values();
@endphp
window.variantStockData = @json($variantStockData);
window.currentSelection = { color: null };

/**
 * Recalcula qué colores siguen disponibles según el stock total de cada uno.
 */
function refreshVariantAvailability() {
    var data = window.variantStockData || [];

    document.querySelectorAll('.color-btn').forEach(function (btn) {
        var color = btn.dataset.color;
        var stock = data
            .filter(function (v) { return v.color === color; })
            .reduce(function (sum, v) { return sum + (v.stock || 0); }, 0);

        var outOfStock = stock <= 0;
        btn.dataset.outOfStock = outOfStock ? '1' : '0';
        btn.style.opacity = outOfStock ? '0.4' : '1';
        btn.style.cursor = outOfStock ? 'not-allowed' : 'pointer';
        btn.title = color + (outOfStock ? ' (Agotado)' : '');

        var xMark = btn.querySelector('.color-out-x');
        if (outOfStock && !xMark) {
            var span = document.createElement('span');
            span.className = 'color-out-x';
            span.textContent = '×';
            span.style.cssText = 'position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#dc2626;font-weight:700;font-size:18px;line-height:1;';
            btn.appendChild(span);
        } else if (!outOfStock && xMark) {
            xMark.remove();
        }

        if (outOfStock) {
            btn.onclick = null;
        } else {
            btn.onclick = (function (c) { return function () { selectColor(c); }; })(color);
        }
    });
}

/* ── Image zoom on hover ── */
function productZoomMove(e, container) {
    var imgs = container.querySelectorAll('img');
    var rect = container.getBoundingClientRect();
    var x = ((e.clientX - rect.left) / rect.width) * 100;
    var y = ((e.clientY - rect.top) / rect.height) * 100;
    imgs.forEach(function(img) {
        if (img.offsetParent !== null || img.style.display !== 'none') {
            img.style.transformOrigin = x + '% ' + y + '%';
            img.style.transform = 'scale(2)';
        }
    });
}

function productZoomLeave(container) {
    var imgs = container.querySelectorAll('img');
    imgs.forEach(function(img) {
        img.style.transform = 'scale(1)';
        img.style.transformOrigin = 'center center';
    });
}

function hideVariantImage() {
    var overlay = document.getElementById('variant-color-image');
    if (overlay) {
        overlay.style.display = 'none';
        overlay.src = '';
    }
}

/* ── Helper para mostrar/ocultar el botón "Quitar" de color ── */
function updateClearButtons() {
    var sel = window.currentSelection;
    var clearColorBtn = document.getElementById('clear-color-btn');
    if (clearColorBtn) {
        clearColorBtn.style.display = sel.color ? 'inline-block' : 'none';
    }
}

/* ── Color selector ── */
function selectColor(color) {
    // Toggle: si el color ya está seleccionado, lo deseleccionamos.
    if (window.currentSelection.color === color) {
        clearColor();
        return;
    }

    document.querySelectorAll('.color-btn').forEach(function(b) {
        b.style.borderColor = 'transparent';
        b.style.boxShadow = 'none';
    });
    var btn = document.querySelector('[data-color="' + color + '"]');
    if (btn) {
        btn.style.borderColor = '#D9B56D';
        btn.style.boxShadow = '0 0 0 2px rgba(55,138,221,0.3)';
    }
    var label = document.getElementById('selected-color-name');
    if (label) label.textContent = color;

    // Swap main image to the variant image for this color (if any)
    var overlay = document.getElementById('variant-color-image');
    if (overlay) {
        var url = window.variantImagesByColor[color];
        if (url) {
            overlay.src = url;
            overlay.style.display = 'block';
        } else {
            overlay.src = '';
            overlay.style.display = 'none';
        }
    }

    window.currentSelection.color = color;
    refreshVariantAvailability();
    updateClearButtons();
    window.dispatchEvent(new CustomEvent('variant-selection-changed'));
}

/* ── Limpiar color ── */
function clearColor() {
    document.querySelectorAll('.color-btn').forEach(function (b) {
        b.style.borderColor = 'transparent';
        b.style.boxShadow = 'none';
    });
    var label = document.getElementById('selected-color-name');
    if (label) label.textContent = '— Selecciona un color';

    var overlay = document.getElementById('variant-color-image');
    if (overlay) { overlay.src = ''; overlay.style.display = 'none'; }

    window.currentSelection.color = null;
    refreshVariantAvailability();
    updateClearButtons();
    window.dispatchEvent(new CustomEvent('variant-selection-changed'));
}

/* ── Auto-select first in-stock color ── */
document.addEventListener('DOMContentLoaded', function() {
    refreshVariantAvailability();

    var buttons = document.querySelectorAll('.color-btn');
    for (var i = 0; i < buttons.length; i++) {
        if (buttons[i].dataset.outOfStock !== '1') {
            selectColor(buttons[i].dataset.color);
            break;
        }
    }

    updateClearButtons();
});

/* ──────────────────────────────────────────────────────────
   Selector de variantes genéricas (Tamaño / Aroma / Acabado…)
   El cliente puede tener N grupos de opciones. Por simplicidad,
   la última opción clickeada define el variant_id que se envía
   al carrito. Si quieres combinaciones (color + tamaño),
   se necesita un modelo de "combinations" aparte.
   ────────────────────────────────────────────────────────── */
window.selectedGenericVariantId = null;
window.selectedGenericVariants = {}; // { 'Tamaño': {value, modifier}, ... }
window.baseProductPrice = {{ (float) $product->price }};

(function () {
    function fmtPrice(n) {
        return '$' + Number(n).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function updatePriceDisplay() {
        var totalMod = 0;
        Object.values(window.selectedGenericVariants).forEach(function (g) {
            totalMod += g.modifier || 0;
        });
        var newPrice = window.baseProductPrice + totalMod;
        var el = document.getElementById('product-current-price');
        if (el) el.textContent = fmtPrice(newPrice);
    }
    function selectOption(btn) {
        if (btn.dataset.outOfStock === '1') return;

        var group = btn.closest('.ba-option-group');
        if (!group) return;

        // Limpia hermanos
        group.querySelectorAll('.ba-opt').forEach(function (b) {
            if (b.dataset.optionType === 'color' || group.dataset.optionType === 'color') {
                b.style.boxShadow = '';
                b.style.borderColor = 'rgba(184,169,153,.35)';
                b.style.borderWidth = '2px';
                b.style.transform = '';
            } else {
                b.style.background = '#FFFFFF';
                b.style.borderColor = '#D1C7BC';
                b.style.color = '#2E2A26';
                b.style.fontWeight = '400';
            }
        });

        // Marca seleccionado
        if (group.dataset.optionType === 'color') {
            btn.style.boxShadow = '0 0 0 2px #FFFFFF, 0 0 0 4px #D9B56D';
            btn.style.transform = 'scale(1.08)';
        } else {
            btn.style.background = '#2E2A26';
            btn.style.borderColor = '#2E2A26';
            btn.style.color = '#FFFFFF';
            btn.style.fontWeight = '500';
        }

        var label = group.querySelector('.ba-option-selected');
        if (label) label.textContent = btn.dataset.variantValue;

        // Guarda selección
        var optionLabel = group.dataset.optionLabel;
        window.selectedGenericVariants[optionLabel] = {
            value:    btn.dataset.variantValue,
            modifier: parseFloat(btn.dataset.variantPriceMod) || 0,
            id:       btn.dataset.variantId,
        };
        window.selectedGenericVariantId = parseInt(btn.dataset.variantId, 10);

        // Si la variante tiene su propia imagen, la mostramos sobre el visor
        // usando el overlay #variant-color-image que ya existe en el blade.
        if (btn.dataset.variantImage) {
            var overlay = document.getElementById('variant-color-image');
            if (overlay) {
                overlay.src = btn.dataset.variantImage;
                overlay.style.display = 'block';
            }
        } else {
            var overlay2 = document.getElementById('variant-color-image');
            if (overlay2 && overlay2.dataset.controlledBy !== 'color') {
                overlay2.style.display = 'none';
            }
        }

        updatePriceDisplay();
        window.dispatchEvent(new CustomEvent('variant-selection-changed'));
    }

    // Event delegation
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.ba-opt');
        if (btn) selectOption(btn);
    });
})();

/* ── Alpine component ── */
function productDetail() {
    return {
        activeImage: 0,
        lightboxOpen: false,
        qty: 1,
        currentMax: 10,
        adding: false,
        added: false,
        hoverBtn: false,
        stockError: '',

        init() {
            // Limpiar el mensaje de error cuando el usuario cambia la cantidad
            // (porque pudo haber cambiado el stock disponible).
            this.$watch('qty', () => { this.stockError = ''; });
            // Calculo inicial del maximo segun la variante por defecto.
            this.recomputeMax();
        },

        /**
         * Calcula el stock disponible para la combinacion actualmente seleccionada
         * (color + graduacion) y lo guarda en currentMax para que Alpine reactivamente
         * actualice los bindings :disabled/:title del boton +.
         * Si qty actual ya excede el nuevo max, la bajamos al max para no quedar invalida.
         */
        recomputeMax() {
            var data = window.variantStockData || [];
            var sel = window.currentSelection || {};

            // Sin variantes: usa el stock del producto (sin tope artificial — mayoreo).
            if (data.length === 0) {
                this.currentMax = Math.max({{ (int) ($product->stock ?? 0) }}, 0);
            } else {
                var stock = data.reduce(function (sum, v) {
                    if (sel.color && v.color !== sel.color) return sum;
                    return sum + (v.stock || 0);
                }, 0);
                // El máximo es el stock disponible real (permite pedidos mayoristas).
                this.currentMax = Math.max(stock, 0);
            }

            // Clampear qty al nuevo maximo (siempre >= 1).
            if (this.qty > this.currentMax) {
                this.qty = Math.max(this.currentMax, 1);
            }
        },

        /**
         * Incrementa qty pero no permite pasar del maximo disponible.
         * Si el cliente clickea cuando ya esta en el maximo, mostramos
         * un hint inline en lugar de subir el contador.
         */
        increaseQty() {
            // Recalculamos por si las dudas (la selecccion pudo cambiar
            // entre eventos).
            this.recomputeMax();
            if (this.qty < this.currentMax) {
                this.qty++;
                this.stockError = '';
            } else {
                this.stockError = this.currentMax > 0
                    ? 'Máximo disponible: ' + this.currentMax + ' unidad(es).'
                    : 'Sin stock disponible.';
            }
        },

        openLightbox() {
            window.dispatchEvent(new CustomEvent('open-lightbox', { detail: { index: this.activeImage } }));
        },

        async addToCart() {
            if (this.adding) return;
            this.adding = true;
            this.added = false;

            // Buscar la variante seleccionada (por color o por opción genérica)
            var selectedColor = document.getElementById('selected-color-name');
            var colorName = selectedColor ? selectedColor.textContent : null;

            var variantId = null;
            @if($product->variants->count())
            @php
                $variantData = $product->variants->where('is_active', true)->map(fn ($v) => [
                    'id' => $v->id, 'color' => $v->color,
                ])->values();
            @endphp
            var variants = @json($variantData);

            // Match por color si el cliente eligió uno
            for (var i = 0; i < variants.length; i++) {
                if (colorName && variants[i].color === colorName) {
                    variantId = variants[i].id;
                    break;
                }
            }

            // Si el cliente eligió una variante genérica (Tamaño / Aroma / Acabado…),
            // ésa tiene prioridad.
            if (window.selectedGenericVariantId) {
                variantId = window.selectedGenericVariantId;
            }
            @endif

            try {
                var res = await fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        variant_id: variantId,
                        qty: this.qty,
                    }),
                });

                var data = await res.json();

                if (res.ok) {
                    this.added = true;
                    this.stockError = '';

                    // ── Analytics: add_to_cart / AddToCart ──
                    try {
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'add_to_cart', {
                                currency: 'COP',
                                value: {{ (float) $product->price }} * this.qty,
                                items: [{
                                    item_id: '{{ $product->id }}',
                                    item_name: @js($product->name),
                                    item_category: @js(optional($product->category)->name),
                                    price: {{ (float) $product->price }},
                                    quantity: this.qty
                                }]
                            });
                        }
                        if (typeof fbq !== 'undefined') {
                            fbq('track', 'AddToCart', {
                                content_ids: ['{{ $product->id }}'],
                                content_name: @js($product->name),
                                content_type: 'product',
                                value: {{ (float) $product->price }} * this.qty,
                                currency: 'COP'
                            });
                        }
                    } catch (_) { /* nunca bloquear la UX por métricas */ }

                    var badge = document.getElementById('cart-badge');
                    var count = document.getElementById('cart-count');
                    if (badge && count) {
                        badge.classList.remove('hidden');
                        count.textContent = data.cart_count;
                    }
                    // Abre el mini-cart drawer del navbar. Enviamos el evento y, como cinturón
                    // de seguridad por si Alpine no engancha el listener a tiempo, también
                    // abrimos la instancia directamente vía _x_dataStack.
                    window.dispatchEvent(new CustomEvent('open-cart-drawer', { detail: data }));
                    try {
                        var drawerEl = document.querySelector('[x-data^="cartDrawer"]');
                        if (drawerEl && drawerEl._x_dataStack && drawerEl._x_dataStack[0]) {
                            drawerEl._x_dataStack[0].open(data);
                        }
                    } catch (_) { /* silencio: el evento ya voló */ }
                    var self = this;
                    setTimeout(function() { self.added = false; }, 2000);
                } else {
                    // Backend rechazo (p. ej. stock insuficiente). Mostramos
                    // el mensaje inline en lugar de un alert intrusivo.
                    this.stockError = data.message || 'No se pudo agregar al carrito.';
                }
            } catch (e) {
                console.error(e);
                this.stockError = 'Error de conexión. Intenta de nuevo.';
            } finally {
                this.adding = false;
            }
        },
    };
}
</script>

<style>
.product-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: start;
}
@media (max-width: 900px) {
    .related-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 768px) {
    .product-layout {
        grid-template-columns: 1fr;
        gap: 32px;
    }
}
@media (max-width: 480px) {
    .related-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .pdp-howto-grid { grid-template-columns: 1fr !important; gap: 32px !important; }
}
</style>

{{-- ── Analytics: view_item / ViewContent (al abrir la ficha) ── --}}
<script>
    (function () {
        try {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'view_item', {
                    currency: 'COP',
                    value: {{ (float) $product->price }},
                    items: [{
                        item_id: '{{ $product->id }}',
                        item_name: @js($product->name),
                        item_category: @js(optional($product->category)->name),
                        price: {{ (float) $product->price }},
                        quantity: 1
                    }]
                });
            }
            if (typeof fbq !== 'undefined') {
                fbq('track', 'ViewContent', {
                    content_ids: ['{{ $product->id }}'],
                    content_name: @js($product->name),
                    content_type: 'product',
                    value: {{ (float) $product->price }},
                    currency: 'COP'
                });
            }
        } catch (_) { /* silencio */ }
    })();
</script>
@endpush
