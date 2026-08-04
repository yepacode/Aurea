@csrf
<div class="max-w-3xl space-y-6">

    @if($errors->any())
    <div class="p-4 rounded-xl" style="background:#FCEFE6;border:1px solid #C97B6B;color:#A65A4D;">
        <p class="font-semibold text-sm mb-2">Revisa estos campos:</p>
        <ul class="text-sm list-disc list-inside">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif

    {{-- Información básica --}}
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #E8DCC6;">
        <div class="flex items-baseline justify-between mb-5 pb-3" style="border-bottom:1px solid #F0EAE0;">
            <div>
                <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Información</p>
                <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Nombre y descripción</h2>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-xs font-medium mb-1" style="color:#4B4541;">Nombre *</label>
                <input type="text" id="name" name="name" required maxlength="80"
                       value="{{ old('name', $category->name ?? '') }}"
                       placeholder="ej. Cremas para peinar, Esmaltes, Mantequillas..."
                       class="w-full rounded-lg px-3 py-2.5 text-sm"
                       style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                <p class="mt-1 text-xs italic" style="color:#9CA3AF;">El slug se genera automáticamente.</p>
            </div>

            <div>
                <label for="description" class="block text-xs font-medium mb-1" style="color:#4B4541;">Descripción</label>
                <textarea id="description" name="description" rows="3" maxlength="1000"
                          placeholder="Texto opcional que aparece bajo el nombre de la categoría (en el home se muestra al hover)."
                          class="w-full rounded-lg px-3 py-2.5 text-sm"
                          style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">{{ old('description', $category->description ?? '') }}</textarea>
                <p class="mt-1 text-xs italic" style="color:#9CA3AF;">110 caracteres ideal para que entre en la card.</p>
            </div>

            <div>
                <label for="sort_order" class="block text-xs font-medium mb-1" style="color:#4B4541;">
                    Orden de aparición
                    <span class="text-xs ml-1" style="color:#9CA3AF;">— las 8 primeras aparecen en el home</span>
                </label>
                <input type="number" id="sort_order" name="sort_order" min="0"
                       value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                       class="w-32 rounded-lg px-3 py-2.5 text-sm"
                       style="background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;">
                <p class="mt-1 text-xs italic" style="color:#9CA3AF;">
                    <strong>1</strong> = aparece primera (card destacada grande del home). Números mayores van más abajo.
                </p>
            </div>
        </div>
    </div>

    {{-- Imagen --}}
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #E8DCC6;">
        <div class="flex items-baseline justify-between mb-5 pb-3" style="border-bottom:1px solid #F0EAE0;">
            <div>
                <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Imagen</p>
                <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Foto de la categoría</h2>
                <p class="text-xs mt-1" style="color:#6B6157;">Se muestra como fondo en las cards del home y en la página del catálogo.</p>
            </div>
        </div>

        @if(isset($category) && $category->image)
        <div class="flex items-center gap-4 mb-4 p-3 rounded-xl" style="background:#FBF4E6;border:1px solid #E8CC92;">
            <img src="{{ asset('storage/'.$category->image) }}" alt="" class="w-24 h-16 object-cover rounded-lg">
            <div class="flex-1">
                <p class="text-sm font-medium" style="color:#2E2A26;">Imagen actual</p>
                <label class="flex items-center gap-2 text-xs mt-1 cursor-pointer" style="color:#C97B6B;">
                    <input type="checkbox" name="remove_image" value="1" class="w-3 h-3">
                    Eliminarla al guardar
                </label>
            </div>
        </div>
        @endif

        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm cursor-pointer" style="color:#6B6157;">
        <p class="mt-2 text-xs" style="color:#9CA3AF;">
            JPG, PNG o WebP · máx 5 MB · <strong>tamaño recomendado: 800×1000</strong> (vertical 4:5 para que se vea premium en las cards del home).
        </p>
        <p class="text-xs mt-1" style="color:#9CA3AF;">
            Si no subes imagen, se muestra un gradiente áureo (gold, sage, blush, bronze rotando según la posición).
        </p>
    </div>

    {{-- Promoción 2x1 --}}
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #E8DCC6;">
        <div class="mb-4 pb-3" style="border-bottom:1px solid #F0EAE0;">
            <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Promoción</p>
            <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">Promo 2×1</h2>
            <p class="text-xs mt-1" style="color:#6B6157;">Si la activas, <strong>todos los productos de esta categoría</strong> entran en 2×1 (paga 1, lleva 2). También puedes activarla producto por producto en cada ficha.</p>
        </div>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="promo_2x1" value="0">
            <input type="checkbox" name="promo_2x1" value="1" {{ old('promo_2x1', $category->promo_2x1 ?? false) ? 'checked' : '' }} class="w-4 h-4" style="accent-color:#D9B56D;">
            <span class="text-sm" style="color:#2E2A26;">Activar 2×1 en toda esta categoría</span>
        </label>
    </div>

    {{-- SEO --}}
    <div class="bg-white rounded-2xl p-6" style="border:1px solid #E8DCC6;">
        <div class="mb-5 pb-3" style="border-bottom:1px solid #F0EAE0;">
            <p class="text-xs font-bold uppercase" style="color:#BE9A53;letter-spacing:.18em;">Posicionamiento</p>
            <h2 class="text-lg font-semibold mt-1" style="font-family:'Playfair Display',serif;color:#2E2A26;">SEO de la categoría</h2>
            <p class="text-xs mt-1" style="color:#6B6157;">Cómo se ve esta categoría en Google y al compartirla en redes. Todo es opcional: si lo dejas vacío se usan valores automáticos.</p>
        </div>

        @php $catSeo = $category ?? new \App\Models\Category; @endphp
        @include('admin.partials.seo-panel', [
            'seo'           => $catSeo,
            'ogCol'         => 'og_image_path',
            'twCol'         => 'twitter_image_path',
            'baseUrl'       => url('/productos'),
            'slug'          => old('slug', $catSeo->slug ?? ''),
            'titleFallback' => ($catSeo->name ?? 'Categoría').' | Belleza Áurea',
            'descFallback'  => 'Descubre '.($catSeo->name ?? 'esta categoría').' en Belleza Áurea: cosmética e insumos de belleza seleccionados.',
        ])
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.categories.index') }}" class="text-sm" style="color:#6B6157;">Cancelar</a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold text-white transition-all"
                style="background:#D9B56D;letter-spacing:.04em;"
                onmouseover="this.style.background='#BE9A53';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='#D9B56D';this.style.transform=''">
            {{ isset($category) ? 'Actualizar categoría' : 'Crear categoría' }}
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>
</div>
