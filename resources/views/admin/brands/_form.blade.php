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
