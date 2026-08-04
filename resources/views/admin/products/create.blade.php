@extends('layouts.admin')

@section('title', 'Nuevo producto')
@section('page_title', 'Nuevo producto')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Basic info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información básica</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div x-data="quickCategory()">
                    @php $brands = \App\Models\Brand::active()->ordered()->get(); @endphp
                    <div style="margin-bottom:1rem;">
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Marca <span class="text-xs text-gray-400 ml-2">(opcional)</span>
                        </label>
                        <select id="brand_id" name="brand_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">— Sin marca —</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            ¿Falta una marca? <a href="{{ route('admin.brands.create') }}" target="_blank" style="color:#BE9A53;text-decoration:underline;">Créala aquí →</a>
                        </p>
                    </div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <div class="flex items-center gap-2">
                        <select id="category_id" name="category_id" required
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="showModal = true"
                                class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition-colors"
                                title="Crear categoría rápida">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Quick create category modal --}}
                    <div x-show="showModal" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                         @keydown.escape.window="showModal = false">
                        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.outside="showModal = false">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nueva categoría</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                    <input type="text" x-model="newName" x-ref="catName"
                                           @keydown.enter.prevent="createCategory()"
                                           placeholder="Ej: Esmalte semipermanente"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <p x-show="error" x-text="error" class="text-sm text-red-600"></p>
                            </div>
                            <div class="flex justify-end gap-2 mt-5">
                                <button type="button" @click="showModal = false; error = ''"
                                        class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</button>
                                <button type="button" @click="createCategory()" :disabled="saving"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                                    <span x-show="!saving">Crear</span>
                                    <span x-show="saving">Creando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ── Precios ── --}}
                <div x-data="{
                        price: {{ (float) old('price', 0) }},
                        compare: {{ (float) old('compare_price', 0) }},
                        cost: {{ (float) old('cost_price', 0) }},
                        get margin() { return this.cost > 0 ? this.price - this.cost : null; },
                        get marginPct() { return (this.cost > 0 && this.price > 0) ? ((this.price - this.cost) / this.price * 100) : null; },
                        money(n) { return n === null ? '—' : '$' + Number(n).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2}); },
                     }"
                     class="rounded-xl p-5 border" style="background:#FBF8F2;border-color:#E8CC92;">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-4" style="color:#BE9A53;letter-spacing:0.15em;">Precios</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="cost_price" class="block text-xs font-medium text-gray-600 mb-1">
                                Costo (PV Distribuidor)
                                <span class="text-gray-400" title="Lo que tú pagas por unidad. Solo visible en admin.">ⓘ</span>
                            </label>
                            <input type="number" id="cost_price" name="cost_price" x-model.number="cost" step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div>
                            <label for="price" class="block text-xs font-medium text-gray-600 mb-1">
                                Precio venta web *
                                <span class="text-gray-400" title="Lo que el cliente paga en la tienda online.">ⓘ</span>
                            </label>
                            <input type="number" id="price" name="price" x-model.number="price" step="0.01" min="0" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white font-semibold">
                        </div>
                        <div>
                            <label for="compare_price" class="block text-xs font-medium text-gray-600 mb-1">
                                Precio anterior / PVP
                                <span class="text-gray-400" title="PV Centro de Exp. Se muestra tachado al lado del precio. Si está vacío o ≤ precio venta, no aparece.">ⓘ</span>
                            </label>
                            <input type="number" id="compare_price" name="compare_price" x-model.number="compare" step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs" style="color:#6B6157;">
                        <span>
                            Margen bruto:
                            <strong style="color:#2E2A26;" x-text="money(margin)"></strong>
                            <span x-show="marginPct !== null" x-cloak>
                                (<span x-text="marginPct?.toFixed(1)+'%'" :style="marginPct < 20 ? 'color:#C97B6B' : (marginPct < 40 ? 'color:#BE9A53' : 'color:#7C9B7E')"></span>)
                            </span>
                        </span>
                        <span x-show="compare > 0 && compare > price" x-cloak style="color:#7C9B7E;">
                            Descuento mostrado: <strong x-text="(((compare - price) / compare * 100).toFixed(0) + '%')"></strong>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                {{-- Placeholder — la visibilidad ahora vive en un bloque dedicado abajo --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clasificación *</label>
                    <p class="text-xs text-gray-400 mb-2">Define dónde aparece el producto en la tienda. Puedes marcar las dos si aplica.</p>
                    <div class="flex flex-wrap gap-4">
                        @php $currentTypes = old('type', ['sin_graduacion']); @endphp
                        @foreach(['sin_graduacion' => ['Producto individual', 'Skincare, fragancia, accesorio, etc.'], 'toallitas' => ['Set / Ritual', 'Kit de varios productos juntos']] as $val => [$label, $hint])
                        <label class="flex items-start gap-2 cursor-pointer p-3 rounded-lg border transition-colors"
                               style="{{ in_array($val, $currentTypes) ? 'border-color:#D9B56D;background:#FBF4E6;' : 'border-color:#e5e7eb;' }}">
                            <input type="checkbox" name="type[]" value="{{ $val }}"
                                   {{ in_array($val, $currentTypes) ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 rounded border-gray-300 focus:ring-yellow-500" style="accent-color:#D9B56D;">
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $hint }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Images --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imágenes</h2>
            <input type="file" name="images[]" multiple accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-2 text-xs text-gray-400">JPG, PNG o WebP. Máximo 2MB por imagen. Puedes seleccionar varias.</p>
        </div>

        {{-- Variantes — genéricas para cualquier producto de belleza --}}
        @php
            $variantTypes = \App\Models\ProductVariant::OPTION_TYPES;
            $variantLabels = \App\Models\ProductVariant::DEFAULT_LABELS;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
             x-data="{
                variants: [],
                defaultLabels: {{ json_encode($variantLabels) }},
                addVariant() {
                    this.variants.push({
                        option_type: 'color', name: 'Tono', value: '',
                        color: '', color_hex: '#D9B56D',
                        graduation: '', graduation_type: '',
                        price_modifier: 0, stock: 0
                    });
                },
                onTypeChange(v) {
                    if (this.defaultLabels[v.option_type] && (!v.name || Object.values(this.defaultLabels).includes(v.name))) {
                        v.name = this.defaultLabels[v.option_type];
                    }
                    if (v.option_type !== 'color') { v.color_hex = ''; }
                    else if (!v.color_hex) { v.color_hex = '#D9B56D'; }
                },
             }">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-lg font-semibold text-gray-800" style="font-family:'Playfair Display',serif;">Variantes</h2>
                <button type="button" @click="addVariant()"
                        class="text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors"
                        style="background:#D9B56D;color:#2E2A26;"
                        onmouseover="this.style.background='#E8CC92'"
                        onmouseout="this.style.background='#D9B56D'">
                    + Agregar variante
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Color de esmalte, tamaño de envase, aroma, acabado, etc. Cada variante puede tener su propio precio, stock e imagen.
            </p>
            <template x-for="(variant, index) in variants" :key="index">
                <div class="rounded-xl p-4 mb-3" style="background:#FBF8F2;border:1px solid #E8CC92;">
                    <input type="hidden" :name="'variants['+index+'][color]'" :value="variant.color || variant.value">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                            <select :name="'variants['+index+'][option_type]'" x-model="variant.option_type" @change="onTypeChange(variant)"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                                @foreach($variantTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Etiqueta</label>
                            <input type="text" :name="'variants['+index+'][name]'" x-model="variant.name" :placeholder="defaultLabels[variant.option_type]"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Valor *</label>
                            <input type="text" :name="'variants['+index+'][value]'" x-model="variant.value"
                                   :placeholder="variant.option_type === 'color' ? 'Rojo Coral' : (variant.option_type === 'size' ? '50 ml' : 'Valor')"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div class="md:col-span-2" x-show="variant.option_type === 'color'" x-cloak>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hex</label>
                            <div class="flex items-center gap-1">
                                <input type="color" :value="variant.color_hex || '#D9B56D'" @input="variant.color_hex = $event.target.value"
                                       class="w-9 h-9 p-0.5 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" :name="'variants['+index+'][color_hex]'" x-model="variant.color_hex" maxlength="7"
                                       class="flex-1 border border-gray-300 rounded-lg px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                            </div>
                        </div>
                        <div class="md:col-span-2 flex items-end justify-end">
                            <button type="button" @click="variants.splice(index, 1)" class="text-xs text-red-600 hover:text-red-800 py-2">Eliminar</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-3 items-end">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">+/- Precio</label>
                            <input type="number" :name="'variants['+index+'][price_modifier]'" x-model="variant.price_modifier" step="0.01" placeholder="0"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Stock</label>
                            <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div class="md:col-span-7">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Imagen específica de esta variante (opcional)</label>
                            <input type="file" :name="'variants['+index+'][image]'" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="variants.length === 0">
                <p class="text-sm text-gray-400 italic">Sin variantes. Haz clic en "+ Agregar variante" para crear una.</p>
            </template>
        </div>

        {{-- ───────────── VISIBILIDAD EN EL HOME ───────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-baseline justify-between mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
                <div>
                    <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Visibilidad</p>
                    <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">¿Dónde y cómo aparece este producto?</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('is_active', true) ? '#D9B56D' : '#E5DCC9' }};background:{{ old('is_active', true) ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4" style="accent-color:#D9B56D;">
                    <div class="flex-1">
                        <p class="text-sm font-semibold" style="color:#2E2A26;">Producto activo</p>
                        <p class="text-xs mt-1" style="color:#6B6157;">Si está desmarcado, <strong>no se muestra en ningún lugar</strong> del storefront.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('is_featured') ? '#D9B56D' : '#E5DCC9' }};background:{{ old('is_featured') ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured') ? 'checked' : '' }}
                           class="mt-1 w-4 h-4" style="accent-color:#D9B56D;">
                    <div class="flex-1">
                        <p class="text-sm font-semibold flex items-center gap-2" style="color:#2E2A26;">
                            ★ Destacado en el home
                            <span class="text-[10px] px-2 py-0.5 rounded-full" style="background:#D9B56D;color:#FFFFFF;letter-spacing:.05em;">RECOMENDADO</span>
                        </p>
                        <p class="text-xs mt-1" style="color:#6B6157;">Aparece <strong>primero</strong> en la sección "Nuestros productos" del home con un badge dorado.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('badge_2x1') ? '#D9B56D' : '#E5DCC9' }};background:{{ old('badge_2x1') ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="hidden" name="badge_2x1" value="0">
                    <input type="checkbox" name="badge_2x1" value="1"
                           {{ old('badge_2x1') ? 'checked' : '' }}
                           class="mt-1 w-4 h-4" style="accent-color:#D9B56D;">
                    <div class="flex-1">
                        <p class="text-sm font-semibold" style="color:#2E2A26;">Promo 2×1</p>
                        <p class="text-xs mt-1" style="color:#6B6157;">Muestra el badge <strong>2×1</strong> y aplica "paga 1, lleva 2" en el carrito para este producto. (También puedes activar 2×1 por categoría.)</p>
                    </div>
                </label>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                    Orden de aparición
                    <span class="text-xs ml-2" style="color:#9CA3AF;">— entre los destacados, controla el orden manual</span>
                </label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                       class="w-32 rounded-lg px-3 py-2 text-sm"
                       style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                <p class="mt-2 text-xs italic" style="color:#9CA3AF;">
                    1 = primero · 2 = segundo · etc. Los productos sin orden (0) aparecen después.
                </p>
            </div>
        </div>

        {{-- ───────────── SEO ───────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="flex items-baseline justify-between mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
                <div>
                    <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">SEO</p>
                    <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Cómo te ve Google</h2>
                    <p class="text-xs mt-1" style="color:#6B6157;">Si dejas vacío, se genera automático del nombre + descripción del producto.</p>
                </div>
            </div>

            {{-- Slug --}}
            <div class="mb-4">
                <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                    URL (slug) <span class="text-xs ml-2" style="color:#9CA3AF;">— se autogenera del nombre si lo dejas vacío</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-xs" style="color:#9CA3AF;">{{ url('/productos') }}/</span>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="flex-1 rounded-lg px-3 py-2 text-sm font-mono"
                           style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                </div>
            </div>

            @include('admin.partials.seo-panel', [
                'seo'           => new \App\Models\Product,
                'ogCol'         => 'og_image_path',
                'twCol'         => 'twitter_image_path',
                'baseUrl'       => url('/productos'),
                'slug'          => old('slug'),
                'titleFallback' => 'Título del producto | Belleza Áurea',
                'descFallback'  => 'Descripción del producto…',
            ])
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Crear producto
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function seoEditor(initial) {
    return {
        title: initial.title || '',
        desc:  initial.desc || '',
        slug:  initial.slug || '',
        productName: initial.productName || '',
        baseUrl: initial.baseUrl || '',
    };
}
</script>
<script>
function quickCategory() {
    return {
        showModal: false,
        newName: '',
        error: '',
        saving: false,
        async createCategory() {
            if (!this.newName.trim()) {
                this.error = 'El nombre es obligatorio.';
                return;
            }
            this.saving = true;
            this.error = '';
            try {
                const res = await fetch('{{ route("admin.categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ name: this.newName.trim() }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.message || Object.values(data.errors || {}).flat()[0] || 'Error al crear.';
                    return;
                }
                const select = document.getElementById('category_id');
                const option = new Option(data.name, data.id, true, true);
                select.add(option);
                this.newName = '';
                this.showModal = false;
            } catch (e) {
                this.error = 'Error de conexión.';
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush
