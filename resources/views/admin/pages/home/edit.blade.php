@extends('layouts.admin')

@section('title', 'Editar secciones del inicio')
@section('page_title', 'Secciones del inicio')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
        openSection: 'categories',
        toggle(s) { this.openSection = this.openSection === s ? null : s; }
     }">

    <p class="text-sm text-gray-500 mb-6">Edita el texto, iconos y contenido de las secciones del inicio. Los cambios se reflejan inmediatamente en la tienda.</p>

    <form method="POST" action="{{ route('admin.pages.home.update') }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        {{-- ═══════════ CATEGORÍAS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('categories')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Tarjetas de categoría</h3>
                <svg :class="openSection === 'categories' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'categories'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta superior</label>
                    <input type="text" name="categories_label" value="{{ $page->categories_label }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="categories_title" value="{{ $page->categories_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="categories_subtitle" value="{{ $page->categories_subtitle }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-1">Tarjetas (3 categorías)</h4>
                    <p class="text-xs text-gray-500 mb-3">Estas son las tarjetas que se muestran en la página principal. El botón "Ver modelos" usa el campo <strong>Tipo a filtrar</strong> para llevar al catálogo filtrado (<code>/productos?type=...</code>).</p>
                    @php $cards = $page->category_cards ?? []; @endphp
                    @for($i = 0; $i < 3; $i++)
                    @php $card = $cards[$i] ?? ['name' => '', 'link_param' => '', 'description' => '', 'icon_svg' => '', 'image' => '']; @endphp
                    <div class="bg-gray-50 rounded-lg p-4 mb-3" x-data="{ showSvg{{ $i }}: false }">
                        {{-- Persistimos el path de imagen actual; si suben una nueva, el controller lo reemplaza --}}
                        <input type="hidden" name="category_cards[{{ $i }}][image]" value="{{ $card['image'] ?? '' }}">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nombre</label>
                                <input type="text" name="category_cards[{{ $i }}][name]" value="{{ $card['name'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo a filtrar (<code>?type=</code>)</label>
                                <input type="text" name="category_cards[{{ $i }}][link_param]" value="{{ $card['link_param'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="miopia">
                                <p class="text-xs text-gray-400 mt-1">Valores válidos: <code>miopia</code>, <code>lectura</code>, <code>sin_graduacion</code>, <code>toallitas</code>. Para varios separa por coma (ej: <code>miopia,lectura</code>). Vacío = lleva al catálogo completo.</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                            <textarea name="category_cards[{{ $i }}][description]" rows="2"
                                      class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">{{ $card['description'] }}</textarea>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Imagen de fondo</label>
                            @if(!empty($card['image']))
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('storage/' . $card['image']) }}" alt="Imagen actual"
                                     class="h-16 w-24 object-cover rounded-lg border border-gray-200">
                                <label class="flex items-center gap-1 text-xs text-red-500 cursor-pointer">
                                    <input type="checkbox" name="category_cards[{{ $i }}][image_remove]" value="1"
                                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Quitar imagen actual
                                </label>
                            </div>
                            @endif
                            <input type="file" name="category_cards[{{ $i }}][image_file]" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-400 mt-1">JPG/PNG/WebP, máx 2MB. Recomendado 800×450 px.</p>
                        </div>

                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-medium text-gray-600">Icono SVG (se muestra cuando la tarjeta voltea)</label>
                                <button type="button" @click="showSvg{{ $i }} = !showSvg{{ $i }}"
                                        class="text-xs text-blue-500 hover:text-blue-700"
                                        x-text="showSvg{{ $i }} ? 'Ocultar SVG' : 'Editar SVG'"></button>
                            </div>
                            @if($card['icon_svg'])
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(55,138,221,0.1);">
                                    <svg class="w-5 h-5" style="color:#D9B56D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon_svg'] !!}</svg>
                                </div>
                                <span class="text-xs text-gray-400">Icono actual</span>
                            </div>
                            @endif
                            <div x-show="showSvg{{ $i }}" x-collapse>
                                <textarea name="category_cards[{{ $i }}][icon_svg]" rows="3"
                                          class="w-full rounded-lg border-gray-300 shadow-sm text-xs font-mono focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Pega el contenido SVG (paths) aquí">{{ $card['icon_svg'] }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Pega solo los &lt;path&gt; internos del SVG, no el &lt;svg&gt; exterior.</p>
                            </div>
                            <input x-show="!showSvg{{ $i }}" type="hidden" name="category_cards[{{ $i }}][icon_svg]" value="{{ $card['icon_svg'] }}">
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- ═══════════ CATÁLOGO HEADER ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('catalog')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Encabezado del catálogo</h3>
                <svg :class="openSection === 'catalog' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'catalog'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta</label>
                    <input type="text" name="catalog_label" value="{{ $page->catalog_label }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="catalog_title" value="{{ $page->catalog_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="catalog_subtitle" value="{{ $page->catalog_subtitle }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- ═══════════ PROMO 2×1 ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('promo')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Banner promoción 2×1</h3>
                <svg :class="openSection === 'promo' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'promo'" x-collapse class="px-6 pb-6 space-y-4">
                {{-- ⭐ Producto estrella --}}
                <div class="p-4 rounded-xl" style="background:#FBF4E6;border:1px solid #E8CC92;">
                    <label class="block text-sm font-semibold mb-1" style="color:#2E2A26;">
                        ⭐ Producto estrella del home
                    </label>
                    <p class="text-xs mb-3" style="color:#6B6157;">
                        Es el producto destacado que se muestra en la sección split con imagen grande.
                        Solo aparecen productos activos, con stock y con al menos 1 imagen.
                    </p>
                    <select name="star_product_id"
                            class="w-full rounded-lg text-sm px-3 py-2"
                            style="background:#FFFFFF;border:1px solid #D9B56D;color:#2E2A26;">
                        <option value="">— Auto (primer producto destacado con imagen)</option>
                        @php
                            $eligibleProducts = \App\Models\Product::where('is_active', true)
                                ->where('stock', '>', 0)
                                ->whereNotNull('images')
                                ->whereRaw('JSON_LENGTH(images) > 0')
                                ->with('category')
                                ->orderByDesc('is_featured')
                                ->orderBy('name')
                                ->get();
                        @endphp
                        @foreach($eligibleProducts as $prod)
                            <option value="{{ $prod->id }}" {{ $page->star_product_id == $prod->id ? 'selected' : '' }}>
                                [{{ $prod->internal_code }}] {{ $prod->name }}
                                @if($prod->category) · {{ $prod->category->name }} @endif
                                — ${{ number_format($prod->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs mt-2" style="color:#9CA3AF;font-style:italic;">
                        💡 Tip: si dejas "Auto", el sistema elige el mejor candidato automáticamente.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta</label>
                    <input type="text" name="promo_label" value="{{ $page->promo_label }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="promo_title" value="{{ $page->promo_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="promo_description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->promo_description }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <input type="text" name="promo_price" value="{{ $page->promo_price }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                               placeholder="$499.90">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nota del precio</label>
                        <input type="text" name="promo_price_note" value="{{ $page->promo_price_note }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto del botón</label>
                    <input type="text" name="promo_btn_text" value="{{ $page->promo_btn_text }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                {{-- Fondo imagen/video --}}
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Fondo del banner (imagen o video)</h4>

                    @if($page->promo_background)
                    <div class="mb-3">
                        @php $ext = strtolower(pathinfo($page->promo_background, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['mp4','webm']))
                        <video src="{{ asset('storage/' . $page->promo_background) }}" class="h-32 rounded-lg border border-gray-200" muted autoplay loop playsinline></video>
                        @else
                        <img src="{{ asset('storage/' . $page->promo_background) }}" alt="Fondo promo" class="h-32 rounded-lg border border-gray-200 object-cover">
                        @endif
                        <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="promo_background_remove" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Eliminar fondo actual
                        </label>
                    </div>
                    @endif

                    <input type="file" name="promo_background_file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-xs text-gray-400">JPG, PNG, WebP (imagen) o MP4, WebM (video). Máximo 10MB.</p>
                    <p class="mt-1 text-xs text-blue-600">Recomendado: 1920×800 px para imagen. Si no se sube nada, se usa el gradiente azul por defecto.</p>
                </div>
            </div>
        </div>

        {{-- ═══════════ TOALLITAS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('wipes')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Sección toallitas / accesorios</h3>
                <svg :class="openSection === 'wipes' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'wipes'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta</label>
                    <input type="text" name="wipes_label" value="{{ $page->wipes_label }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="wipes_title" value="{{ $page->wipes_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="wipes_description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->wipes_description }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Características (una por línea)</label>
                    <textarea name="wipes_features_text" rows="4"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Fórmula sin alcohol&#10;Sistema 2 en 1">{{ implode("\n", $page->wipes_features ?? []) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ═══════════ BENEFICIOS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('benefits')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Beneficios (¿Por qué elegir Belleza Áurea?)</h3>
                <svg :class="openSection === 'benefits' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'benefits'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta superior</label>
                    <input type="text" name="benefits_label" value="{{ $page->benefits_label }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="benefits_title" value="{{ $page->benefits_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="benefits_subtitle" value="{{ $page->benefits_subtitle }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Tarjetas de beneficios</h4>
                    <div x-data="benefitsRepeater()" x-init="init()">
                        <template x-for="(card, idx) in items" :key="idx">
                            <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-gray-500" x-text="'Beneficio ' + (idx + 1)"></span>
                                        <template x-if="card.icon_svg">
                                            <div class="w-7 h-7 rounded flex items-center justify-center" style="background:rgba(55,138,221,0.1);">
                                                <svg class="w-4 h-4" style="color:#D9B56D;" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="card.icon_svg"></svg>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="items.splice(idx, 1)" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Título</label>
                                        <input type="text" :name="'benefits_cards[' + idx + '][title]'" x-model="card.title"
                                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                        <input type="text" :name="'benefits_cards[' + idx + '][description]'" x-model="card.description"
                                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="mt-2" x-data="{ showSvg: false }">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-xs font-medium text-gray-600">Icono SVG</label>
                                        <button type="button" @click="showSvg = !showSvg"
                                                class="text-xs text-blue-500 hover:text-blue-700"
                                                x-text="showSvg ? 'Ocultar SVG' : 'Editar SVG'"></button>
                                    </div>
                                    <div x-show="showSvg" x-collapse>
                                        <textarea :name="'benefits_cards[' + idx + '][icon_svg]'" x-model="card.icon_svg" rows="2"
                                                  class="w-full rounded-lg border-gray-300 shadow-sm text-xs font-mono focus:border-blue-500 focus:ring-blue-500"
                                                  placeholder="Pega los <path> del SVG aquí"></textarea>
                                    </div>
                                    <input x-show="!showSvg" type="hidden" :name="'benefits_cards[' + idx + '][icon_svg]'" x-model="card.icon_svg">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="items.push({icon_svg:'', title:'', description:''})"
                                class="mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Agregar beneficio
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ TÍTULOS DE SECCIÓN (SEO / H2) ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 px-6 py-5">
            <h3 class="text-base font-semibold text-gray-900 mb-1">Títulos de sección (SEO)</h3>
            <p class="text-xs text-gray-400 mb-4">Estos son encabezados <strong>H2</strong> del home. Escríbelos con palabras clave de belleza para mejorar el posicionamiento.</p>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sección "Distribuidora / autoridad"</label>
                    <input type="text" name="authority_title" value="{{ old('authority_title', $page->authority_title) }}" maxlength="120"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Tu distribuidora de productos profesionales de belleza">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de Testimonios</label>
                    <input type="text" name="testimonials_title" value="{{ old('testimonials_title', $page->testimonials_title) }}" maxlength="120"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Lo que dicen nuestras clientes">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de Preguntas frecuentes</label>
                    <input type="text" name="faq_title" value="{{ old('faq_title', $page->faq_title) }}" maxlength="120"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Preguntas frecuentes">
                </div>
            </div>
        </div>

        {{-- ═══════════ TESTIMONIOS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Testimonios de clientes</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Se muestran en el home después de las preguntas frecuentes.</p>
                </div>
                <a href="{{ route('admin.testimonials.index') }}"
                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                    Gestionar testimonios
                </a>
            </div>
        </div>

        {{-- ═══════════ FAQ ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('faq')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Preguntas frecuentes (FAQ)</h3>
                <svg :class="openSection === 'faq' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'faq'" x-collapse class="px-6 pb-6">
                @php $faqs = $page->faqs ?? []; @endphp
                <div x-data="faqRepeater()" x-init="init()">
                    <template x-for="(faq, idx) in items" :key="idx">
                        <div class="bg-gray-50 rounded-lg p-4 mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-500" x-text="'Pregunta ' + (idx + 1)"></span>
                                <button type="button" @click="items.splice(idx, 1)" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                            </div>
                            <div class="mb-2">
                                <input type="text" :name="'faqs[' + idx + '][q]'" x-model="faq.q"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Pregunta">
                            </div>
                            <div>
                                <textarea :name="'faqs[' + idx + '][a]'" x-model="faq.a" rows="3"
                                          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Respuesta"></textarea>
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="items.push({q:'', a:''})"
                            class="mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar pregunta
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════ TRUST BADGES ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('trust')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Badges de confianza</h3>
                <svg :class="openSection === 'trust' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'trust'" x-collapse class="px-6 pb-6">
                @php $badges = $page->trust_badges ?? []; @endphp
                <div x-data="trustRepeater()" x-init="init()">
                    <template x-for="(badge, idx) in items" :key="idx">
                        <div class="bg-gray-50 rounded-lg p-4 mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500" x-text="'Badge ' + (idx + 1)"></span>
                                    <template x-if="badge.icon_svg">
                                        <div class="w-7 h-7 rounded flex items-center justify-center" style="background:rgba(55,138,221,0.1);">
                                            <svg class="w-4 h-4" style="color:#D9B56D;" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="badge.icon_svg"></svg>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="items.splice(idx, 1)" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Título</label>
                                    <input type="text" :name="'trust_badges[' + idx + '][title]'" x-model="badge.title"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                    <input type="text" :name="'trust_badges[' + idx + '][description]'" x-model="badge.description"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-2" x-data="{ showCustom: false }">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-medium text-gray-600">Icono</label>
                                    <button type="button" @click="showCustom = !showCustom"
                                            class="text-xs text-blue-500 hover:text-blue-700"
                                            x-text="showCustom ? 'Ocultar SVG' : 'SVG personalizado'"></button>
                                </div>
                                <div x-show="showCustom" x-collapse>
                                    <textarea :name="'trust_badges[' + idx + '][icon_svg]'" x-model="badge.icon_svg" rows="2"
                                              class="w-full rounded-lg border-gray-300 shadow-sm text-xs font-mono focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Paths SVG"></textarea>
                                </div>
                                <input x-show="!showCustom" type="hidden" :name="'trust_badges[' + idx + '][icon_svg]'" x-model="badge.icon_svg">
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="items.push({icon_svg:'', title:'', description:''})"
                            class="mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar badge
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════ COMPARATIVO Con vs. sin protección ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('comparison')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Comparativo (Con vs. sin protección)</h3>
                <svg :class="openSection === 'comparison' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'comparison'" x-collapse class="px-6 pb-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta superior</label>
                        <input type="text" name="comparison_label" value="{{ $page->comparison_label ?? 'Comparativo' }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="comparison_title" value="{{ $page->comparison_title ?? 'Con vs. sin protección' }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <textarea name="comparison_subtitle" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->comparison_subtitle ?? 'Mira la diferencia real cuando incorporas Belleza Áurea a tu rutina diaria.' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    {{-- Sin protección --}}
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 space-y-3">
                        <h4 class="text-sm font-semibold text-red-700">Columna izquierda — Sin protección</h4>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Título de la columna</label>
                            <input type="text" name="comparison_without_label" value="{{ $page->comparison_without_label ?? 'Sin protección' }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Items (uno por línea)</label>
                            <textarea name="comparison_without_items_text" rows="6"
                                      class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Productos genéricos sin garantía&#10;Precios altos comprando al detal&#10;Envíos lentos y sin seguimiento&#10;Sin soporte cuando surgen dudas">{{ implode("\n", $page->comparison_without_items ?? [
                                'Productos genéricos sin garantía',
                                'Precios altos comprando al detal',
                                'Envíos lentos y sin seguimiento',
                                'Sin soporte cuando surgen dudas',
                              ]) }}</textarea>
                        </div>
                    </div>

                    {{-- Con Belleza Áurea --}}
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-3">
                        <h4 class="text-sm font-semibold text-green-700">Columna derecha — Con Belleza Áurea</h4>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Título de la columna</label>
                            <input type="text" name="comparison_with_label" value="{{ $page->comparison_with_label ?? 'Con Belleza Áurea' }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Items (uno por línea)</label>
                            <textarea name="comparison_with_items_text" rows="6"
                                      class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Marcas originales verificadas&#10;Precios mayoristas reales&#10;Envío con seguimiento a todo el país&#10;Soporte directo por WhatsApp">{{ implode("\n", $page->comparison_with_items ?? [
                                'Marcas originales verificadas',
                                'Precios mayoristas reales',
                                'Envío con seguimiento a todo el país',
                                'Soporte directo por WhatsApp',
                              ]) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ CTA FINAL ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('cta')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">CTA final</h3>
                <svg :class="openSection === 'cta' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'cta'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="cta_title" value="{{ $page->cta_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <textarea name="cta_subtitle" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->cta_subtitle }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón principal</label>
                        <input type="text" name="cta_btn_primary_text" value="{{ $page->cta_btn_primary_text }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón secundario</label>
                        <input type="text" name="cta_btn_secondary_text" value="{{ $page->cta_btn_secondary_text }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Línea de confianza (una por línea)</label>
                    <textarea name="cta_trust_items_text" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Envío gratis +$999&#10;Garantía 6 meses&#10;30 días de devolución">{{ implode("\n", $page->cta_trust_items ?? []) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Botón guardar --}}
        <button type="submit"
                class="w-full py-3 rounded-xl text-white font-medium text-base transition-colors"
                style="background:#D9B56D;"
                onmouseover="this.style.background='#BE9A53'"
                onmouseout="this.style.background='#D9B56D'">
            Guardar cambios
        </button>
    </form>
</div>

@push('scripts')
<script>
function faqRepeater() {
    return {
        items: [],
        init() {
            this.items = @json($page->faqs ?? []);
        }
    };
}

function trustRepeater() {
    return {
        items: [],
        init() {
            this.items = @json($page->trust_badges ?? []);
        }
    };
}

function benefitsRepeater() {
    return {
        items: [],
        init() {
            this.items = @json($page->benefits_cards ?? []);
            if (this.items.length === 0) {
                this.items = [
                    { icon_svg: '<path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>', title: 'Calidad profesional', description: 'Productos originales de marcas verificadas para resultados de salón.' },
                    { icon_svg: '<path d="M21.752 15.002A9.718 9.718 0 0118 15.75 9.75 9.75 0 018.25 6c0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25 9.75 9.75 0 0012.75 21a9.753 9.753 0 009.002-5.998z"/>', title: 'Precios mayoristas', description: 'Compra al por mayor con los mejores precios del mercado.' },
                    { icon_svg: '<path d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/>', title: 'Envío a toda Colombia', description: 'Despacho con seguimiento a los 32 departamentos del país.' },
                    { icon_svg: '<path d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>', title: 'Atención cercana', description: 'Soporte directo por WhatsApp cuando lo necesites.' },
                ];
            }
        }
    };
}
</script>
@endpush
@endsection
