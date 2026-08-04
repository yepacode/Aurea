{{--
    Panel SEO por-ítem reutilizable (producto, marca, categoría, ritual).
    Paridad total con el editor de páginas (SeoSetting).

    Variables:
      $seo          modelo actual (sus atributos pueden ser null)
      $ogCol        columna de imagen OG del modelo (default 'og_image_path')
      $twCol        columna de imagen Twitter (default 'twitter_image_path')
      $baseUrl      URL base para la vista previa (ej. url('/productos'))
      $slug         slug actual (para el breadcrumb del preview)
      $titleFallback  texto que se muestra si no hay meta título
      $descFallback   texto que se muestra si no hay meta descripción

    Los name= de los inputs coinciden con App\Http\Controllers\Concerns\HandlesSeoInput.
--}}
@php
    $ogCol = $ogCol ?? 'og_image_path';
    $twCol = $twCol ?? 'twitter_image_path';
    $baseUrl = $baseUrl ?? url('/');
    $slug = $slug ?? ($seo->slug ?? '');
    $titleFallback = $titleFallback ?? 'Título de la página';
    $descFallback = $descFallback ?? 'Descripción de la página…';
    $ogImg = $seo->{$ogCol} ?? null;
    $twImg = $seo->{$twCol} ?? null;
    $inp = 'width:100%;border-radius:8px;padding:8px 12px;font-size:13px;background:#FBF8F2;border:1px solid #E5DCC9;color:#2E2A26;';
    $lbl = 'display:block;font-size:12px;font-weight:600;color:#4B4541;margin-bottom:4px;';
    $hint = 'margin-top:4px;font-size:11px;font-style:italic;color:#9CA3AF;';
    $card = 'background:#fff;border:1px solid #EFE7D8;border-radius:14px;padding:20px 22px;margin-bottom:20px;';
    $head = 'font-family:\'Playfair Display\',serif;font-size:15px;font-weight:600;color:#2E2A26;margin-bottom:14px;display:flex;align-items:center;gap:8px;';
@endphp

<div x-data="{
        mt: @js(old('meta_title', $seo->meta_title ?? '')),
        md: @js(old('meta_description', $seo->meta_description ?? '')),
     }">

    {{-- Vista previa Google --}}
    <div style="{{ $card }}">
        <p style="{{ $head }}">🔍 Vista previa en Google</p>
        <div style="background:#FBF8F2;border:1px solid #E5DCC9;border-radius:10px;padding:14px 16px;font-family:Arial,sans-serif;">
            <p x-text="mt || @js($titleFallback)" style="font-size:18px;color:#1a0dab;line-height:1.3;margin:0 0 2px;font-weight:400;"></p>
            <p style="font-size:12px;color:#3a7d3a;margin:0 0 3px;">{{ rtrim($baseUrl, '/') }}/{{ $slug ?: 'mi-slug' }}</p>
            <p x-text="md || @js($descFallback)" style="font-size:13px;color:#4d5156;line-height:1.4;margin:0;"></p>
        </div>
    </div>

    {{-- SEO General --}}
    <div style="{{ $card }}">
        <p style="{{ $head }}">⚙️ SEO General</p>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }} display:flex;justify-content:space-between;">
                <span>Meta Title</span>
                <span x-text="mt.length + '/60 caracteres'" :style="{ color: mt.length > 60 ? '#C97B6B' : '#9CA3AF' }" style="font-weight:400;font-size:11px;"></span>
            </label>
            <input type="text" name="meta_title" x-model="mt" maxlength="255" placeholder="Título para motores de búsqueda" style="{{ $inp }}">
            <p style="{{ $hint }}">Recomendado: 50–60 caracteres. Palabra clave + nombre + | Belleza Áurea.</p>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }} display:flex;justify-content:space-between;">
                <span>Meta Description</span>
                <span x-text="md.length + '/160 caracteres'" :style="{ color: md.length > 160 ? '#C97B6B' : '#9CA3AF' }" style="font-weight:400;font-size:11px;"></span>
            </label>
            <textarea name="meta_description" x-model="md" rows="3" maxlength="500" placeholder="Descripción para motores de búsqueda" style="{{ $inp }} resize:vertical;"></textarea>
            <p style="{{ $hint }}">Recomendado: 120–160 caracteres. Beneficio + diferenciador + CTA suave.</p>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords ?? '') }}" placeholder="palabra1, palabra2, palabra3" style="{{ $inp }}">
            <p style="{{ $hint }}">Separadas por coma. Poco impacto en Google, pero útil para otros buscadores.</p>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Palabra clave principal</label>
            <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $seo->focus_keyword ?? '') }}" maxlength="120" placeholder="ej. sérum vitamina C" style="{{ $inp }}">
            <p style="{{ $hint }}">1–3 palabras que mejor describen este ítem. Se incluye en el JSON-LD.</p>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">URL Canónica</label>
            <input type="text" name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url ?? '') }}" placeholder="Dejar vacío para usar la URL automática" style="{{ $inp }}">
            <p style="{{ $hint }}">Solo cambiar si este contenido está duplicado en otra URL.</p>
        </div>

        <div>
            <label style="{{ $lbl }}">Robots</label>
            <div style="display:flex;gap:20px;flex-wrap:wrap;">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#4B4541;cursor:pointer;">
                    <input type="hidden" name="noindex" value="0">
                    <input type="checkbox" name="noindex" value="1" {{ old('noindex', $seo->noindex ?? false) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#C97B6B;">
                    <span>No indexar (<code>noindex</code>)</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#4B4541;cursor:pointer;">
                    <input type="hidden" name="nofollow" value="0">
                    <input type="checkbox" name="nofollow" value="1" {{ old('nofollow', $seo->nofollow ?? false) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#C97B6B;">
                    <span>No seguir enlaces (<code>nofollow</code>)</span>
                </label>
            </div>
            <p style="{{ $hint }}">Por defecto: index, follow. Marca solo para ocultar este ítem de los buscadores.</p>
        </div>
    </div>

    {{-- Open Graph --}}
    <div style="{{ $card }}">
        <p style="{{ $head }}">🔗 Open Graph (Facebook, LinkedIn, WhatsApp)</p>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Tipo (og:type)</label>
            @php $ogType = old('og_type', $seo->og_type ?? ''); @endphp
            <select name="og_type" style="{{ $inp }}">
                <option value="">— automático —</option>
                @foreach(['website'=>'website','product'=>'product','article'=>'article','profile'=>'profile'] as $v=>$t)
                    <option value="{{ $v }}" {{ $ogType === $v ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Título OG</label>
            <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title ?? '') }}" maxlength="255" placeholder="Dejar vacío para usar el Meta Title" style="{{ $inp }}">
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Descripción OG</label>
            <textarea name="og_description" rows="2" maxlength="500" placeholder="Dejar vacío para usar la Meta Description" style="{{ $inp }} resize:vertical;">{{ old('og_description', $seo->og_description ?? '') }}</textarea>
        </div>

        <div>
            <label style="{{ $lbl }}">Imagen OG (1200×630 recomendado)</label>
            @if($ogImg)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;padding:8px;border-radius:8px;background:#FBF4E6;border:1px solid #E8CC92;">
                    <img src="{{ asset('storage/'.$ogImg) }}" alt="" style="height:56px;border-radius:6px;">
                    <span style="font-size:11px;color:#6B6157;">Actual: {{ basename($ogImg) }}</span>
                </div>
            @endif
            <input type="file" name="og_image" accept="image/*" style="font-size:12px;color:#6B6157;">
            <p style="{{ $hint }}">Si vacío, se usa la imagen principal del ítem. Ideal 1200×630 (1.91:1).</p>
        </div>
    </div>

    {{-- Twitter Card --}}
    <div style="{{ $card }}">
        <p style="{{ $head }}">💬 Twitter Card (X)</p>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Tipo de tarjeta</label>
            @php $twCard = old('twitter_card', $seo->twitter_card ?? ''); @endphp
            <select name="twitter_card" style="{{ $inp }}">
                <option value="">— automático (summary_large_image) —</option>
                <option value="summary_large_image" {{ $twCard === 'summary_large_image' ? 'selected' : '' }}>summary_large_image</option>
                <option value="summary" {{ $twCard === 'summary' ? 'selected' : '' }}>summary</option>
            </select>
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Título Twitter</label>
            <input type="text" name="twitter_title" value="{{ old('twitter_title', $seo->twitter_title ?? '') }}" maxlength="255" placeholder="Dejar vacío para usar el Meta Title" style="{{ $inp }}">
        </div>

        <div style="margin-bottom:14px;">
            <label style="{{ $lbl }}">Descripción Twitter</label>
            <textarea name="twitter_description" rows="2" maxlength="500" placeholder="Dejar vacío para usar la Meta Description" style="{{ $inp }} resize:vertical;">{{ old('twitter_description', $seo->twitter_description ?? '') }}</textarea>
        </div>

        <div>
            <label style="{{ $lbl }}">Imagen Twitter</label>
            @if($twImg)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;padding:8px;border-radius:8px;background:#FBF4E6;border:1px solid #E8CC92;">
                    <img src="{{ asset('storage/'.$twImg) }}" alt="" style="height:56px;border-radius:6px;">
                    <span style="font-size:11px;color:#6B6157;">Actual: {{ basename($twImg) }}</span>
                </div>
            @endif
            <input type="file" name="twitter_image" accept="image/*" style="font-size:12px;color:#6B6157;">
            <p style="{{ $hint }}">Si se deja vacío, se usará la imagen OG.</p>
        </div>
    </div>

    {{-- JSON-LD --}}
    <div style="{{ $card }} margin-bottom:0;">
        <p style="{{ $head }}">&lt;/&gt; Schema.org / JSON-LD personalizado</p>
        <label style="{{ $lbl }}">JSON-LD adicional</label>
        <textarea name="custom_schema_markup" rows="6" placeholder='{"@@context": "https://schema.org", "@@type": "...", ...}' style="{{ $inp }} font-family:ui-monospace,monospace;font-size:12px;resize:vertical;">{{ old('custom_schema_markup', $seo->custom_schema_markup ?? '') }}</textarea>
        <p style="{{ $hint }}">Se inyecta en el <code>&lt;head&gt;</code> como <code>&lt;script type="application/ld+json"&gt;</code>. Los schemas automáticos (Product, Article, FAQ) se mantienen.</p>
    </div>
</div>
