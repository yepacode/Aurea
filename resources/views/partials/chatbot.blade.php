{{-- =========================================================================
     Chatbot FAQ — Belleza Áurea
     FAB flotante junto al widget de WhatsApp (bottom:118px) que abre un
     popup con vistas: categorías → preguntas → respuesta.
     Escalada final a WhatsApp humano.
     ========================================================================= --}}
@php
    $__cb_wa = \App\Models\ContactPageSetting::whatsappUrl();
@endphp

<style>
    /* ── FAB ─────────────────────────────────────────────────────────── */
    .ba-cb-fab{position:fixed;bottom:118px;right:26px;z-index:9997;width:66px;height:66px;
        display:flex;align-items:center;justify-content:center;border:0;padding:0;cursor:pointer;
        background:radial-gradient(circle at 30% 30%,#EBD298,#BE9A53 65%,#8C6E38);
        color:#2E2A26;border-radius:9999px;
        box-shadow:0 12px 26px -8px rgba(140,110,56,.55),0 4px 10px rgba(46,42,38,.18);
        transition:transform .28s cubic-bezier(.2,.8,.3,1),box-shadow .28s ease;
        animation:ba-cb-float 4.6s ease-in-out infinite;}
    @keyframes ba-cb-float{0%,100%{transform:translateY(0);}50%{transform:translateY(-4px);}}
    .ba-cb-fab:hover{transform:scale(1.06) rotate(-2deg);box-shadow:0 16px 34px -10px rgba(140,110,56,.65),0 6px 14px rgba(46,42,38,.22);}
    .ba-cb-fab svg{width:30px;height:30px;stroke-width:1.6;filter:drop-shadow(0 1px 1px rgba(255,255,255,.4));}
    .ba-cb-fab__badge{position:absolute;top:-4px;right:-4px;min-width:22px;height:22px;padding:0 6px;
        background:#2E2A26;color:#EBD298;border-radius:9999px;
        font:700 12px/22px 'Montserrat',system-ui,sans-serif;text-align:center;
        box-shadow:0 2px 6px rgba(46,42,38,.4);border:2px solid #FBF7EE;
        animation:ba-cb-bounce 1.8s ease-in-out infinite;}
    @keyframes ba-cb-bounce{0%,100%{transform:scale(1);}50%{transform:scale(1.14);}}

    /* ── Popup ───────────────────────────────────────────────────────── */
    .ba-cb-pop{position:fixed;bottom:196px;right:26px;z-index:9998;
        width:min(360px,calc(100vw - 32px));height:min(560px,calc(100vh - 220px));
        background:linear-gradient(180deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.28);border-radius:22px;
        box-shadow:0 40px 90px -30px rgba(46,42,38,.55),0 8px 24px -12px rgba(0,0,0,.12);
        overflow:hidden;transform-origin:bottom right;display:flex;flex-direction:column;
        opacity:0;pointer-events:none;transform:translateY(14px) scale(.96);
        transition:opacity .3s ease,transform .3s cubic-bezier(.2,.8,.3,1);}
    .ba-cb-pop.is-open{opacity:1;pointer-events:auto;transform:translateY(0) scale(1);}

    .ba-cb-pop__hd{padding:16px 18px;flex-shrink:0;
        background:linear-gradient(120deg,#BE9A53 0%,#EBD298 55%,#BE9A53 100%);
        color:#2E2A26;position:relative;overflow:hidden;}
    .ba-cb-pop__hd::before{content:"";position:absolute;inset:0;
        background:radial-gradient(ellipse at 20% 0%,rgba(255,255,255,.35),transparent 60%);
        pointer-events:none;}
    .ba-cb-pop__hd-row{display:flex;align-items:center;gap:12px;position:relative;}
    .ba-cb-pop__hd-logo{width:38px;height:38px;border-radius:9999px;flex-shrink:0;
        background:#2E2A26;color:#EBD298;
        display:flex;align-items:center;justify-content:center;
        font:700 18px/1 'Playfair Display',serif;
        box-shadow:inset 0 0 0 2px rgba(235,210,152,.4);}
    .ba-cb-pop__hd-title{font:600 15px/1.2 'Playfair Display',serif;letter-spacing:.02em;}
    .ba-cb-pop__hd-sub{margin-top:3px;font:500 11px/1.2 'Montserrat',system-ui,sans-serif;
        letter-spacing:.05em;color:rgba(46,42,38,.7);}
    .ba-cb-pop__close{margin-left:auto;background:rgba(46,42,38,.1);border:0;
        color:#2E2A26;cursor:pointer;width:32px;height:32px;border-radius:9999px;
        display:flex;align-items:center;justify-content:center;
        transition:background .2s ease;}
    .ba-cb-pop__close:hover{background:rgba(46,42,38,.2);}

    .ba-cb-pop__search{padding:10px 14px;flex-shrink:0;background:rgba(217,181,109,.06);
        border-bottom:1px solid rgba(217,181,109,.16);}
    .ba-cb-pop__search input{width:100%;padding:9px 12px 9px 34px;border-radius:10px;
        background:#fff;border:1px solid rgba(46,42,38,.1);
        font:400 13px/1.2 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238C6E38' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='11' cy='11' r='7'/><line x1='21' y1='21' x2='16.65' y2='16.65'/></svg>");
        background-repeat:no-repeat;background-position:11px center;background-size:16px;}
    .ba-cb-pop__search input:focus{outline:none;border-color:#BE9A53;box-shadow:0 0 0 3px rgba(217,181,109,.2);}

    .ba-cb-pop__body{flex:1;overflow-y:auto;padding:14px;position:relative;
        scrollbar-width:thin;scrollbar-color:rgba(190,154,83,.4) transparent;}
    .ba-cb-pop__body::-webkit-scrollbar{width:6px;}
    .ba-cb-pop__body::-webkit-scrollbar-thumb{background:rgba(190,154,83,.4);border-radius:3px;}

    /* Vista transitions */
    .ba-cb-view{animation:ba-cb-slide-in .3s cubic-bezier(.2,.8,.3,1);}
    @keyframes ba-cb-slide-in{from{opacity:0;transform:translateX(16px);}to{opacity:1;transform:translateX(0);}}

    /* Grid de categorías */
    .ba-cb-cats{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .ba-cb-cat{display:flex;flex-direction:column;align-items:flex-start;gap:6px;
        padding:14px 12px;background:#fff;border:1px solid rgba(217,181,109,.2);
        border-radius:14px;cursor:pointer;text-align:left;
        transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
        font-family:'Montserrat',system-ui,sans-serif;color:#2E2A26;}
    .ba-cb-cat:hover{transform:translateY(-2px);border-color:#BE9A53;
        box-shadow:0 8px 20px -8px rgba(140,110,56,.35);}
    .ba-cb-cat__emoji{font-size:26px;line-height:1;}
    .ba-cb-cat__name{font:600 13px/1.2 'Montserrat',system-ui,sans-serif;}
    .ba-cb-cat__count{font:400 10.5px/1 'Montserrat',system-ui,sans-serif;color:#8A7A5F;}

    /* Lista de preguntas */
    .ba-cb-back{display:inline-flex;align-items:center;gap:6px;background:none;border:0;
        cursor:pointer;color:#8C6E38;font:600 12px/1 'Montserrat',system-ui,sans-serif;
        letter-spacing:.04em;text-transform:uppercase;padding:0 0 10px;}
    .ba-cb-back:hover{color:#2E2A26;}
    .ba-cb-qs{display:flex;flex-direction:column;gap:6px;}
    .ba-cb-q{display:flex;align-items:center;justify-content:space-between;gap:10px;
        padding:12px 14px;background:#fff;border:1px solid rgba(217,181,109,.18);
        border-radius:12px;cursor:pointer;text-align:left;
        font:500 13px/1.35 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        transition:border-color .2s ease,background .2s ease;}
    .ba-cb-q:hover{border-color:#BE9A53;background:#FEFCF8;}
    .ba-cb-q__arrow{flex-shrink:0;color:#BE9A53;}

    /* Vista respuesta */
    .ba-cb-answer__cat{display:inline-flex;align-items:center;gap:6px;
        padding:4px 10px;border-radius:9999px;background:rgba(217,181,109,.15);
        color:#8C6E38;font:600 10.5px/1 'Montserrat',system-ui,sans-serif;
        letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px;}
    .ba-cb-answer__q{font:600 15px/1.3 'Playfair Display',serif;color:#2E2A26;margin-bottom:12px;}
    .ba-cb-answer__body{background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:14px;
        padding:14px 16px;font:400 13.5px/1.55 'Montserrat',system-ui,sans-serif;
        color:#3B342C;white-space:pre-wrap;}
    .ba-cb-feedback{margin-top:14px;display:flex;align-items:center;gap:8px;justify-content:center;
        font:500 12px 'Montserrat',system-ui,sans-serif;color:#5A4B33;}
    .ba-cb-feedback button{background:#fff;border:1px solid rgba(217,181,109,.3);
        padding:6px 12px;border-radius:9999px;cursor:pointer;
        font:600 12px 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        transition:background .2s ease,transform .2s ease;}
    .ba-cb-feedback button:hover{background:#F8F2E8;transform:translateY(-1px);}
    .ba-cb-feedback button.is-active{background:#BE9A53;color:#fff;border-color:#BE9A53;}

    /* Búsqueda */
    .ba-cb-search-empty{text-align:center;color:#8A7A5F;padding:24px 12px;
        font:400 13px/1.5 'Montserrat',system-ui,sans-serif;}

    /* Footer con escalada WhatsApp */
    .ba-cb-pop__ft{padding:12px 14px;background:#2E2A26;flex-shrink:0;text-align:center;}
    .ba-cb-pop__ft p{color:rgba(251,247,238,.7);font:400 11.5px/1.4 'Montserrat',system-ui,sans-serif;margin-bottom:8px;}
    .ba-cb-pop__ft a{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;
        background:#25D366;color:#fff;border-radius:9999px;text-decoration:none;
        font:600 12.5px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.02em;
        box-shadow:0 6px 14px -4px rgba(37,211,102,.55);
        transition:background .2s ease,transform .2s ease;}
    .ba-cb-pop__ft a:hover{background:#1FBD5A;transform:translateY(-1px);}
    .ba-cb-pop__ft svg{width:14px;height:14px;}

    /* Loading */
    .ba-cb-loading{display:flex;align-items:center;justify-content:center;padding:30px;color:#8A7A5F;
        font:400 12.5px 'Montserrat',system-ui,sans-serif;}
    .ba-cb-loading::before{content:"";width:16px;height:16px;border-radius:9999px;margin-right:10px;
        border:2px solid rgba(190,154,83,.25);border-top-color:#BE9A53;
        animation:ba-cb-spin .8s linear infinite;}
    @keyframes ba-cb-spin{to{transform:rotate(360deg);}}

    @media (prefers-reduced-motion: reduce){
        .ba-cb-fab,.ba-cb-fab__badge{animation:none;}
        .ba-cb-pop{transition:opacity .15s ease;}
        .ba-cb-view{animation:none;}
    }
    @media (max-width:520px){
        .ba-cb-fab{width:58px;height:58px;bottom:100px;right:18px;}
        .ba-cb-fab svg{width:26px;height:26px;}
        .ba-cb-pop{bottom:0;right:0;left:0;top:0;width:100vw;height:100vh;
            border-radius:0;max-width:none;max-height:none;}
    }
</style>

<div x-data="baChatbot()" x-cloak>
    {{-- Popup --}}
    <div class="ba-cb-pop" :class="{ 'is-open': open }" role="dialog" aria-modal="false" aria-label="Chat de ayuda Belleza Áurea"
         x-show="open" x-transition.opacity>
        {{-- Header --}}
        <div class="ba-cb-pop__hd">
            <div class="ba-cb-pop__hd-row">
                <div class="ba-cb-pop__hd-logo" aria-hidden="true">A</div>
                <div>
                    <div class="ba-cb-pop__hd-title">💛 Hola, soy Áurea</div>
                    <div class="ba-cb-pop__hd-sub">¿En qué te ayudo hoy?</div>
                </div>
                <button type="button" class="ba-cb-pop__close" @click="close()" aria-label="Cerrar chat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Buscador (siempre visible arriba) --}}
        <div class="ba-cb-pop__search">
            <input type="search" placeholder="Buscar en preguntas frecuentes..."
                   x-model="searchQuery" @input.debounce.300ms="runSearch()"
                   aria-label="Buscar preguntas frecuentes">
        </div>

        {{-- Body: vistas --}}
        <div class="ba-cb-pop__body">
            {{-- LOADING --}}
            <template x-if="loading">
                <div class="ba-cb-loading">Cargando…</div>
            </template>

            {{-- VISTA: RESULTADOS DE BÚSQUEDA --}}
            <template x-if="!loading && searchQuery.trim().length >= 2">
                <div class="ba-cb-view" :key="'search'">
                    <template x-if="searchResults.length === 0">
                        <div class="ba-cb-search-empty">
                            No encontramos resultados para «<span x-text="searchQuery"></span>».<br>
                            Escríbenos por WhatsApp para atenderte 💛
                        </div>
                    </template>
                    <div class="ba-cb-qs">
                        <template x-for="r in searchResults" :key="r.id">
                            <button type="button" class="ba-cb-q" @click="openAnswer(r)">
                                <span>
                                    <span x-text="(r.category?.emoji || '') + ' ' + r.question"></span>
                                </span>
                                <svg class="ba-cb-q__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- VISTA: CATEGORÍAS --}}
            <template x-if="!loading && searchQuery.trim().length < 2 && view === 'categories'">
                <div class="ba-cb-view" :key="'cats'">
                    <div class="ba-cb-cats">
                        <template x-for="cat in categories" :key="cat.id">
                            <button type="button" class="ba-cb-cat" @click="openCategory(cat)">
                                <span class="ba-cb-cat__emoji" x-text="cat.emoji || '💬'"></span>
                                <span class="ba-cb-cat__name" x-text="cat.name"></span>
                                <span class="ba-cb-cat__count"><span x-text="cat.count"></span> preguntas</span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- VISTA: PREGUNTAS DE UNA CATEGORÍA --}}
            <template x-if="!loading && searchQuery.trim().length < 2 && view === 'questions'">
                <div class="ba-cb-view" :key="'qs-'+(selectedCategory?.slug||'')">
                    <button type="button" class="ba-cb-back" @click="backToCategories()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        <span x-text="(selectedCategory?.emoji || '') + ' ' + (selectedCategory?.name || '')"></span>
                    </button>
                    <div class="ba-cb-qs">
                        <template x-for="q in categoryFaqs" :key="q.id">
                            <button type="button" class="ba-cb-q" @click="openAnswer(q)">
                                <span x-text="q.question"></span>
                                <svg class="ba-cb-q__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- VISTA: RESPUESTA --}}
            <template x-if="!loading && searchQuery.trim().length < 2 && view === 'answer'">
                <div class="ba-cb-view" :key="'answer-'+(selectedFaq?.id||'')">
                    <button type="button" class="ba-cb-back" @click="backFromAnswer()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        Volver
                    </button>
                    <template x-if="selectedFaq?.category?.name">
                        <div class="ba-cb-answer__cat">
                            <span x-text="selectedFaq.category.emoji || ''"></span>
                            <span x-text="selectedFaq.category.name"></span>
                        </div>
                    </template>
                    <p class="ba-cb-answer__q" x-text="selectedFaq?.question"></p>
                    <div class="ba-cb-answer__body" x-text="selectedFaq?.answer"></div>
                    <div class="ba-cb-feedback">
                        <span>¿Te sirvió?</span>
                        <button type="button" @click="markHelpful(true)" :class="{ 'is-active': feedback === 'yes' }">👍 Útil</button>
                        <button type="button" @click="markHelpful(false)" :class="{ 'is-active': feedback === 'no' }">👎 No sirve</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer siempre visible --}}
        <div class="ba-cb-pop__ft">
            <p>¿No encontraste tu respuesta?</p>
            <a href="{{ $__cb_wa }}" target="_blank" rel="noopener" @click="close()">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/></svg>
                Hablar con humana
            </a>
        </div>
    </div>

    {{-- FAB --}}
    <button type="button" class="ba-cb-fab" @click="toggle()"
            :aria-expanded="open" aria-controls="ba-cb-pop"
            aria-label="Abrir chat de ayuda">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
        <span class="ba-cb-fab__badge" x-show="showBadge" x-cloak>!</span>
    </button>
</div>

<script>
window.baChatbot = function () {
    return {
        open: false,
        loading: false,
        view: 'categories',           // 'categories' | 'questions' | 'answer'
        categories: [],
        categoryFaqs: [],
        selectedCategory: null,
        selectedFaq: null,
        searchQuery: '',
        searchResults: [],
        feedback: null,
        showBadge: false,

        init() {
            try {
                this.showBadge = !sessionStorage.getItem('ba_cb_seen');
                const saved = sessionStorage.getItem('ba_cb_state');
                if (saved) {
                    const s = JSON.parse(saved);
                    this.view = s.view || 'categories';
                    this.selectedCategory = s.selectedCategory || null;
                    this.selectedFaq = s.selectedFaq || null;
                    this.categoryFaqs = s.categoryFaqs || [];
                }
            } catch (e) {}
        },

        persist() {
            try {
                sessionStorage.setItem('ba_cb_state', JSON.stringify({
                    view: this.view,
                    selectedCategory: this.selectedCategory,
                    selectedFaq: this.selectedFaq,
                    categoryFaqs: this.categoryFaqs,
                }));
            } catch (e) {}
        },

        async toggle() {
            this.open ? this.close() : await this.openPanel();
        },

        async openPanel() {
            this.open = true;
            this.showBadge = false;
            try { sessionStorage.setItem('ba_cb_seen', '1'); } catch (e) {}
            if (this.categories.length === 0) {
                await this.loadCategories();
            }
        },

        close() {
            this.open = false;
            this.persist();
        },

        async loadCategories() {
            this.loading = true;
            try {
                const r = await fetch('{{ route('faq.categories.json') }}', { headers: { 'Accept': 'application/json' } });
                const j = await r.json();
                this.categories = j.data || [];
            } catch (e) {
                this.categories = [];
            } finally {
                this.loading = false;
            }
        },

        async openCategory(cat) {
            this.selectedCategory = cat;
            this.view = 'questions';
            this.loading = true;
            try {
                const r = await fetch('/faq/categoria/' + encodeURIComponent(cat.slug), { headers: { 'Accept': 'application/json' } });
                const j = await r.json();
                this.categoryFaqs = (j.faqs || []).map(f => Object.assign({}, f, {
                    category: { name: cat.name, slug: cat.slug, emoji: cat.emoji },
                }));
            } catch (e) {
                this.categoryFaqs = [];
            } finally {
                this.loading = false;
                this.persist();
            }
        },

        backToCategories() {
            this.view = 'categories';
            this.selectedCategory = null;
            this.categoryFaqs = [];
            this.persist();
        },

        openAnswer(faq) {
            this.selectedFaq = faq;
            this.view = 'answer';
            this.feedback = null;
            this.searchQuery = '';
            this.searchResults = [];
            this.persist();
            this.trackView(faq.id);
        },

        backFromAnswer() {
            this.selectedFaq = null;
            this.feedback = null;
            this.view = this.selectedCategory ? 'questions' : 'categories';
            this.persist();
        },

        async runSearch() {
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.searchResults = [];
                return;
            }
            try {
                const r = await fetch('{{ route('faq.search') }}?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                });
                const j = await r.json();
                this.searchResults = j.data || [];
            } catch (e) {
                this.searchResults = [];
            }
        },

        async markHelpful(isHelpful) {
            if (!this.selectedFaq) return;
            this.feedback = isHelpful ? 'yes' : 'no';
            if (isHelpful) {
                try {
                    await fetch('/faq/' + this.selectedFaq.id + '/util', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                    });
                } catch (e) {}
            }
        },

        async trackView(id) {
            try {
                await fetch('/faq/' + id + '/view', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
            } catch (e) {}
        },
    };
};
</script>
