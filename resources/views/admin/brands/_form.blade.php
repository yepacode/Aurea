@csrf
<div class="max-w-3xl space-y-6">
    {{-- Información básica --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Información de la marca</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                   placeholder="ej. La Roche-Posay">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">País de origen</label>
                <input type="text" name="country_origin" value="{{ old('country_origin', $brand->country_origin ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                       placeholder="ej. Francia">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sitio web oficial</label>
                <input type="url" name="website_url" value="{{ old('website_url', $brand->website_url ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                       placeholder="https://...">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción corta
                <span class="text-xs text-gray-400 ml-2">(255 chars · se muestra en cards y al hover)</span>
            </label>
            <input type="text" name="short_description" maxlength="255" value="{{ old('short_description', $brand->short_description ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                   placeholder="ej. Cosmética farmacéutica premium con tecnología termal">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción larga
                <span class="text-xs text-gray-400 ml-2">(SEO en la página de la marca)</span>
            </label>
            <textarea name="long_description" rows="6"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                      placeholder="Historia, valores, líneas principales, etc.">{{ old('long_description', $brand->long_description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Logos --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Imágenes</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo *</label>
                <p class="text-xs text-gray-500 mb-2">Cuadrado o horizontal · SVG, PNG o WebP · máx. 2 MB</p>
                @if(isset($brand) && $brand->logo_path)
                    <img src="{{ $brand->logo_url }}" alt="" class="h-20 mb-2 rounded border border-gray-200 bg-white object-contain p-1">
                @endif
                <input type="file" name="logo" accept="image/*"
                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banner (opcional)</label>
                <p class="text-xs text-gray-500 mb-2">Wide ~1920×600 · para hero de la página · máx. 5 MB</p>
                @if(isset($brand) && $brand->banner_path)
                    <img src="{{ $brand->banner_url }}" alt="" class="h-20 mb-2 rounded border border-gray-200 object-cover w-full">
                @endif
                <input type="file" name="banner" accept="image/*"
                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
            </div>
        </div>
    </div>

    {{-- Estado --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Visibilidad</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="flex items-start gap-2 cursor-pointer p-3 rounded-lg border"
                   style="border-color:#e5e7eb;">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', isset($brand) ? $brand->is_active : true) ? 'checked' : '' }}
                       class="mt-0.5 w-4 h-4" style="accent-color:#D9B56D;">
                <div>
                    <span class="text-sm font-medium">Marca activa</span>
                    <p class="text-xs text-gray-500">Visible en la tienda</p>
                </div>
            </label>
            <label class="flex items-start gap-2 cursor-pointer p-3 rounded-lg border"
                   style="border-color:#e5e7eb;">
                <input type="checkbox" name="is_featured" value="1"
                       {{ old('is_featured', isset($brand) ? $brand->is_featured : true) ? 'checked' : '' }}
                       class="mt-0.5 w-4 h-4" style="accent-color:#D9B56D;">
                <div>
                    <span class="text-sm font-medium">★ Destacar en home</span>
                    <p class="text-xs text-gray-500">Aparece en el carrusel del home</p>
                </div>
            </label>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden de visualización</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $brand->sort_order ?? 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────
         LANDING ENRIQUECIDA — hero, historia, pilares, quote, etc.
         ───────────────────────────────────────────────────────────── --}}
    @php
        $pillarsOld = old('pillars');
        if ($pillarsOld === null) {
            $pillarsOld = isset($brand) && is_array($brand->pillars_json) ? $brand->pillars_json : [];
        }
        // Aseguremos al menos una fila vacía si el usuario no ha ingresado nada
        if (empty($pillarsOld)) {
            $pillarsOld = [['icon' => '', 'title' => '', 'description' => '']];
        }
        $featuredOld = old('featured_products');
        if ($featuredOld === null) {
            $featuredOld = isset($brand) && is_array($brand->featured_products_json) ? $brand->featured_products_json : [];
        }
        $featuredOld = array_map('intval', $featuredOld);
        $brandProductsList = $brandProducts ?? collect();
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5"
         x-data="{ enabled: {{ old('landing_enabled', isset($brand) ? ($brand->landing_enabled ? 'true' : 'false') : 'false') }} }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Landing enriquecida</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Reemplaza el grid simple de <code>/marcas/{slug}</code> por una landing con storytelling, pilares, quote y productos destacados.
                </p>
            </div>
            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                <input type="hidden" name="landing_enabled" value="0">
                <input type="checkbox" name="landing_enabled" value="1"
                       x-model="enabled"
                       class="w-4 h-4" style="accent-color:#D9B56D;">
                <span class="text-sm font-medium">Activar landing</span>
            </label>
        </div>

        <div x-show="enabled" x-cloak class="space-y-6 pt-2">

            {{-- HERO --}}
            <fieldset class="border border-gray-100 rounded-lg p-4">
                <legend class="px-2 text-xs font-semibold uppercase tracking-widest text-[#BE9A53]">Hero</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del hero</label>
                        <p class="text-xs text-gray-500 mb-2">Editorial · ratio recomendado 3:1 (ej. 1800×600) · máx. 5 MB</p>
                        @if(isset($brand) && $brand->hero_image)
                            <img src="{{ $brand->hero_image_url }}" alt="" class="h-24 w-full object-cover mb-2 rounded border border-gray-200">
                        @endif
                        <input type="file" name="hero_image" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título del hero <span class="text-xs text-gray-400 ml-1">(override del nombre)</span></label>
                            <input type="text" name="hero_title" value="{{ old('hero_title', $brand->hero_title ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                   placeholder="ej. Áurea Naturals">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <textarea name="hero_tagline" rows="2"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                      placeholder="Rituales que despiertan tu esencia">{{ old('hero_tagline', $brand->hero_tagline ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color de marca <span class="text-xs text-gray-400 ml-1">(#HEX, acento opcional)</span></label>
                    <div class="flex items-center gap-3">
                        <input type="color"
                               value="{{ old('brand_color', $brand->brand_color ?? '#D9B56D') }}"
                               oninput="this.nextElementSibling.value=this.value"
                               class="h-10 w-14 rounded border border-gray-300 cursor-pointer">
                        <input type="text" name="brand_color"
                               value="{{ old('brand_color', $brand->brand_color ?? '') }}"
                               placeholder="#D9B56D"
                               class="w-40 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <span class="text-xs text-gray-500">Se usa como acento sobre los dorados de Áurea.</span>
                    </div>
                </div>
            </fieldset>

            {{-- HISTORIA --}}
            <fieldset class="border border-gray-100 rounded-lg p-4">
                <legend class="px-2 text-xs font-semibold uppercase tracking-widest text-[#BE9A53]">Historia</legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                            <input type="text" name="story_title"
                                   value="{{ old('story_title', $brand->story_title ?? 'Nuestra historia') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contenido
                                <span class="text-xs text-gray-400 ml-1">(párrafos separados por línea en blanco)</span>
                            </label>
                            <textarea name="story_content" rows="8"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                      placeholder="Nació en 2015 en una pequeña cocina de Madrid…">{{ old('story_content', $brand->story_content ?? '') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen (opcional)</label>
                        @if(isset($brand) && $brand->story_image)
                            <img src="{{ $brand->story_image_url }}" alt="" class="h-32 w-full object-cover mb-2 rounded border border-gray-200">
                        @endif
                        <input type="file" name="story_image" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                    </div>
                </div>
            </fieldset>

            {{-- PILARES --}}
            <fieldset class="border border-gray-100 rounded-lg p-4"
                      x-data="pillarsRepeater({{ \Illuminate\Support\Js::from($pillarsOld) }})">
                <legend class="px-2 text-xs font-semibold uppercase tracking-widest text-[#BE9A53]">Pilares / valores (máx. 4)</legend>

                <template x-for="(p, idx) in items" :key="idx">
                    <div class="grid grid-cols-12 gap-2 mb-3 items-start">
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Icono</label>
                            <input type="text" :name="`pillars[${idx}][icon]`" x-model="p.icon" maxlength="8"
                                   placeholder="🌿"
                                   class="w-full text-center text-xl border border-gray-300 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-xs text-gray-500 mb-1">Título</label>
                            <input type="text" :name="`pillars[${idx}][title]`" x-model="p.title" maxlength="100"
                                   placeholder="Cruelty-free"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div class="col-span-5">
                            <label class="block text-xs text-gray-500 mb-1">Descripción</label>
                            <input type="text" :name="`pillars[${idx}][description]`" x-model="p.description" maxlength="200"
                                   placeholder="Sin pruebas en animales, certificado por Leaping Bunny"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div class="col-span-1 pt-5">
                            <button type="button" @click="remove(idx)"
                                    class="text-red-500 hover:text-red-700 text-sm">
                                &times;
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="add()" x-show="items.length < 4"
                        class="mt-2 text-sm font-medium text-[#BE9A53] hover:text-[#8B7355]">
                    + Añadir pilar
                </button>
            </fieldset>

            {{-- PRODUCTOS DESTACADOS --}}
            <fieldset class="border border-gray-100 rounded-lg p-4">
                <legend class="px-2 text-xs font-semibold uppercase tracking-widest text-[#BE9A53]">Productos destacados (máx. 6)</legend>

                @if($brandProductsList->isEmpty())
                    <p class="text-xs text-gray-500 italic">
                        Aún no hay productos asignados a esta marca. Asigna productos primero y vuelve para elegir los favoritos.
                    </p>
                @else
                    <p class="text-xs text-gray-500 mb-2">Marca los productos que el cliente va a mostrar arriba con el badge <em>"Elegido por la marca ⭐"</em>.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-2">
                        @foreach($brandProductsList as $p)
                            <label class="flex items-center gap-2 border border-gray-200 rounded-md px-3 py-2 hover:bg-yellow-50 cursor-pointer">
                                <input type="checkbox" name="featured_products[]" value="{{ $p->id }}"
                                       {{ in_array($p->id, $featuredOld, true) ? 'checked' : '' }}
                                       class="w-4 h-4" style="accent-color:#D9B56D;">
                                <span class="text-sm">{{ $p->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </fieldset>

            {{-- QUOTE --}}
            <fieldset class="border border-gray-100 rounded-lg p-4">
                <legend class="px-2 text-xs font-semibold uppercase tracking-widest text-[#BE9A53]">Testimonio / quote (opcional)</legend>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frase</label>
                        <textarea name="quote_text" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                  placeholder="Cada gota es un ritual de amor propio.">{{ old('quote_text', $brand->quote_text ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                        <input type="text" name="quote_author"
                               value="{{ old('quote_author', $brand->quote_author ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                               placeholder="María López, fundadora">
                    </div>
                </div>
            </fieldset>

        </div>
    </div>

    {{-- SEO (panel completo reutilizable) --}}
    @php $seoBrand = $brand ?? new \App\Models\Brand; @endphp
    @include('admin.partials.seo-panel', [
        'seo'           => $seoBrand,
        'ogCol'         => 'og_image_path',
        'twCol'         => 'twitter_image_path',
        'baseUrl'       => url('/marcas'),
        'slug'          => old('slug', $seoBrand->slug ?? ''),
        'titleFallback' => ($seoBrand->name ?? 'Marca') . ' | Belleza Áurea',
        'descFallback'  => 'Descripción de la marca…',
    ])

    <div class="flex items-center gap-3">
        <button type="submit" class="px-5 py-2.5 rounded-lg font-semibold text-sm text-white" style="background:#D9B56D;">
            {{ isset($brand) ? 'Actualizar marca' : 'Crear marca' }}
        </button>
        <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pillarsRepeater', (initial) => ({
            items: (Array.isArray(initial) && initial.length)
                ? initial.map(p => ({
                    icon: p.icon || '',
                    title: p.title || '',
                    description: p.description || '',
                }))
                : [{ icon: '', title: '', description: '' }],
            add() {
                if (this.items.length < 4) {
                    this.items.push({ icon: '', title: '', description: '' });
                }
            },
            remove(idx) {
                this.items.splice(idx, 1);
                if (this.items.length === 0) {
                    this.items.push({ icon: '', title: '', description: '' });
                }
            }
        }));
    });
</script>
<style>[x-cloak]{display:none!important;}</style>
@endpush
