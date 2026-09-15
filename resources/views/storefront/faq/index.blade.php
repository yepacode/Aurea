@extends('layouts.app')

@section('title', 'Preguntas frecuentes | Belleza Áurea')
@section('meta_description', 'Respuestas a las preguntas más frecuentes sobre envíos, pagos, devoluciones, productos y programa de mayoristas de Belleza Áurea.')
@section('canonical', route('faq.index'))

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'FAQPage',
    'mainEntity' => $categories->flatMap(function ($cat) {
        return $cat->faqs->map(function ($faq) {
            return [
                '@type' => 'Question',
                'name'  => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq->answer,
                ],
            ];
        });
    })->values(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')

{{-- ══════════════ HERO ══════════════ --}}
<section style="position:relative;overflow:hidden;background:linear-gradient(180deg,#FBF8F2 0%,#F7F3ED 100%);">
    <div aria-hidden="true" style="position:absolute;top:-120px;right:-100px;width:380px;height:380px;border-radius:50%;filter:blur(90px);background:radial-gradient(circle,rgba(217,181,109,0.20),transparent 70%);pointer-events:none;"></div>
    <div aria-hidden="true" style="position:absolute;bottom:-140px;left:-100px;width:420px;height:420px;border-radius:50%;filter:blur(90px);background:radial-gradient(circle,rgba(168,178,154,0.16),transparent 70%);pointer-events:none;"></div>

    <div style="position:relative;max-width:820px;margin:0 auto;padding:clamp(56px,9vw,112px) clamp(24px,5vw,48px);text-align:center;z-index:1;">
        <span style="display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:500;letter-spacing:0.24em;text-transform:uppercase;color:#BE9A53;margin-bottom:22px;">
            <span aria-hidden="true" style="display:block;width:32px;height:1px;background:#D9B56D;"></span>
            Centro de ayuda
            <span aria-hidden="true" style="display:block;width:32px;height:1px;background:#D9B56D;"></span>
        </span>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(34px,5vw,58px);font-weight:500;line-height:1.08;letter-spacing:-0.015em;color:#2E2A26;margin:0 0 22px;">
            Preguntas <em style="font-style:italic;color:#D9B56D;">frecuentes</em>
        </h1>
        <p style="font-family:'Montserrat',system-ui,sans-serif;font-size:clamp(15px,1.5vw,17px);line-height:1.65;color:#5A4B33;max-width:600px;margin:0 auto 30px;">
            Encuentra respuestas rápidas sobre envíos, pagos, devoluciones y todo lo que necesitas saber antes y después de tu compra.
        </p>

        {{-- Buscador --}}
        <div x-data="baFaqSearch()" class="max-w-2xl mx-auto" style="position:relative;">
            <div style="position:relative;">
                <input type="search" x-model="q" @input.debounce.300ms="run()"
                       placeholder="Buscar en preguntas frecuentes..."
                       aria-label="Buscar preguntas frecuentes"
                       style="width:100%;padding:16px 20px 16px 52px;border-radius:14px;background:#fff;border:1px solid rgba(217,181,109,.3);font:400 15px/1 'Montserrat',system-ui,sans-serif;color:#2E2A26;box-shadow:0 6px 20px -10px rgba(140,110,56,.35);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#BE9A53" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     style="position:absolute;left:18px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div x-show="q.trim().length >= 2" x-cloak
                 style="margin-top:10px;background:#fff;border:1px solid rgba(217,181,109,.25);border-radius:14px;box-shadow:0 10px 30px -12px rgba(140,110,56,.35);text-align:left;max-height:340px;overflow-y:auto;">
                <template x-if="results.length === 0">
                    <div style="padding:18px;font:400 13.5px/1.5 'Montserrat',system-ui,sans-serif;color:#8A7A5F;text-align:center;">
                        Sin resultados. Escríbenos por WhatsApp y te ayudamos 💛
                    </div>
                </template>
                <template x-for="r in results" :key="r.id">
                    <a :href="'#faq-' + r.id"
                       style="display:block;padding:12px 16px;border-bottom:1px solid rgba(217,181,109,.12);font:500 13.5px/1.35 'Montserrat',system-ui,sans-serif;color:#2E2A26;text-decoration:none;transition:background .15s ease;"
                       onmouseover="this.style.background='#FBF8F2'" onmouseout="this.style.background='#fff'"
                       @click="q = ''">
                        <span x-text="(r.category?.emoji || '') + ' ' + r.question"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════ GRID DE CATEGORÍAS (rápido) ══════════════ --}}
<section style="padding:60px 24px 30px;">
    <div style="max-width:1080px;margin:0 auto;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat->slug }}"
                   style="display:flex;flex-direction:column;align-items:flex-start;gap:8px;padding:22px 20px;background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);border:1px solid rgba(217,181,109,.2);border-radius:18px;text-decoration:none;transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease;"
                   onmouseover="this.style.transform='translateY(-4px)';this.style.borderColor='#BE9A53';this.style.boxShadow='0 12px 26px -12px rgba(140,110,56,.45)'"
                   onmouseout="this.style.transform='';this.style.borderColor='rgba(217,181,109,.2)';this.style.boxShadow=''">
                    <span style="font-size:32px;line-height:1;">{{ $cat->emoji ?: '💬' }}</span>
                    <span style="font:600 15px/1.2 'Playfair Display',serif;color:#2E2A26;">{{ $cat->name }}</span>
                    <span style="font:400 12px/1 'Montserrat',system-ui,sans-serif;color:#8A7A5F;">{{ $cat->faqs->count() }} preguntas</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════ FAQS POR CATEGORÍA ══════════════ --}}
<section style="padding:40px 24px 80px;">
    <div style="max-width:820px;margin:0 auto;">
        @foreach($categories as $cat)
            <div id="cat-{{ $cat->slug }}" style="margin-bottom:56px;scroll-margin-top:100px;">
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid rgba(217,181,109,.22);">
                    <span style="font-size:32px;line-height:1;">{{ $cat->emoji ?: '💬' }}</span>
                    <h2 style="font:600 26px/1.2 'Playfair Display',serif;color:#2E2A26;letter-spacing:-0.01em;">{{ $cat->name }}</h2>
                </div>
                @foreach($cat->faqs as $faq)
                    <details id="faq-{{ $faq->id }}"
                             style="margin-bottom:10px;background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:14px;padding:16px 20px;transition:border-color .2s ease,box-shadow .2s ease;scroll-margin-top:100px;"
                             onmouseover="this.style.borderColor='#BE9A53'" onmouseout="this.style.borderColor='rgba(217,181,109,.2)'">
                        <summary style="cursor:pointer;font:600 15px/1.4 'Montserrat',system-ui,sans-serif;color:#2E2A26;list-style:none;display:flex;align-items:center;justify-content:space-between;gap:14px;">
                            <span>{{ $faq->question }}</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#BE9A53" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:transform .3s ease;">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </summary>
                        <div style="margin-top:14px;padding-top:14px;border-top:1px solid rgba(217,181,109,.15);font:400 14px/1.65 'Montserrat',system-ui,sans-serif;color:#3B342C;white-space:pre-wrap;">{{ $faq->answer }}</div>
                    </details>
                @endforeach
            </div>
        @endforeach

        {{-- Escalada WhatsApp --}}
        <div style="margin-top:30px;padding:36px 28px;background:linear-gradient(120deg,#2E2A26 0%,#3B342C 100%);border-radius:22px;text-align:center;">
            <h3 style="font:600 22px/1.25 'Playfair Display',serif;color:#EBD298;margin:0 0 10px;">¿No encontraste tu respuesta?</h3>
            <p style="font:400 14px/1.55 'Montserrat',system-ui,sans-serif;color:rgba(251,247,238,.7);margin:0 auto 22px;max-width:440px;">
                Escríbenos por WhatsApp y una asesora humana te atiende personalmente.
            </p>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:10px;padding:14px 26px;background:#25D366;color:#fff;border-radius:9999px;text-decoration:none;font:600 14px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.02em;box-shadow:0 10px 22px -6px rgba(37,211,102,.55);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm5.46 12.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
                Hablar con una asesora
            </a>
        </div>
    </div>
</section>

<script>
window.baFaqSearch = function () {
    return {
        q: '',
        results: [],
        async run() {
            const q = this.q.trim();
            if (q.length < 2) { this.results = []; return; }
            try {
                const r = await fetch('{{ route('faq.search') }}?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                const j = await r.json();
                this.results = j.data || [];
            } catch (e) { this.results = []; }
        },
    };
};
</script>

@endsection
