@extends('layouts.admin')

@section('title', 'Editar: ' . $product->name)
@section('page_title', 'Editar producto')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf @method('PUT')

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
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Marca
                        <span class="text-xs text-gray-400 ml-2">(opcional)</span>
                    </label>
                    @php $brands = \App\Models\Brand::active()->ordered()->get(); @endphp
                    <select id="brand_id" name="brand_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">— Sin marca —</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ old('brand_id', $product->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        ¿Falta una marca? <a href="{{ route('admin.brands.create') }}" target="_blank" style="color:#BE9A53;text-decoration:underline;">Créala aquí →</a>
                    </p>
                </div>
                <div x-data="quickCategory()">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <div class="flex items-center gap-2">
                        <select id="category_id" name="category_id" required
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                                    <input type="text" x-model="newName"
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
                        price: {{ (float) old('price', $product->price) }},
                        compare: {{ (float) old('compare_price', $product->compare_price ?? 0) }},
                        cost: {{ (float) old('cost_price', $product->cost_price ?? 0) }},
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
                    {{-- Cálculo de margen en vivo --}}
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
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- ── Precios mayoristas ── --}}
                @php
                    $wholesaleDefaultPct = (int) round((1 - (float) config('wholesale.default_discount', 0.80)) * 100);
                    $defaultWholesalePreview = (int) round((float) old('price', $product->price) * (float) config('wholesale.default_discount', 0.80));
                @endphp
                <div class="md:col-span-2 rounded-xl p-5 border" style="background:#FDF6E9;border-color:#E0BE77;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#EBCF90;color:#3B310F;">
                            🏪 Mayoristas
                        </span>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color:#8A6E2E;letter-spacing:0.15em;">Precios para distribuidoras aprobadas</p>
                    </div>
                    <p class="text-xs text-gray-600 mb-4">
                        Se aplican automáticamente cuando la clienta está logueada como mayorista aprobada.
                        Si dejas vacío el precio, se calcula un <strong>{{ $wholesaleDefaultPct }}% de descuento</strong> sobre el precio de venta web.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="wholesale_price" class="block text-xs font-medium text-gray-600 mb-1">
                                Precio mayorista (COP)
                            </label>
                            <input type="number" id="wholesale_price" name="wholesale_price"
                                   value="{{ old('wholesale_price', $product->wholesale_price) }}" step="1" min="0"
                                   placeholder="Deja vacío para usar {{ $wholesaleDefaultPct }}% de descuento (≈ ${{ number_format($defaultWholesalePreview, 0, ',', '.') }})"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        <div>
                            <label for="wholesale_min_qty" class="block text-xs font-medium text-gray-600 mb-1">
                                Cantidad mínima por unidad
                            </label>
                            <input type="number" id="wholesale_min_qty" name="wholesale_min_qty"
                                   value="{{ old('wholesale_min_qty', $product->wholesale_min_qty ?? 1) }}" step="1" min="1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                            <p class="mt-1 text-xs text-gray-500">1 = siempre aplica el precio mayorista.</p>
                        </div>
                    </div>
                </div>
                {{-- Placeholder — la visibilidad ahora vive en un bloque dedicado abajo --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clasificación *</label>
                    <p class="text-xs text-gray-400 mb-2">Define dónde aparece el producto en la tienda. Puedes marcar las dos si aplica.</p>
                    <div class="flex flex-wrap gap-4">
                        @php $currentTypes = old('type', $product->type ?? ['sin_graduacion']); @endphp
                        @foreach(['sin_graduacion' => ['Producto individual', 'Skincare, fragancia, accesorio, etc.'], 'toallitas' => ['Set / Ritual', 'Kit de varios productos juntos']] as $val => [$label, $hint])
                        <label class="flex items-start gap-2 cursor-pointer p-3 rounded-lg border transition-colors"
                               :class="'{{ in_array($val, $currentTypes) ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 hover:bg-gray-50' }}'"
                               style="{{ in_array($val, $currentTypes) ? 'border-color:#D9B56D;background:#FBF4E6;' : '' }}">
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
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Images --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imágenes</h2>
            @if($product->images && count($product->images) > 0)
                <div class="flex flex-wrap gap-4 mb-4">
                    @foreach($product->images as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image) }}" alt="" class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                            <label class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <input type="checkbox" name="remove_images[]" value="{{ $image }}" class="sr-only peer">
                                <span class="text-white text-xs peer-checked:hidden">Eliminar</span>
                                <span class="text-red-400 text-xs hidden peer-checked:block font-bold">Marcado</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mb-4">Pasa el mouse sobre una imagen y haz clic para marcarla para eliminar.</p>
            @endif
            <input type="file" name="images[]" multiple accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-2 text-xs text-gray-400">Agrega más imágenes (JPG, PNG o WebP, máx. 2MB cada una).</p>
        </div>

        {{-- Variantes — genéricas para cualquier producto de belleza --}}
        @php
            $existingVariants = old('variants', $product->variants->map(fn($v) => [
                'id'             => $v->id,
                'option_type'    => $v->option_type ?: 'other',
                'name'           => $v->name,
                'value'          => $v->value,
                'color'          => $v->color,
                'color_hex'      => $v->color_hex,
                'graduation'     => $v->graduation,
                'graduation_type'=> $v->graduation_type,
                'price_modifier' => $v->price_modifier,
                'stock'          => $v->stock,
                'image_path'     => $v->image_path,
            ])->toArray());
            $variantTypes = \App\Models\ProductVariant::OPTION_TYPES;
            $variantLabels = \App\Models\ProductVariant::DEFAULT_LABELS;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
             x-data="{
                variants: {{ json_encode(array_values($existingVariants)) }},
                storageUrl: '{{ asset('storage') }}',
                defaultLabels: {{ json_encode($variantLabels) }},
                addVariant() {
                    this.variants.push({
                        id: null, option_type: 'color', name: 'Tono', value: '',
                        color: '', color_hex: '#D9B56D',
                        graduation: '', graduation_type: '',
                        price_modifier: 0, stock: 0, image_path: null
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
                Define las opciones que el cliente puede elegir: color de esmalte, tamaño de envase, aroma, acabado, etc.
                Cada variante puede tener su propio precio, stock e imagen.
            </p>
            <template x-for="(variant, index) in variants" :key="index">
                <div class="rounded-xl p-4 mb-3" style="background:#FBF8F2;border:1px solid #E8CC92;">
                    <input type="hidden" :name="'variants['+index+'][id]'" :value="variant.id">
                    <input type="hidden" :name="'variants['+index+'][color]'" :value="variant.color || variant.value">

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        {{-- Tipo --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                            <select :name="'variants['+index+'][option_type]'" x-model="variant.option_type"
                                    @change="onTypeChange(variant)"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                                @foreach($variantTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Etiqueta --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Etiqueta</label>
                            <input type="text" :name="'variants['+index+'][name]'" x-model="variant.name"
                                   :placeholder="defaultLabels[variant.option_type]"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>

                        {{-- Valor --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Valor *</label>
                            <input type="text" :name="'variants['+index+'][value]'" x-model="variant.value"
                                   :placeholder="variant.option_type === 'color' ? 'Rojo Coral' : (variant.option_type === 'size' ? '50 ml' : (variant.option_type === 'scent' ? 'Rosa' : 'Valor'))"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>

                        {{-- Color hex (solo si tipo color) --}}
                        <div class="md:col-span-2" x-show="variant.option_type === 'color'" x-cloak>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hex</label>
                            <div class="flex items-center gap-1">
                                <input type="color" :value="variant.color_hex || '#D9B56D'"
                                       @input="variant.color_hex = $event.target.value"
                                       class="w-9 h-9 p-0.5 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" :name="'variants['+index+'][color_hex]'" x-model="variant.color_hex"
                                       maxlength="7" placeholder="#D9B56D"
                                       class="flex-1 border border-gray-300 rounded-lg px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="md:col-span-2 flex items-end justify-end">
                            <button type="button" @click="variants.splice(index, 1)"
                                    class="text-xs text-red-600 hover:text-red-800 py-2">Eliminar</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-3 items-end">
                        {{-- Precio mod --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">+/- Precio</label>
                            <input type="number" :name="'variants['+index+'][price_modifier]'" x-model="variant.price_modifier" step="0.01"
                                   placeholder="0"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        {{-- Stock --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Stock</label>
                            <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                        </div>
                        {{-- Imagen --}}
                        <div class="md:col-span-7 flex items-end gap-3">
                            <div x-show="variant.image_path" class="flex items-center gap-2 shrink-0">
                                <img :src="variant.image_path ? storageUrl + '/' + variant.image_path : ''"
                                     class="w-14 h-14 object-cover rounded-lg border border-gray-200">
                                <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                                    <input type="checkbox" :name="'variants['+index+'][remove_image]'" value="1" class="w-3 h-3">
                                    Quitar
                                </label>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1" x-text="variant.image_path ? 'Reemplazar imagen' : 'Imagen específica de esta variante (opcional)'"></label>
                                <input type="file" :name="'variants['+index+'][image]'" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                            </div>
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
                {{-- ACTIVO --}}
                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('is_active', $product->is_active) ? '#D9B56D' : '#E5DCC9' }};background:{{ old('is_active', $product->is_active) ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4" style="accent-color:#D9B56D;">
                    <div class="flex-1">
                        <p class="text-sm font-semibold" style="color:#2E2A26;">Producto activo</p>
                        <p class="text-xs mt-1" style="color:#6B6157;">Si está desmarcado, <strong>no se muestra en ningún lugar</strong> del storefront (ni catálogo, ni home, ni búsqueda).</p>
                    </div>
                </label>

                {{-- DESTACADO --}}
                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('is_featured', $product->is_featured) ? '#D9B56D' : '#E5DCC9' }};background:{{ old('is_featured', $product->is_featured) ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4" style="accent-color:#D9B56D;">
                    <div class="flex-1">
                        <p class="text-sm font-semibold flex items-center gap-2" style="color:#2E2A26;">
                            ★ Destacado en el home
                            <span class="text-[10px] px-2 py-0.5 rounded-full" style="background:#D9B56D;color:#FFFFFF;letter-spacing:.05em;">RECOMENDADO</span>
                        </p>
                        <p class="text-xs mt-1" style="color:#6B6157;">Aparece <strong>primero</strong> en la sección "Nuestros productos" del home con un badge dorado. Marca aquí tus 4–8 mejores.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       style="border-color:{{ old('badge_2x1', $product->badge_2x1) ? '#D9B56D' : '#E5DCC9' }};background:{{ old('badge_2x1', $product->badge_2x1) ? '#FBF4E6' : '#FBF8F2' }};">
                    <input type="hidden" name="badge_2x1" value="0">
                    <input type="checkbox" name="badge_2x1" value="1"
                           {{ old('badge_2x1', $product->badge_2x1) ? 'checked' : '' }}
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
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                       class="w-32 rounded-lg px-3 py-2 text-sm"
                       style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                <p class="mt-2 text-xs" style="color:#9CA3AF;font-style:italic;">
                    1 = primero · 2 = segundo · etc. Los productos sin orden definido (0) aparecen después.
                </p>
            </div>

            <div class="mt-5 p-3 rounded-lg" style="background:#FBF4E6;border:1px solid #E8CC92;">
                <p class="text-xs flex items-start gap-2" style="color:#6B6157;line-height:1.6;">
                    <span style="color:#BE9A53;font-size:14px;line-height:1;">💡</span>
                    <span><strong style="color:#2E2A26;">Cómo aparece en el home:</strong>
                    primero los <strong>★ Destacados</strong> (los 4-8 que marques arriba), luego el resto por <strong>orden</strong> y por más recientes. Solo se muestran los 8 primeros — los demás van al catálogo completo.</span>
                </p>
            </div>
        </div>

        {{-- ───────────── CONTENIDO ENRIQUECIDO (AI-friendly / GEO) ───────────── --}}
        @php
            $featuresRaw = is_array($product->key_features ?? null)
                ? implode("\n", $product->key_features)
                : '';
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-baseline justify-between mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
                <div>
                    <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Contenido enriquecido</p>
                    <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Contenido para humanos y para IA</h2>
                    <p class="text-xs mt-1" style="color:#6B6157;">
                        Estos campos se muestran en la página del producto y también se exponen como datos estructurados
                        para que <strong>ChatGPT, Perplexity, Google AI Overviews y Bing Copilot</strong> puedan citar tu producto.
                    </p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                        Características clave <span class="text-xs ml-2" style="color:#9CA3AF;">(una por línea — se rendean como bullets ✦)</span>
                    </label>
                    <textarea name="key_features_raw" rows="5"
                              placeholder="Ej:&#10;Vitamina C estabilizada al 15%&#10;Aceite de rosa mosqueta prensado en frío&#10;Textura ligera, absorción inmediata&#10;Apto para piel sensible&#10;Resultados visibles en 28 días"
                              class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                              style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">{{ old('key_features_raw', $featuresRaw) }}</textarea>
                    <p class="mt-1 text-xs italic" style="color:#9CA3AF;">💡 Los LLMs adoran las listas. 4–8 puntos es el sweet spot.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                            Modo de uso
                        </label>
                        <textarea name="how_to_use" rows="4"
                                  placeholder="Cómo se aplica el producto, paso a paso. Ej: '1. Limpia el rostro. 2. Aplica 3 gotas sobre piel seca. 3. Masajea con movimientos ascendentes. Usar mañana y noche.'"
                                  class="w-full rounded-lg px-3 py-2 text-sm"
                                  style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">{{ old('how_to_use', $product->how_to_use) }}</textarea>
                        <p class="mt-1 text-xs italic" style="color:#9CA3AF;">Se publica como <code>HowTo</code> Schema.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                            Ingredientes / Composición (INCI)
                        </label>
                        <textarea name="ingredients" rows="4"
                                  placeholder="Lista completa INCI o de composición. Ej: 'Aqua, Ascorbyl Glucoside, Glycerin, Rosa Canina Fruit Oil, Niacinamide, Tocopherol, Citric Acid, Sodium Benzoate.'"
                                  class="w-full rounded-lg px-3 py-2 text-sm"
                                  style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">{{ old('ingredients', $product->ingredients) }}</textarea>
                        <p class="mt-1 text-xs italic" style="color:#9CA3AF;">Mejora confianza + matches con búsquedas tipo "con ácido hialurónico".</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                        Recomendado para <span class="text-xs ml-2" style="color:#9CA3AF;">(tipo de piel, cabello, uso, edad...)</span>
                    </label>
                    <input type="text" name="suitable_for" maxlength="500"
                           value="{{ old('suitable_for', $product->suitable_for) }}"
                           placeholder="ej. Piel sensible y reactiva · Hidratación diaria · Mayores de 25 años"
                           class="w-full rounded-lg px-3 py-2 text-sm"
                           style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                    <p class="mt-1 text-xs italic" style="color:#9CA3AF;">Ayuda a la IA a responder "el mejor producto para piel grasa".</p>
                </div>
            </div>
        </div>

        {{-- ───────────── DATOS TÉCNICOS (Schema.org Product) ───────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-baseline justify-between mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
                <div>
                    <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Datos técnicos</p>
                    <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Para Schema.org & Google Shopping</h2>
                    <p class="text-xs mt-1" style="color:#6B6157;">Códigos de barras y origen — habilitan rich snippets y Google Shopping.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                            GTIN / EAN / UPC <span class="text-xs ml-2" style="color:#9CA3AF;">(código de barras)</span>
                        </label>
                        <input type="text" name="gtin" maxlength="14"
                               value="{{ old('gtin', $product->gtin) }}"
                               placeholder="7701234567890"
                               class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                               style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                            MPN <span class="text-xs ml-2" style="color:#9CA3AF;">(código del fabricante)</span>
                        </label>
                        <input type="text" name="mpn" maxlength="70"
                               value="{{ old('mpn', $product->mpn) }}"
                               placeholder="WUH-NP-001"
                               class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                               style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">Peso / volumen</label>
                        <input type="number" name="weight_value" step="0.01" min="0"
                               value="{{ old('weight_value', $product->weight_value) }}"
                               placeholder="50"
                               class="w-full rounded-lg px-3 py-2 text-sm"
                               style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">Unidad</label>
                        <select name="weight_unit"
                                class="w-full rounded-lg px-3 py-2 text-sm"
                                style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                            <option value="">—</option>
                            @foreach(['ml' => 'ml', 'L' => 'L', 'g' => 'g', 'kg' => 'kg', 'oz' => 'oz'] as $u => $lbl)
                                <option value="{{ $u }}" {{ old('weight_unit', $product->weight_unit) === $u ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#4B4541;">País de origen</label>
                        <input type="text" name="country_origin" maxlength="100"
                               value="{{ old('country_origin', $product->country_origin) }}"
                               placeholder="ej. Francia"
                               class="w-full rounded-lg px-3 py-2 text-sm"
                               style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                           style="border-color:{{ old('is_cruelty_free', $product->is_cruelty_free) ? '#A8B29A' : '#E5DCC9' }};background:{{ old('is_cruelty_free', $product->is_cruelty_free) ? '#F0F2EB' : '#FBF8F2' }};">
                        <input type="checkbox" name="is_cruelty_free" value="1"
                               {{ old('is_cruelty_free', $product->is_cruelty_free) ? 'checked' : '' }}
                               class="mt-1 w-4 h-4" style="accent-color:#8A9680;">
                        <div>
                            <p class="text-sm font-semibold" style="color:#2E2A26;">🐰 Cruelty-free</p>
                            <p class="text-xs mt-0.5" style="color:#6B6157;">Sin pruebas en animales</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                           style="border-color:{{ old('is_vegan', $product->is_vegan) ? '#A8B29A' : '#E5DCC9' }};background:{{ old('is_vegan', $product->is_vegan) ? '#F0F2EB' : '#FBF8F2' }};">
                        <input type="checkbox" name="is_vegan" value="1"
                               {{ old('is_vegan', $product->is_vegan) ? 'checked' : '' }}
                               class="mt-1 w-4 h-4" style="accent-color:#8A9680;">
                        <div>
                            <p class="text-sm font-semibold" style="color:#2E2A26;">🌱 Vegano</p>
                            <p class="text-xs mt-0.5" style="color:#6B6157;">Sin ingredientes de origen animal</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ───────────── SEO ───────────── --}}
        @php
            $defaultMetaTitle = $product->meta_title ?: $product->name . ' | Belleza Áurea';
            $defaultMetaDesc  = $product->meta_description
                ?: \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 155);
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
             x-data="seoEditor({
                title: @js(old('meta_title', $defaultMetaTitle)),
                desc:  @js(old('meta_description', $defaultMetaDesc)),
                slug:  @js(old('slug', $product->slug)),
                productName: @js($product->name),
                baseUrl: @js(url('/productos')),
             })">

            <div class="flex items-baseline justify-between mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
                <div>
                    <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">SEO</p>
                    <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Cómo te ve Google</h2>
                    <p class="text-xs mt-1" style="color:#6B6157;">Define cómo se ve tu producto en los resultados de búsqueda. Si dejas vacío, generamos automático del nombre + descripción.</p>
                </div>
            </div>

            {{-- Slug --}}
            <div class="mb-4">
                <label class="block text-xs font-medium mb-1" style="color:#4B4541;">
                    URL (slug) <span class="text-xs ml-2" style="color:#9CA3AF;">— se autogenera del nombre si lo dejas vacío</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-xs" style="color:#9CA3AF;">{{ url('/productos') }}/</span>
                    <input type="text" name="slug" x-model="slug"
                           class="flex-1 rounded-lg px-3 py-2 text-sm font-mono"
                           style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                </div>
            </div>

            @include('admin.partials.seo-panel', [
                'seo'           => $product,
                'ogCol'         => 'og_image_path',
                'twCol'         => 'twitter_image_path',
                'baseUrl'       => url('/productos'),
                'slug'          => old('slug', $product->slug),
                'titleFallback' => $product->name . ' | Belleza Áurea',
                'descFallback'  => \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 155),
            ])
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Guardar cambios
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
