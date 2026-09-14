@extends('layouts.admin')

@section('title', 'Hero del home')
@section('page_title', 'Hero del home')

@push('head')
<style>
    /* ─────────── Form áureo unificado ─────────── */
    .ba-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
    }
    .ba-card__head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #F0EAE0;
    }
    .ba-card__title {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 600;
        color: #2E2A26;
        margin: 0;
    }
    .ba-card__label {
        font-size: 10px;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: #BE9A53;
        font-weight: 600;
    }
    .ba-card__hint {
        font-size: 12px;
        color: #6B6157;
        margin: 4px 0 0;
        line-height: 1.5;
    }
    .ba-label {
        display: block;
        font-size: 12px;
        font-weight: 500;
        color: #4B4541;
        margin-bottom: 6px;
    }
    .ba-label__req::after {
        content: " *";
        color: #BE9A53;
    }
    .ba-help {
        font-size: 11px;
        color: #9CA3AF;
        margin-top: 4px;
        font-style: italic;
    }
    .ba-input,
    .ba-textarea,
    .ba-select {
        width: 100%;
        background: #FBF8F2;
        border: 1px solid #E5DCC9;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        color: #2E2A26;
        font-family: 'Montserrat', sans-serif;
        transition: border-color .2s, background .2s;
    }
    .ba-input:focus,
    .ba-textarea:focus,
    .ba-select:focus {
        outline: none;
        border-color: #D9B56D;
        background: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(217,181,109,.18);
    }
    .ba-row {
        display: grid;
        gap: 16px;
    }
    .ba-row--2 { grid-template-columns: 1fr 1fr; }
    .ba-row--3 { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 720px) {
        .ba-row--2, .ba-row--3 { grid-template-columns: 1fr; }
    }
    .ba-radio-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 6px;
        padding: 16px 12px;
        border: 1px solid #E5DCC9;
        border-radius: 12px;
        background: #FBF8F2;
        cursor: pointer;
        transition: all .2s;
    }
    .ba-radio-card:hover { border-color: #D9B56D; }
    .ba-radio-card input { display: none; }
    .ba-radio-card.is-active {
        border-color: #D9B56D;
        background: #FBF4E6;
        box-shadow: 0 0 0 2px rgba(217,181,109,.18);
    }
    .ba-radio-card__icon {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        color: #BE9A53;
    }
    .ba-radio-card__label {
        font-size: 13px;
        font-weight: 500;
        color: #2E2A26;
    }
    .ba-toggle {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    .ba-toggle input {
        appearance: none;
        width: 40px;
        height: 22px;
        background: #D1C7BC;
        border-radius: 999px;
        position: relative;
        cursor: pointer;
        transition: background .2s;
    }
    .ba-toggle input:checked { background: #D9B56D; }
    .ba-toggle input::after {
        content: "";
        position: absolute;
        top: 2px; left: 2px;
        width: 18px; height: 18px;
        border-radius: 50%;
        background: #FFFFFF;
        transition: transform .2s;
        box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
    .ba-toggle input:checked::after { transform: translateX(18px); }
    .ba-trust-row {
        display: grid;
        grid-template-columns: 80px 1fr auto;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }
    .ba-sticky-bar {
        position: sticky;
        top: 0;
        z-index: 30;
        background: rgba(247, 243, 237, 0.92);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 14px 0;
        margin: -24px -24px 24px;
        padding-left: 24px;
        padding-right: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid rgba(184,169,153,.2);
    }
    .ba-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .04em;
        text-decoration: none;
        cursor: pointer;
        transition: all .25s;
        border: none;
        font-family: 'Montserrat', sans-serif;
    }
    .ba-btn--primary { background: #D9B56D; color: #2E2A26; }
    .ba-btn--primary:hover { background: #BE9A53; color: #FFFFFF; }
    .ba-btn--ghost { background: transparent; color: #6B6157; }
    .ba-btn--ghost:hover { color: #2E2A26; }
    .ba-btn--danger-text { background: transparent; color: #C97B6B; padding: 6px 10px; font-size: 12px; }
    .ba-btn--danger-text:hover { color: #A65A4D; }

    .ba-media-preview {
        background: #FBF4E6;
        border: 1px dashed #D9B56D;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ba-media-preview__thumb {
        width: 80px; height: 60px;
        background: #FFFFFF;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        color: #BE9A53;
        flex-shrink: 0;
        overflow: hidden;
    }
    .ba-media-preview__thumb img,
    .ba-media-preview__thumb video {
        width: 100%; height: 100%; object-fit: cover;
    }
    .ba-file {
        display: block;
        width: 100%;
        font-size: 12px;
        color: #6B6157;
    }
    .ba-file::file-selector-button {
        background: #FFFFFF;
        color: #BE9A53;
        border: 1px solid #D9B56D;
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        margin-right: 12px;
        transition: all .2s;
    }
    .ba-file::file-selector-button:hover {
        background: #D9B56D;
        color: #FFFFFF;
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.hero.update') }}" method="POST" enctype="multipart/form-data" class="max-w-5xl">
    @csrf
    @method('PUT')

    {{-- ───────────── BARRA STICKY DE GUARDADO ───────────── --}}
    <div class="ba-sticky-bar">
        <div>
            <p class="text-sm font-semibold" style="color:#2E2A26;">Configura el hero de la home</p>
            <p class="text-xs" style="color:#6B6157;">Los cambios se aplican al guardar. Verifica el resultado en
                <a href="{{ url('/') }}" target="_blank" style="color:#BE9A53;text-decoration:underline;">/ (home) ↗</a>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <label class="ba-toggle">
                <input type="checkbox" name="is_active" value="1" {{ ($hero->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm" style="color:#4B4541;">Hero activo</span>
            </label>
            <button type="submit" class="ba-btn ba-btn--primary">
                Guardar cambios
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl" style="background:#FCEFE6;border:1px solid #C97B6B;color:#A65A4D;">
        <p class="font-semibold text-sm mb-2">Hubo errores al guardar:</p>
        <ul class="text-sm list-disc list-inside">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif

    {{-- ───────────── 1. FONDO DEL HERO (panel derecho) ───────────── --}}
    <div class="ba-card" x-data="{ mode: '{{ $hero->media_type ?? 'gradient' }}' }">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 1</span>
                <h2 class="ba-card__title">Fondo del panel derecho</h2>
                <p class="ba-card__hint">Define qué se muestra a la derecha del hero: gradiente cream + logo (default), una imagen, o un video b-roll en loop.</p>
            </div>
        </div>

        {{-- Tipo de fondo --}}
        <div class="ba-row ba-row--3 mb-5">
            <label class="ba-radio-card" :class="mode === 'video' && 'is-active'">
                <input type="radio" name="media_mode" value="video" x-model="mode">
                <div class="ba-radio-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="6" width="14" height="12" rx="2"/><path d="m22 8-6 4 6 4V8Z"/></svg>
                </div>
                <span class="ba-radio-card__label">Video</span>
                <span class="text-xs" style="color:#9CA3AF;">B-roll loop muted</span>
            </label>
            <label class="ba-radio-card" :class="mode === 'image' && 'is-active'">
                <input type="radio" name="media_mode" value="image" x-model="mode">
                <div class="ba-radio-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3-3-9 9"/></svg>
                </div>
                <span class="ba-radio-card__label">Imagen</span>
                <span class="text-xs" style="color:#9CA3AF;">Foto estática</span>
            </label>
            <label class="ba-radio-card" :class="mode === 'gradient' && 'is-active'">
                <input type="radio" name="media_mode" value="gradient" x-model="mode">
                <div class="ba-radio-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><defs><linearGradient id="g1" x1="0" x2="1" y1="0" y2="1"><stop offset="0%" stop-color="#FBF4E6"/><stop offset="100%" stop-color="#E8D1C5"/></linearGradient></defs><rect x="3" y="3" width="18" height="18" rx="3" fill="url(#g1)" stroke="#D9B56D" stroke-width="1"/></svg>
                </div>
                <span class="ba-radio-card__label">Gradiente</span>
                <span class="text-xs" style="color:#9CA3AF;">Cream + logo</span>
            </label>
        </div>

        {{-- Media actual + subir --}}
        <div x-show="mode !== 'gradient'" x-cloak>
            @if($hero->media_path)
            <div class="ba-media-preview mb-3">
                <div class="ba-media-preview__thumb">
                    @php $ext = strtolower(pathinfo($hero->media_path, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($ext, ['mp4','webm','mov']))
                        <video src="{{ asset('storage/'.$hero->media_path) }}" muted loop autoplay playsinline></video>
                    @else
                        <img src="{{ asset('storage/'.$hero->media_path) }}" alt="">
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-xs" style="color:#6B6157;">Actualmente:</p>
                    <p class="text-sm font-medium" style="color:#2E2A26;">{{ basename($hero->media_path) }}</p>
                </div>
                <label class="ba-toggle">
                    <input type="checkbox" name="use_gradient" value="1">
                    <span class="text-xs" style="color:#C97B6B;">Eliminar archivo</span>
                </label>
            </div>
            @endif

            <label class="ba-label">Subir archivo nuevo</label>
            <input type="file" name="media_file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime" class="ba-file">
            <p class="ba-help">
                <strong>Imagen</strong>: JPG/PNG/WebP, máx 8 MB.
                <strong>Video</strong>: MP4/WebM/MOV, máx 100 MB. Recomendado: 1080×1350, 15–20s loop, &lt;10 MB.
                Comprime con <a href="https://clideo.com/compress-video" target="_blank" style="color:#BE9A53;text-decoration:underline;">Clideo</a> o <a href="https://handbrake.fr" target="_blank" style="color:#BE9A53;text-decoration:underline;">HandBrake</a>.
            </p>
        </div>

        {{-- Opacidad del overlay (solo si es imagen/video) --}}
        <div class="mt-5" x-show="mode !== 'gradient'" x-cloak>
            <label class="ba-label">
                Opacidad del overlay
                <span class="text-xs" style="color:#9CA3AF;">— oscurece el fondo para legibilidad del texto si fuera fullbleed</span>
            </label>
            <input type="range" name="overlay_opacity" min="0" max="1" step="0.05"
                   value="{{ $hero->overlay_opacity ?? 0.3 }}"
                   class="w-full accent-yellow-600"
                   oninput="this.nextElementSibling.textContent = parseFloat(this.value).toFixed(2)">
            <span class="text-xs" style="color:#6B6157;">{{ number_format($hero->overlay_opacity ?? 0.3, 2) }}</span>
        </div>
    </div>

    {{-- ───────────── 2. TEXTO PRINCIPAL ───────────── --}}
    <div class="ba-card">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 2</span>
                <h2 class="ba-card__title">Texto principal</h2>
                <p class="ba-card__hint">El eyebrow y las 3 líneas del título grande. La palabra destacada se pinta en italic dorado.</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="ba-label">Eyebrow <span class="text-xs" style="color:#9CA3AF;">(pequeña pill arriba del título)</span></label>
            <input type="text" name="eyebrow_text" value="{{ old('eyebrow_text', $hero->eyebrow_text) }}"
                   class="ba-input" placeholder="ej. Cosmética natural" maxlength="60">
        </div>

        <div class="ba-row ba-row--3 mb-4">
            <div>
                <label class="ba-label ba-label__req">Línea 1 del título</label>
                <input type="text" name="title_line1" value="{{ old('title_line1', $hero->title_line1) }}" class="ba-input" placeholder="Tu ritual de" maxlength="80">
            </div>
            <div>
                <label class="ba-label">Línea 2</label>
                <input type="text" name="title_line2" value="{{ old('title_line2', $hero->title_line2) }}" class="ba-input" placeholder="belleza natural, elegante" maxlength="80">
            </div>
            <div>
                <label class="ba-label">Línea 3</label>
                <input type="text" name="title_line3" value="{{ old('title_line3', $hero->title_line3) }}" class="ba-input" placeholder="y atemporal" maxlength="80">
            </div>
        </div>

        <div>
            <label class="ba-label">Palabra a destacar en dorado italic</label>
            <input type="text" name="title_highlight_word" value="{{ old('title_highlight_word', $hero->title_highlight_word) }}"
                   class="ba-input" placeholder="ej. atemporal" maxlength="40">
            <p class="ba-help">Debe coincidir con UNA palabra que aparezca en alguna de las 3 líneas. Se pintará en cursiva dorada.</p>
        </div>
    </div>

    {{-- ───────────── 3. SUBTÍTULO Y BADGE ───────────── --}}
    <div class="ba-card">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 3</span>
                <h2 class="ba-card__title">Subtítulo y badge promocional</h2>
                <p class="ba-card__hint">Texto descriptivo bajo el título y badge opcional (envío gratis, promo, etc.).</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="ba-label">Subtítulo</label>
            <textarea name="subtitle" rows="2" class="ba-textarea" maxlength="220"
                      placeholder="Skincare, fragancias y rituales premium con ingredientes botánicos.">{{ old('subtitle', $hero->subtitle) }}</textarea>
            <p class="ba-help">1–2 líneas, máx 220 caracteres.</p>
        </div>

        <div>
            <label class="ba-label">Badge promocional <span class="text-xs" style="color:#9CA3AF;">(opcional)</span></label>
            <input type="text" name="badge_text" value="{{ old('badge_text', $hero->badge_text) }}"
                   class="ba-input" placeholder="ej. Envío gratis desde $150.000" maxlength="100">
            <p class="ba-help">Si lo dejas vacío, no aparece. Aparece en una pillbox cream con borde dorado.</p>
        </div>
    </div>

    {{-- ───────────── 4. BOTONES (CTAs) ───────────── --}}
    <div class="ba-card">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 4</span>
                <h2 class="ba-card__title">Botones (call to action)</h2>
                <p class="ba-card__hint">Dos botones bajo el subtítulo. El primario es sólido (negro→dorado al hover). El secundario es outline pill.</p>
            </div>
        </div>

        <div class="ba-row ba-row--2 mb-5">
            <div>
                <label class="ba-label ba-label__req">Botón primario — texto</label>
                <input type="text" name="btn_primary_text" value="{{ old('btn_primary_text', $hero->btn_primary_text) }}"
                       class="ba-input" placeholder="ej. Descubrir productos" maxlength="40">
            </div>
            <div>
                <label class="ba-label ba-label__req">Botón primario — URL</label>
                <input type="text" name="btn_primary_url" value="{{ old('btn_primary_url', $hero->btn_primary_url) }}"
                       class="ba-input" placeholder="/productos">
            </div>
        </div>

        <div class="ba-row ba-row--2">
            <div>
                <label class="ba-label">Botón secundario — texto</label>
                <input type="text" name="btn_secondary_text" value="{{ old('btn_secondary_text', $hero->btn_secondary_text) }}"
                       class="ba-input" placeholder="ej. Hacer mi quiz de piel" maxlength="40">
            </div>
            <div>
                <label class="ba-label">Botón secundario — URL</label>
                <input type="text" name="btn_secondary_url" value="{{ old('btn_secondary_url', $hero->btn_secondary_url) }}"
                       class="ba-input" placeholder="/quiz">
            </div>
        </div>
    </div>

    {{-- ───────────── 5. STATS / MÉTRICAS ───────────── --}}
    <div class="ba-card">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 5</span>
                <h2 class="ba-card__title">Métricas destacadas (stats)</h2>
                <p class="ba-card__hint">Dos números grandes Playfair con una etiqueta debajo. Aparecen al pie del texto del hero.</p>
            </div>
        </div>

        <div class="ba-row ba-row--2 mb-4">
            <div>
                <label class="ba-label">Stat 1 — Número</label>
                <input type="text" name="stat1_number" value="{{ old('stat1_number', $hero->stat1_number) }}"
                       class="ba-input" placeholder="ej. 100%" maxlength="20">
            </div>
            <div>
                <label class="ba-label">Stat 1 — Etiqueta</label>
                <input type="text" name="stat1_label" value="{{ old('stat1_label', $hero->stat1_label) }}"
                       class="ba-input" placeholder="ej. ingredientes naturales" maxlength="60">
            </div>
        </div>

        <div class="ba-row ba-row--2">
            <div>
                <label class="ba-label">Stat 2 — Número</label>
                <input type="text" name="stat2_number" value="{{ old('stat2_number', $hero->stat2_number) }}"
                       class="ba-input" placeholder="ej. 8" maxlength="20">
            </div>
            <div>
                <label class="ba-label">Stat 2 — Etiqueta</label>
                <input type="text" name="stat2_label" value="{{ old('stat2_label', $hero->stat2_label) }}"
                       class="ba-input" placeholder="ej. rituales únicos" maxlength="60">
            </div>
        </div>
        <p class="ba-help mt-3">💡 Tip: si dejas un par vacío, esa stat no se muestra. Buenos ejemplos: "+10 años", "150 marcas", "24h envío", "Cruelty-free".</p>
    </div>

    {{-- ───────────── 6. MARQUEE: TRUST ITEMS ───────────── --}}
    <div class="ba-card" x-data="trustBarRepeater()" x-init="init()">
        <div class="ba-card__head">
            <div>
                <span class="ba-card__label">Sección 6</span>
                <h2 class="ba-card__title">Marquee de confianza</h2>
                <p class="ba-card__hint">Banda que se desliza bajo el hero con tus garantías y promesas. Máx 6 items.</p>
            </div>
            <button type="button" @click="if (items.length < 6) items.push({icon:'✓', text:''})"
                    class="ba-btn ba-btn--primary" :class="items.length >= 6 ? 'opacity-40 pointer-events-none' : ''">
                + Agregar
            </button>
        </div>

        <input type="hidden" name="trust_items_json" :value="JSON.stringify(items)">

        <template x-for="(item, idx) in items" :key="idx">
            <div class="ba-trust-row">
                <input type="text" x-model="item.icon" maxlength="4" placeholder="✓"
                       class="ba-input text-center">
                <input type="text" x-model="item.text" placeholder="ej. Envío gratis desde $150.000"
                       class="ba-input">
                <button type="button" @click="items.splice(idx, 1)" class="ba-btn ba-btn--danger-text">Quitar</button>
            </div>
        </template>

        <template x-if="items.length === 0">
            <p class="text-sm italic" style="color:#9CA3AF;">Sin items. Sin items, el marquee no aparece en el home.</p>
        </template>

        <div class="mt-3 pt-3" style="border-top:1px solid #F0EAE0;">
            <button type="button" @click="if (confirm('¿Restablecer los 3 items áureos por defecto?')) items = JSON.parse(JSON.stringify(defaults))"
                    class="ba-btn ba-btn--ghost text-xs">Restablecer por defecto</button>
        </div>
    </div>

    {{-- Botón final --}}
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ url('/') }}" target="_blank" class="ba-btn ba-btn--ghost">Ver home ↗</a>
        <button type="submit" class="ba-btn ba-btn--primary">
            Guardar todos los cambios
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>
</form>

<script>
    function trustBarRepeater() {
        return {
            items: [],
            defaults: [
                { icon: '✓', text: 'Envío gratis desde $150.000' },
                { icon: '↩', text: '30 días de devolución' },
                { icon: '🌿', text: 'Ingredientes botánicos' },
            ],
            init() {
                const saved = @json($hero->trust_items ?? []);
                if (Array.isArray(saved) && saved.length > 0) {
                    this.items = saved.map(v => typeof v === 'string'
                        ? { icon: '✓', text: v }
                        : { icon: (v && v.icon) || '✓', text: (v && v.text) || '' });
                } else {
                    this.items = JSON.parse(JSON.stringify(this.defaults));
                }
            }
        };
    }
</script>
@endsection
